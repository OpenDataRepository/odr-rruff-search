<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://opendatarepository.org
 * @since      1.0.0
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/admin
 * @author     Nathan Stone <nate.stone@opendatarepository.org>
 */
class Odr_Rruff_Search_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_init', array($this, 'odrRegisterSettings'));
		add_action('admin_menu', array($this, 'addPluginAdminMenu'), 9);

	}

	public function addPluginAdminMenu()
	{
		add_menu_page(
			'ODR Search',
			'ODR Search',
			'administrator',
			$this->plugin_name,
			array($this, 'displayPluginAdminSettings'),
			'dashicons-chart-area',
			26
		);
	}

	public function displayPluginAdminSettings()
	{
		require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
	}

	/**
	 * Fieldtypes ODR can sort on (FieldType.canBeSortField = 1).
	 */
	const SORTABLE_FIELDTYPES = array(
		'Integer', 'Decimal', 'Short Text', 'Medium Text', 'Long Text', 'Paragraph Text',
		'DateTime', 'Single Radio', 'Single Select',
	);

	/**
	 * Search-form row types the builder can add.
	 */
	const SEARCH_ROW_TYPES = array( 'standard', 'general', 'chemistry', 'rruff_mineral', 'ima_mineral' );

	function odrRegisterSettings()
	{
		register_setting(
			'odr_rruff_search_plugin_options',
			'odr_rruff_search_plugin_options',
			array( 'sanitize_callback' => array( $this, 'sanitize_options' ) )
		);

		add_settings_section(
			'field_settings',
			'Database',
			array($this, 'odr_rruff_search_plugin_section_text'),
			$this->plugin_name
		);

		$text_fields = array(
			'database_uuid'   => 'Database UUID',
			'datatype_id'     => 'Datatype ID (auto)',
			'search_slug'     => 'Search page (auto)',
		);
		foreach ( $text_fields as $key => $title ) {
			add_settings_field(
				'odr_rruff_search_' . $key,
				$title,
				array( $this, 'odr_rruff_search_' . $key ),
				$this->plugin_name,
				'field_settings'
			);
		}

		add_settings_section(
			'search_form',
			'Search',
			array($this, 'odr_rruff_search_search_section'),
			$this->plugin_name
		);

		add_settings_section(
			'sort_options',
			'Sort',
			array($this, 'odr_rruff_search_sort_section'),
			$this->plugin_name
		);

		add_settings_section(
			'appearance',
			'Appearance',
			array($this, 'odr_rruff_search_appearance_section'),
			$this->plugin_name
		);

		add_settings_section(
			'help_settings',
			'Help',
			null,
			$this->plugin_name
		);
		add_settings_field(
			'odr_rruff_search_help_text',
			'Search Help Text',
			array($this, 'odr_rruff_search_help_text'),
			$this->plugin_name,
			'help_settings'
		);
	}

	/**
	 * Validates the settings form. The two builder lists arrive as JSON strings
	 * and are stored as arrays.
	 */
	function sanitize_options( $input )
	{
		$previous = get_option( 'odr_rruff_search_plugin_options' );
		$previous = is_array( $previous ) ? $previous : array();
		$input = is_array( $input ) ? $input : array();
		$output = array();

		foreach ( array( 'database_uuid', 'datatype_id' ) as $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
		}
		$output['search_slug'] = isset( $input['search_slug'] ) ? preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $input['search_slug'] ) : '';
		$output['help_text'] = isset( $input['help_text'] ) ? wp_kses_post( $input['help_text'] ) : '';
		$output['appearance'] = Odr_Rruff_Search_Appearance::sanitize( $input['appearance'] ?? array() );

		$output['search_fields'] = $this->sanitize_list( $input, $previous, 'search_fields', array( $this, 'sanitize_search_row' ) );
		$output['sort_fields'] = $this->sanitize_list( $input, $previous, 'sort_fields', array( $this, 'sanitize_sort_row' ) );

		// General and Chemistry may appear once; the two mineral types exclude each other.
		$seen = array();
		$rows = array();
		foreach ( $output['search_fields'] as $row ) {
			$group = in_array( $row['type'], array( 'rruff_mineral', 'ima_mineral' ), true ) ? 'mineral' : $row['type'];
			if ( $group !== 'standard' ) {
				if ( isset( $seen[ $group ] ) )
					continue;
				$seen[ $group ] = true;
			}
			$rows[] = $row;
		}
		$output['search_fields'] = $rows;

		return $output;
	}

	private function sanitize_list( $input, $previous, $key, $row_callback )
	{
		if ( !isset( $input[ $key ] ) )
			return array();

		$decoded = is_array( $input[ $key ] ) ? $input[ $key ] : json_decode( (string) $input[ $key ], true );
		if ( !is_array( $decoded ) ) {
			add_settings_error( 'odr_rruff_search_plugin_options', $key, 'Could not read the ' . str_replace( '_', ' ', $key ) . '; the previous values were kept.' );
			return isset( $previous[ $key ] ) && is_array( $previous[ $key ] ) ? $previous[ $key ] : array();
		}

		$rows = array();
		foreach ( $decoded as $row ) {
			$clean = is_array( $row ) ? call_user_func( $row_callback, $row ) : null;
			if ( $clean !== null )
				$rows[] = $clean;
		}
		return $rows;
	}

	function sanitize_search_row( $row )
	{
		$type = isset( $row['type'] ) ? (string) $row['type'] : '';
		if ( !in_array( $type, self::SEARCH_ROW_TYPES, true ) )
			return null;

		if ( $type === 'general' ) {
			return array( 'type' => 'general', 'field_id' => 'gen', 'label' => 'General' );
		}

		$field_id = isset( $row['field_id'] ) ? (string) $row['field_id'] : '';
		if ( !ctype_digit( $field_id ) )
			return null;

		$clean = array(
			'type'      => $type,
			'field_id'  => $field_id,
			'label'     => sanitize_text_field( $row['label'] ?? '' ),
			'fieldtype' => sanitize_text_field( $row['fieldtype'] ?? '' ),
			'db'        => sanitize_text_field( $row['db'] ?? '' ),
		);

		if ( $type === 'rruff_mineral' ) {
			$sample_id = isset( $row['sample_field_id'] ) ? (string) $row['sample_field_id'] : '';
			if ( !ctype_digit( $sample_id ) )
				return null;
			$clean['sample_field_id'] = $sample_id;
			$clean['sample_label'] = sanitize_text_field( $row['sample_label'] ?? '' );
		}

		if ( $type === 'standard' ) {
			$clean['multiple'] = !empty( $row['multiple'] );
			$clean['options'] = array();
			if ( isset( $row['options'] ) && is_array( $row['options'] ) ) {
				foreach ( $row['options'] as $opt ) {
					if ( is_array( $opt ) && isset( $opt['id'] ) && preg_match( '/^-?\d+$/', (string) $opt['id'] ) ) {
						$clean['options'][] = array(
							'id'   => (string) $opt['id'],
							'name' => sanitize_text_field( $opt['name'] ?? '' ),
						);
					}
				}
			}
		}

		return $clean;
	}

	function sanitize_sort_row( $row )
	{
		$field_id = isset( $row['field_id'] ) ? (string) $row['field_id'] : '';
		if ( !ctype_digit( $field_id ) )
			return null;
		return array(
			'field_id' => $field_id,
			'label'    => sanitize_text_field( $row['label'] ?? '' ),
			'db'       => sanitize_text_field( $row['db'] ?? '' ),
		);
	}

	function odr_rruff_search_plugin_section_text()
	{
		echo '<p>Enter the <strong>Database UUID</strong>; its fields become available to the Search and Sort builders below.</p>';
		echo '<p id="odr_rruff_search_schema_status" style="font-style:italic;"></p>';
	}

	function odr_rruff_search_search_section()
	{
		$options = get_option( 'odr_rruff_search_plugin_options' );
		$rows = isset( $options['search_fields'] ) && is_array( $options['search_fields'] ) ? $options['search_fields'] : array();
		echo '<p>Add the fields shown on the search form. Drag rows to reorder them.</p>';
		echo '<div id="odr-search-builder" class="odr-builder"></div>';
		echo "<input type='hidden' id='odr_rruff_search_search_fields' name='odr_rruff_search_plugin_options[search_fields]' value='" . esc_attr( wp_json_encode( $rows ) ) . "' />";
	}

	function odr_rruff_search_sort_section()
	{
		$options = get_option( 'odr_rruff_search_plugin_options' );
		$rows = isset( $options['sort_fields'] ) && is_array( $options['sort_fields'] ) ? $options['sort_fields'] : array();
		echo '<p>Add the fields visitors can sort results by; the first one is the default. Only fields from the main database, or from related databases that allow a single record (all the way up), can be used.</p>';
		echo '<div id="odr-sort-builder" class="odr-builder"></div>';
		echo "<input type='hidden' id='odr_rruff_search_sort_fields' name='odr_rruff_search_plugin_options[sort_fields]' value='" . esc_attr( wp_json_encode( $rows ) ) . "' />";
	}

	function odr_rruff_search_appearance_section()
	{
		$options = get_option( 'odr_rruff_search_plugin_options' );
		$values = Odr_Rruff_Search_Appearance::sanitize( $options['appearance'] ?? array() );
		$defaults = Odr_Rruff_Search_Appearance::defaults();
		$name = 'odr_rruff_search_plugin_options[appearance]';

		echo '<p>Colors and corner rounding for the public search form. The preview updates as you change them.</p>';
		echo '<div id="odr-appearance" class="odr-appearance">';
		echo '<div class="odr-appearance-controls">';
		echo '<fieldset><legend>Colors</legend>';
		foreach ( Odr_Rruff_Search_Appearance::COLORS as $key => $def ) {
			printf(
				'<p class="odr-appearance-row"><label for="odr_appearance_%1$s">%2$s</label><input type="text" id="odr_appearance_%1$s" class="odr-appearance-color" name="%3$s[%1$s]" value="%4$s" data-default-color="%5$s" data-var="%6$s" /></p>',
				esc_attr( $key ), esc_html( $def[0] ), esc_attr( $name ), esc_attr( $values[ $key ] ), esc_attr( $defaults[ $key ] ),
				esc_attr( '--odr-search-' . str_replace( '_', '-', $key ) )
			);
		}
		echo '</fieldset><fieldset><legend>Corner radius</legend>';
		foreach ( Odr_Rruff_Search_Appearance::RADII as $key => $def ) {
			printf(
				'<p class="odr-appearance-row"><label for="odr_appearance_%1$s">%2$s</label><span class="odr-appearance-radius"><input type="range" min="0" max="%7$d" value="%4$d" data-for="odr_appearance_%1$s" aria-hidden="true" tabindex="-1" /><input type="number" id="odr_appearance_%1$s" class="small-text odr-appearance-number" name="%3$s[%1$s]" min="0" max="%7$d" value="%4$d" data-default="%5$d" data-var="%6$s" /> px</span></p>',
				esc_attr( $key ), esc_html( $def[0] ), esc_attr( $name ), (int) $values[ $key ], (int) $defaults[ $key ],
				esc_attr( '--odr-search-' . str_replace( '_', '-', $key ) ), Odr_Rruff_Search_Appearance::MAX_RADIUS
			);
		}
		echo '</fieldset>';
		echo '<p><button type="button" class="button odr-appearance-reset">Reset to defaults</button></p>';
		echo '</div>';

		// Miniature of the public form, styled from the same custom properties.
		echo '<div class="odr-appearance-preview-wrap"><strong>Preview</strong>';
		echo '<div class="odr-appearance-preview" style="' . esc_attr( Odr_Rruff_Search_Appearance::css_vars( $options ) ) . '">';
		echo '<div class="odr-preview-row"><span class="odr-preview-label"><a>Mineral</a></span><input type="text" value="&quot;Quartz&quot;" tabindex="-1" readonly /></div>';
		echo '<div class="odr-preview-row"><span class="odr-preview-label">General</span><input type="text" value="" tabindex="-1" readonly /></div>';
		echo '<div class="odr-preview-row"><span class="odr-preview-label">Sort By</span><select tabindex="-1"><option>Mineral Name</option></select></div>';
		echo '<div class="odr-preview-row"><span class="odr-preview-label"></span><span><button type="button" tabindex="-1">Search</button> <button type="button" tabindex="-1">Reset</button></span></div>';
		echo '</div></div>';
		echo '</div>';
	}

	function odr_rruff_search_help_text()
	{
		$options = get_option('odr_rruff_search_plugin_options');
		$content = isset($options['help_text']) ? $options['help_text'] : '';
		$editor_id = 'odr_rruff_search_help_text';
		$settings = array(
			'textarea_name' => 'odr_rruff_search_plugin_options[help_text]',
			'textarea_rows' => 10,
			'media_buttons' => true,
			'teeny' => false,
			'quicktags' => true,
		);
		wp_editor($content, $editor_id, $settings);
	}

	function odr_rruff_search_database_uuid() {
		$options = get_option( 'odr_rruff_search_plugin_options' );
		echo "<input id='odr_rruff_search_database_uuid' name='odr_rruff_search_plugin_options[database_uuid]' type='text' size='40' value='" . esc_attr( $options['database_uuid'] ?? '' ) . "' placeholder='e.g. ddc5e9ba834ad596cc31aebb1225' />";
		echo "<p class='description'>UUID of the ODR database to search.</p>";
	}
	function odr_rruff_search_datatype_id() {
		$options = get_option( 'odr_rruff_search_plugin_options' );
		echo "<input id='odr_rruff_search_datatype_id' name='odr_rruff_search_plugin_options[datatype_id]' type='text' readonly value='" . esc_attr( $options['datatype_id'] ?? '' ) . "' />";
		echo "<p class='description'>Auto-filled from the Database UUID above.</p>";
	}
	function odr_rruff_search_search_slug() {
		$options = get_option( 'odr_rruff_search_plugin_options' );
		echo "<input id='odr_rruff_search_search_slug' name='odr_rruff_search_plugin_options[search_slug]' type='text' readonly value='" . esc_attr( $options['search_slug'] ?? '' ) . "' />";
		echo "<p class='description'>The database's ODR search page; search results open at <code>" . esc_html( Odr_Rruff_Search_Public::ODR_PATH ) . "/&lt;search page&gt;</code>.</p>";
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix = '' ) {
		if ( $hook_suffix !== 'toplevel_page_' . $this->plugin_name )
			return;

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/odr-rruff-search-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {
		if ( $hook_suffix !== 'toplevel_page_' . $this->plugin_name )
			return;

		wp_enqueue_script( $this->plugin_name . '-builder', plugin_dir_url( __FILE__ ) . 'js/odr-search-builder.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, true );
		wp_localize_script( $this->plugin_name . '-builder', 'odrSearchBuilderConfig', array(
			// The schema URL is window.location.origin + this prefix; Apache aliases
			// the prefix straight to the ODR API.
			'apiPrefix'       => '/odr_rruff',
			'apiVersion'      => 'v5',
			'uuidInput'       => 'odr_rruff_search_database_uuid',
			'datatypeInput'   => 'odr_rruff_search_datatype_id',
			'slugInput'       => 'odr_rruff_search_search_slug',
			'statusEl'        => 'odr_rruff_search_schema_status',
			'searchContainer' => 'odr-search-builder',
			'searchInput'     => 'odr_rruff_search_search_fields',
			'sortContainer'   => 'odr-sort-builder',
			'sortInput'       => 'odr_rruff_search_sort_fields',
			'sortableTypes'   => self::SORTABLE_FIELDTYPES,
		) );

		wp_enqueue_script( $this->plugin_name . '-appearance', plugin_dir_url( __FILE__ ) . 'js/odr-appearance.js', array( 'jquery', 'wp-color-picker' ), $this->version, true );
	}

}
