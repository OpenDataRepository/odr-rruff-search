/**
 * Search and Sort form builders for the ODR search plugin settings page.
 *
 * Loads the ODR schema for the entered Database UUID and lets the admin add,
 * remove and drag-reorder search rows and sort fields. Each builder keeps its
 * rows as JSON in a hidden input that the Settings API saves.
 *
 * Search row types:
 *   standard       any searchable field; stores the field id (radio options are
 *                  copied so the public form can render its dropdown)
 *   general        ODR general search; stores "gen"
 *   chemistry      an elements field; public form shows includes/excludes and
 *                  the periodic table
 *   rruff_mineral  "Mineral": mineral names from the list, or a RRUFF ID (R######)
 *                  which searches the RRUFF ID field instead
 *   ima_mineral    "Mineral": mineral names only
 */
(function ($) {
	'use strict';

	var cfg = window.odrSearchBuilderConfig || {};
	var SORTABLE_TYPES = cfg.sortableTypes || [];
	// Fieldtypes that can't be searched with a plain value from this form.
	var UNSEARCHABLE_TYPES = ['Markdown', 'Tags', 'XYZ Data', 'DateTime', 'Checkbox'];
	var TEXT_TYPES = ['Short Text', 'Medium Text', 'Long Text', 'Paragraph Text'];
	var MINERAL_TYPES = ['rruff_mineral', 'ima_mineral'];

	var ROW_TYPES = [
		{ key: 'standard', label: 'Standard field', icon: 'dashicons-editor-textcolor' },
		{ key: 'general', label: 'General search', icon: 'dashicons-search' },
		{ key: 'chemistry', label: 'Chemistry search', icon: 'dashicons-screenoptions' },
		{ key: 'rruff_mineral', label: 'RRUFF Mineral', icon: 'dashicons-admin-site-alt3' },
		{ key: 'ima_mineral', label: 'IMA Mineral', icon: 'dashicons-admin-site' }
	];

	var state = {
		search: [],
		sort: [],
		groups: [],   // [{label, single, fields: [{id,name,fieldtype,multiple,options,db,sortable}]}]
		index: {},    // field id -> first field entry seen
		loaded: false,
		uid: 0
	};

	function esc(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;').replace(/</g, '&lt;')
			.replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
	}

	function nextKey() {
		state.uid += 1;
		return 'r' + state.uid;
	}

	// ------------------------------------------------------------------
	// Schema
	// ------------------------------------------------------------------

	// `fields` is either a list of single-key wrappers ([{field_<uuid>: {...}}])
	// or an object keyed by field_<uuid>; return the descriptors either way.
	function fieldDescriptors(fields) {
		var out = [];
		if (!fields || typeof fields !== 'object') return out;
		var entries = Array.isArray(fields) ? fields : Object.keys(fields).map(function (k) { return fields[k]; });
		entries.forEach(function (entry) {
			if (!entry || typeof entry !== 'object') return;
			if (entry.internal_id != null) {
				out.push(entry);
			} else {
				Object.keys(entry).forEach(function (k) {
					var f = entry[k];
					if (f && typeof f === 'object' && f.internal_id != null) out.push(f);
				});
			}
		});
		return out;
	}

	function fieldOptions(f) {
		if (f.fieldtype === 'Boolean') {
			return [{ id: '1', name: 'Yes' }, { id: '0', name: 'No' }];
		}
		var ro = f.radio_options;
		if (!ro || typeof ro !== 'object') return [];
		var list = Array.isArray(ro) ? ro : Object.keys(ro).map(function (k) { return ro[k]; });
		return list.filter(function (o) { return o && o.id != null; })
			.map(function (o) { return { id: String(o.id), name: o.name || '' }; });
	}

	// `single` is true while this database and every ancestor allow only one
	// record per parent, which is what makes its fields usable for sorting.
	function flatten(db, path, single, out) {
		if (!db || typeof db !== 'object') return;
		var label = path ? path + ' › ' + db.name : (db.name || 'Database');
		var fields = fieldDescriptors(db.fields).map(function (f) {
			var type = f.fieldtype || '';
			return {
				id: String(f.internal_id),
				name: f.name || f.field_uuid || '(unnamed)',
				fieldtype: type,
				multiple: /^Multiple /.test(type),
				options: fieldOptions(f),
				db: label,
				sortable: single && SORTABLE_TYPES.indexOf(type) !== -1
			};
		});
		fields.sort(function (a, b) { return a.name.localeCompare(b.name); });
		out.push({ label: label, single: single, fields: fields });
		(db.related_databases || []).forEach(function (child) {
			// Older exports lack multiple_allowed; treat those children as multi-record.
			flatten(child, label, single && Number(child.multiple_allowed) === 0, out);
		});
	}

	function setStatus(msg, isError) {
		var el = document.getElementById(cfg.statusEl);
		if (el) { el.textContent = msg; el.style.color = isError ? '#a33' : '#2b7a3e'; }
	}

	function loadSchema(uuid) {
		state.loaded = false;
		state.groups = [];
		state.index = {};
		if (!uuid) {
			setStatus('Enter a Database UUID to load its fields.', false);
			renderAll();
			return;
		}
		var url = window.location.origin + (cfg.apiPrefix || '') + '/api/' + (cfg.apiVersion || 'v5') +
			'/search/database/' + encodeURIComponent(uuid);
		setStatus('Loading schema…', false);
		fetch(url, { headers: { 'Accept': 'application/json' } })
			.then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
			.then(function (schema) {
				var groups = [];
				flatten(schema, '', true, groups);
				state.groups = groups;
				groups.forEach(function (g) {
					g.fields.forEach(function (f) { if (!state.index[f.id]) state.index[f.id] = f; });
				});
				state.loaded = true;

				var dt = document.getElementById(cfg.datatypeInput);
				if (dt && schema.internal_id != null) dt.value = schema.internal_id;
				var slug = document.getElementById(cfg.slugInput);
				if (slug && schema.search_slug != null) slug.value = schema.search_slug;

				refreshFromSchema();
				var n = Object.keys(state.index).length;
				setStatus('Loaded "' + (schema.name || '') + '" (datatype id ' + schema.internal_id + '): ' +
					n + ' fields across ' + groups.length + ' database(s).', false);
				renderAll();
			})
			.catch(function (e) {
				setStatus('Could not load the schema for that UUID (' + e.message +
					'). The database must be public and the UUID correct.', true);
				renderAll();
			});
	}

	// Bring saved rows up to date with the schema (names, radio options).
	function refreshFromSchema() {
		state.search.forEach(function (row) {
			var f = state.index[row.field_id];
			if (!f || row.type === 'general') return;
			row.label = row.type === 'standard' ? f.name : row.label;
			row.fieldtype = f.fieldtype;
			row.db = f.db;
			if (row.type === 'standard') {
				row.multiple = f.multiple;
				row.options = f.options;
			}
			if (row.type === 'rruff_mineral' && state.index[row.sample_field_id]) {
				row.sample_label = state.index[row.sample_field_id].name;
			}
		});
		state.sort.forEach(function (row) {
			var f = state.index[row.field_id];
			if (f) { row.label = f.name; row.db = f.db; }
		});
		save();
	}

	// ------------------------------------------------------------------
	// Persistence
	// ------------------------------------------------------------------

	function strip(row) {
		var copy = $.extend({}, row);
		delete copy._key;
		return copy;
	}

	function save() {
		$('#' + cfg.searchInput).val(JSON.stringify(state.search.map(strip)));
		$('#' + cfg.sortInput).val(JSON.stringify(state.sort.map(strip)));
	}

	function readSaved(inputId) {
		try {
			var rows = JSON.parse($('#' + inputId).val() || '[]');
			return Array.isArray(rows) ? rows : [];
		} catch (e) {
			return [];
		}
	}

	// ------------------------------------------------------------------
	// Field pickers
	// ------------------------------------------------------------------

	// <select> of fields grouped by database path. `accept(field, group)` filters.
	function fieldSelect(cls, accept, usedIds) {
		var html = '<select class="' + cls + '"><option value="">Choose a field…</option>';
		var count = 0;
		state.groups.forEach(function (g) {
			var opts = g.fields.filter(function (f) { return accept(f, g); });
			if (!opts.length) return;
			html += '<optgroup label="' + esc(g.label) + '">';
			opts.forEach(function (f) {
				var used = usedIds.indexOf(f.id) !== -1;
				html += '<option value="' + esc(f.id) + '"' + (used ? ' disabled' : '') + '>' +
					esc(f.name) + ' (' + esc(f.fieldtype) + ') #' + esc(f.id) + (used ? ' (in use)' : '') + '</option>';
				count++;
			});
			html += '</optgroup>';
		});
		html += '</select>';
		return count ? html : '<em>No eligible fields in this database.</em>';
	}

	function searchableField(f) {
		return UNSEARCHABLE_TYPES.indexOf(f.fieldtype) === -1;
	}

	function textField(f) {
		return TEXT_TYPES.indexOf(f.fieldtype) !== -1;
	}

	function usedSearchIds() {
		var ids = [];
		state.search.forEach(function (r) {
			if (r.type === 'general') return;
			ids.push(String(r.field_id));
			if (r.sample_field_id) ids.push(String(r.sample_field_id));
		});
		return ids;
	}

	function hasType(types) {
		return state.search.some(function (r) { return types.indexOf(r.type) !== -1; });
	}

	function typeDisabledReason(key) {
		if (key === 'general' && hasType(['general'])) return 'The form already has a General search.';
		if (key === 'chemistry' && hasType(['chemistry'])) return 'The form already has a Chemistry search.';
		if (MINERAL_TYPES.indexOf(key) !== -1 && hasType(MINERAL_TYPES)) return 'The form already has a Mineral field; RRUFF Mineral and IMA Mineral can\'t be used together.';
		if (key !== 'general' && !state.loaded) return 'Load a database schema first.';
		return '';
	}

	function openSearchPicker($builder) {
		var $picker = $builder.find('.odr-picker').empty().prop('hidden', false);
		$builder.find('.odr-add-button').prop('disabled', true);

		var html = '<div class="odr-picker-step"><strong>What kind of field?</strong><div class="odr-type-choices">';
		ROW_TYPES.forEach(function (t) {
			var reason = typeDisabledReason(t.key);
			html += '<button type="button" class="button odr-type-choice" data-type="' + t.key + '"' +
				(reason ? ' disabled title="' + esc(reason) + '"' : '') + '>' +
				'<span class="dashicons ' + t.icon + '"></span> ' + esc(t.label) + '</button>';
		});
		html += '</div></div><div class="odr-picker-detail"></div>' +
			'<p class="odr-picker-actions"><button type="button" class="button odr-picker-cancel">Cancel</button></p>';
		$picker.html(html);
	}

	function showSearchDetail($builder, type) {
		var $picker = $builder.find('.odr-picker');
		$picker.find('.odr-type-choice').removeClass('button-primary')
			.filter('[data-type="' + type + '"]').addClass('button-primary');
		var used = usedSearchIds();
		var html = '';

		if (type === 'standard') {
			html = '<p><label>Field: ' + fieldSelect('odr-pick-main', searchableField, used) + '</label></p>';
		} else if (type === 'chemistry') {
			html = '<div class="notice notice-warning inline"><p><strong>Choose an "elements" field.</strong> ' +
				'Chemistry search needs a field holding the elements extracted from the chemical formula ' +
				'(e.g. "Fe O Si"), not the formula itself.</p></div>' +
				'<p><label>Elements field: ' + fieldSelect('odr-pick-main', textField, used) + '</label></p>';
		} else if (type === 'rruff_mineral') {
			html = '<p><label>Mineral name field: ' + fieldSelect('odr-pick-main', textField, used) + '</label></p>' +
				'<p><label>RRUFF ID field: ' + fieldSelect('odr-pick-sample', textField, used) + '</label></p>' +
				'<p class="description">Visitors can pick names from the mineral list or type a RRUFF ID (R######), which searches the RRUFF ID field.</p>';
		} else if (type === 'ima_mineral') {
			html = '<p><label>Mineral name field: ' + fieldSelect('odr-pick-main', textField, used) + '</label></p>' +
				'<p class="description">Visitors can pick names from the mineral list; RRUFF IDs are not accepted.</p>';
		}
		html += '<p><button type="button" class="button button-primary odr-picker-add" data-type="' + type + '">Add to form</button></p>';
		$picker.find('.odr-picker-detail').html(html);
	}

	function closePicker($builder) {
		$builder.find('.odr-picker').prop('hidden', true).empty();
		$builder.find('.odr-add-button').prop('disabled', false);
	}

	function addSearchRow($builder, type) {
		var row;
		if (type === 'general') {
			row = { type: 'general', field_id: 'gen', label: 'General' };
		} else {
			var id = $builder.find('.odr-pick-main').val();
			var f = state.index[id];
			if (!f) { window.alert('Choose a field first.'); return; }
			row = { type: type, field_id: f.id, fieldtype: f.fieldtype, db: f.db };
			if (type === 'standard') {
				row.label = f.name;
				row.multiple = f.multiple;
				row.options = f.options;
			} else if (type === 'chemistry') {
				row.label = 'Chemistry';
			} else {
				row.label = 'Mineral';
			}
			if (type === 'rruff_mineral') {
				var s = state.index[$builder.find('.odr-pick-sample').val()];
				if (!s) { window.alert('Choose the RRUFF ID field.'); return; }
				if (s.id === f.id) { window.alert('The mineral name and RRUFF ID fields must be different.'); return; }
				row.sample_field_id = s.id;
				row.sample_label = s.name;
			}
		}
		row._key = nextKey();
		state.search.push(row);
		closePicker($builder);
		save();
		renderSearch();
	}

	// ------------------------------------------------------------------
	// Rendering
	// ------------------------------------------------------------------

	function mockInput(row) {
		switch (row.type) {
			case 'general':
				return '<input type="text" class="odr-mock-text">';
			case 'chemistry':
				return '<label class="odr-mock-sub">Includes:</label><input type="text" class="odr-mock-text">' +
					'<label class="odr-mock-sub">Excludes:</label><input type="text" class="odr-mock-text">';
			case 'rruff_mineral':
			case 'ima_mineral':
				return '<input type="text" class="odr-mock-text" placeholder="' +
					(row.type === 'rruff_mineral' ? '&quot;Quartz&quot;, &quot;Calcite&quot; or R050125' : '&quot;Quartz&quot;, &quot;Calcite&quot;') + '">';
		}
		var options = row.options || [];
		if (options.length) {
			var html = '<select class="odr-mock-select"' +
				(row.multiple ? ' multiple size="' + Math.min(Math.max(options.length, 2), 5) + '"' : '') + '>';
			if (!row.multiple) html += '<option value="">(any)</option>';
			options.forEach(function (o) { html += '<option>' + esc(o.name) + '</option>'; });
			return html + '</select>';
		}
		return '<input type="text" class="odr-mock-text">';
	}

	function mockLabel(row) {
		if (row.type === 'chemistry') {
			return '<a class="odr-mock-link odr-mock-chem-toggle" title="Opens the periodic table">Chemistry</a>';
		}
		if (MINERAL_TYPES.indexOf(row.type) !== -1) {
			return '<a class="odr-mock-link" title="Opens the mineral name list">Mineral</a>';
		}
		return esc(row.label);
	}

	function rowMeta(row) {
		var label = (ROW_TYPES.filter(function (t) { return t.key === row.type; })[0] || {}).label || row.type;
		if (row.type === 'general') {
			return esc(label) + ' · searches all fields (id "gen")';
		}
		var field = state.index[row.field_id];
		var parts = [esc(label)];
		parts.push((row.type === 'standard' ? '' : 'field: ' + esc(field ? field.name : '') + ' ') +
			'#' + esc(row.field_id) + (row.fieldtype ? ' (' + esc(row.fieldtype) + ')' : ''));
		if (row.sample_field_id) parts.push('RRUFF ID: ' + esc(row.sample_label || '') + ' #' + esc(row.sample_field_id));
		if (row.db) parts.push(esc(row.db));
		return parts.join(' · ');
	}

	function isMissing(id) {
		return state.loaded && id !== 'gen' && !state.index[id];
	}

	function renderSearch() {
		var $b = $('#' + cfg.searchContainer);
		var $list = $b.find('.odr-rows').empty();
		if (!state.search.length) {
			$list.append('<li class="odr-empty">No search fields yet. Use "Add field" to build the form.</li>');
		}
		state.search.forEach(function (row) {
			var missing = isMissing(row.field_id) || (row.sample_field_id && isMissing(row.sample_field_id));
			var $li = $('<li class="odr-row"></li>').attr('data-key', row._key).toggleClass('odr-row-missing', !!missing);
			$li.html(
				'<span class="odr-handle dashicons dashicons-menu" title="Drag to reorder"></span>' +
				'<div class="odr-row-body">' +
					'<div class="odr-mock">' +
						'<div class="odr-mock-label">' + mockLabel(row) + '</div>' +
						'<div class="odr-mock-input">' + mockInput(row) +
							(row.type === 'chemistry' ? '<div class="odr-mock-periodic" hidden>Periodic table: click once to include an element, twice to exclude it.</div>' : '') +
						'</div>' +
					'</div>' +
					'<div class="odr-row-meta">' + rowMeta(row) + (missing ? ' · <strong>not in this database</strong>' : '') + '</div>' +
				'</div>' +
				'<button type="button" class="button-link odr-remove" title="Remove"><span class="dashicons dashicons-no-alt"></span></button>'
			);
			$list.append($li);
		});
	}

	function renderSort() {
		var $b = $('#' + cfg.sortContainer);
		var $list = $b.find('.odr-rows').empty();
		if (!state.sort.length) {
			$list.append('<li class="odr-empty">No sort fields yet. Results use ODR\'s default order.</li>');
		}
		state.sort.forEach(function (row, i) {
			var missing = isMissing(row.field_id);
			var $li = $('<li class="odr-row"></li>').attr('data-key', row._key).toggleClass('odr-row-missing', missing);
			$li.html(
				'<span class="odr-handle dashicons dashicons-menu" title="Drag to reorder"></span>' +
				'<div class="odr-row-body">' +
					'<div class="odr-sort-name">' + esc(row.label) + (i === 0 ? ' <span class="odr-default-badge">default</span>' : '') + '</div>' +
					'<div class="odr-row-meta">#' + esc(row.field_id) + (row.db ? ' · ' + esc(row.db) : '') +
						(missing ? ' · <strong>not in this database</strong>' : '') + '</div>' +
				'</div>' +
				'<button type="button" class="button-link odr-remove" title="Remove"><span class="dashicons dashicons-no-alt"></span></button>'
			);
			$list.append($li);
		});

		var $preview = $b.find('.odr-sort-preview');
		if (state.sort.length) {
			var html = '<div class="odr-mock"><div class="odr-mock-label">Sort By</div><div class="odr-mock-input">' +
				'<select class="odr-mock-select odr-mock-inline">';
			state.sort.forEach(function (r) { html += '<option>' + esc(r.label) + '</option>'; });
			html += '</select> <select class="odr-mock-select odr-mock-inline"><option>asc</option><option>desc</option></select></div></div>';
			$preview.html(html).prop('hidden', false);
		} else {
			$preview.prop('hidden', true).empty();
		}
	}

	function renderAll() {
		renderSearch();
		renderSort();
		$('.odr-add-button').prop('disabled', false);
		$('.odr-picker').prop('hidden', true).empty();
	}

	function buildShell($container, addLabel, withPreview) {
		$container.html(
			(withPreview ? '<div class="odr-sort-preview" hidden></div>' : '') +
			'<ul class="odr-rows"></ul>' +
			'<p><button type="button" class="button odr-add-button"><span class="dashicons dashicons-plus-alt2"></span> ' + esc(addLabel) + '</button></p>' +
			'<div class="odr-picker" hidden></div>'
		);
		$container.find('.odr-rows').sortable({
			handle: '.odr-handle',
			items: '> li.odr-row',
			axis: 'y',
			placeholder: 'odr-row-placeholder',
			forcePlaceholderSize: true
		});
	}

	function reorder(list, $container) {
		var byKey = {};
		list.forEach(function (r) { byKey[r._key] = r; });
		var ordered = [];
		$container.find('.odr-rows > li.odr-row').each(function () {
			var r = byKey[$(this).attr('data-key')];
			if (r) ordered.push(r);
		});
		return ordered;
	}

	// ------------------------------------------------------------------
	// Wiring
	// ------------------------------------------------------------------

	$(function () {
		var $search = $('#' + cfg.searchContainer);
		var $sort = $('#' + cfg.sortContainer);
		if (!$search.length || !$sort.length) return;

		state.search = readSaved(cfg.searchInput).map(function (r) { r._key = nextKey(); return r; });
		state.sort = readSaved(cfg.sortInput).map(function (r) { r._key = nextKey(); return r; });

		buildShell($search, 'Add field', false);
		buildShell($sort, 'Add sort field', true);

		// Search builder
		$search.on('click', '.odr-add-button', function () { openSearchPicker($search); });
		$search.on('click', '.odr-type-choice', function () {
			var type = $(this).attr('data-type');
			if (type === 'general') addSearchRow($search, 'general');
			else showSearchDetail($search, type);
		});
		$search.on('click', '.odr-picker-add', function () { addSearchRow($search, $(this).attr('data-type')); });
		$search.on('click', '.odr-picker-cancel', function () { closePicker($search); });
		$search.on('click', '.odr-remove', function () {
			var key = $(this).closest('li').attr('data-key');
			state.search = state.search.filter(function (r) { return r._key !== key; });
			save();
			renderSearch();
		});
		$search.on('click', '.odr-mock-chem-toggle', function () {
			$(this).closest('.odr-mock').find('.odr-mock-periodic').prop('hidden', function (i, h) { return !h; });
		});
		$search.find('.odr-rows').on('sortupdate', function () {
			state.search = reorder(state.search, $search);
			save();
		});

		// Sort builder
		$sort.on('click', '.odr-add-button', function () {
			var $picker = $sort.find('.odr-picker').empty().prop('hidden', false);
			$(this).prop('disabled', true);
			if (!state.loaded) {
				$picker.html('<p><em>Load a database schema first.</em></p><p><button type="button" class="button odr-picker-cancel">Cancel</button></p>');
				return;
			}
			var used = state.sort.map(function (r) { return String(r.field_id); });
			$picker.html(
				'<p><label>Sort field: ' + fieldSelect('odr-pick-main', function (f) { return f.sortable; }, used) + '</label></p>' +
				'<p class="description">Only single-record databases and sortable fieldtypes are listed.</p>' +
				'<p><button type="button" class="button button-primary odr-picker-add">Add sort field</button> ' +
				'<button type="button" class="button odr-picker-cancel">Cancel</button></p>'
			);
		});
		$sort.on('click', '.odr-picker-add', function () {
			var f = state.index[$sort.find('.odr-pick-main').val()];
			if (!f) { window.alert('Choose a field first.'); return; }
			state.sort.push({ field_id: f.id, label: f.name, db: f.db, _key: nextKey() });
			closePicker($sort);
			save();
			renderSort();
		});
		$sort.on('click', '.odr-picker-cancel', function () { closePicker($sort); });
		$sort.on('click', '.odr-remove', function () {
			var key = $(this).closest('li').attr('data-key');
			state.sort = state.sort.filter(function (r) { return r._key !== key; });
			save();
			renderSort();
		});
		$sort.find('.odr-rows').on('sortupdate', function () {
			state.sort = reorder(state.sort, $sort);
			save();
			renderSort();
		});

		renderAll();

		var uuidInput = document.getElementById(cfg.uuidInput);
		loadSchema(uuidInput ? uuidInput.value.trim() : '');
		if (uuidInput) {
			var t;
			uuidInput.addEventListener('input', function () {
				clearTimeout(t);
				t = setTimeout(function () { loadSchema(uuidInput.value.trim()); }, 500);
			});
		}
	});
})(jQuery);
