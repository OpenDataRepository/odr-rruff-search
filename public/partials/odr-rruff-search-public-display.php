<?php

/**
 * Provide a public-facing view for the plugin
 *
 * The search rows and sort options come from the plugin settings (ODR RRUFF
 * Search in wp-admin), built with the Search and Sort builders.
 *
 * @link       https://opendatarepository.org
 * @since      1.0.0
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/public/partials
 */

$_o = is_array($odr_rruff_search_plugin_options) ? $odr_rruff_search_plugin_options : array();
$search_fields = isset($_o['search_fields']) && is_array($_o['search_fields']) ? array_values($_o['search_fields']) : array();
$sort_fields = isset($_o['sort_fields']) && is_array($_o['sort_fields']) ? array_values($_o['sort_fields']) : array();

$has_chemistry = false;
$has_mineral = false;
foreach ($search_fields as $sf) {
    if ($sf['type'] === 'chemistry')
        $has_chemistry = true;
    if ($sf['type'] === 'rruff_mineral' || $sf['type'] === 'ima_mineral')
        $has_mineral = true;
}

// Results open at <odr_path>/<search_slug>#<odr_path>/search/display/<theme_id>/<search_key>;
// theme id 0 lets ODR use the database's preferred search results theme.
$odr_search_config = array(
    'datatype_id' => isset($_o['datatype_id']) ? $_o['datatype_id'] : '',
    'search_slug' => isset($_o['search_slug']) ? $_o['search_slug'] : '',
    'odr_path'    => Odr_Rruff_Search_Public::ODR_PATH,
    'theme_id'    => 0,
);

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script type="text/javascript">
    var odr_rruff_search_config = <?php echo wp_json_encode($odr_search_config); ?>;
</script>


<form id="rruff-search-form-wrapper" style="<?php echo esc_attr(Odr_Rruff_Search_Appearance::css_vars($_o)); ?>">
<div id="rruff-search-form" class="sarch_form">
<?php if (empty($search_fields) && current_user_can('manage_options')) { ?>
    <div class="pure-u-1">
        <p><em>No search fields are configured yet. Add them under ODR Search in the WordPress admin.</em></p>
    </div>
<?php } ?>
<?php foreach ($search_fields as $sf) {
    $row_class = 'rruff-search-form-section pure-u-1';
    $field_id = (string) $sf['field_id'];
?>
    <?php if ($sf['type'] === 'rruff_mineral' || $sf['type'] === 'ima_mineral') { ?>
    <div class="<?php echo esc_attr($row_class); ?> odr-search-row" data-type="<?php echo esc_attr($sf['type']); ?>" data-field-id="<?php echo esc_attr($field_id); ?>"<?php if ($sf['type'] === 'rruff_mineral') { ?> data-sample-field-id="<?php echo esc_attr($sf['sample_field_id']); ?>"<?php } ?>>
        <div class="section-labels pure-u-1 pure-u-md-7-24 pure-u-xl-7-24">
            <label for="txt_mineral">
                <a href="#ODRMineralList" rel="modal:open" class="AMCSDHelperLink">Mineral</a>
            </label>
        </div>
        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
            <input type="text" id="txt_mineral" name="txt_mineral" value="" class="pure-u-1" />
        </div>
        <input type="hidden" id="mineral_ids" name="mineral_ids" value="">
        <input type="hidden" id="txt_tag_ids" name="txt_tag_ids" value="">
    </div>

    <?php } else if ($sf['type'] === 'chemistry') { ?>
    <div class="<?php echo esc_attr($row_class); ?> odr-search-row" data-type="chemistry" data-field-id="<?php echo esc_attr($field_id); ?>">
        <div class="section-labels pure-u-1 pure-u-md-7-24 pure-u-xl-7-24">
            <a class="chemistry_lookup_link">Chemistry</a>
        </div>
        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
            <div class="pure-u-1">
                <label class="chemistry_labels pure-u-1" for="txt_chemistry_incl">Includes:</label>
                <input class="pure-u-1" type="text" id="txt_chemistry_incl" name="txt_chemistry_incl" value="">
            </div>
            <div class="pure-u-1">
                <label class="chemistry_labels pure-u-1" for="txt_chemistry_excl">Excludes:</label>
                <input class="pure-u-1" type="text" id="txt_chemistry_excl" name="txt_chemistry_excl" value="">
            </div>
        </div>
        <input type="hidden" id="chemistry_incl_txt">
        <input type="hidden" id="chemistry_excl_txt">
    </div>

    <?php } else if ($sf['type'] === 'general') { ?>
    <div class="<?php echo esc_attr($row_class); ?> odr-search-row" data-type="general" data-field-id="gen">
        <div class="section-labels pure-u-1 pure-u-md-7-24 pure-u-xl-7-24">
            <label for="txt_general">General</label>
        </div>
        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
            <input type="text" class="pure-u-1" id="txt_general" name="txt_general" value="">
        </div>
    </div>

    <?php } else {
        $input_id = 'odr_sf_' . $field_id;
        $options = isset($sf['options']) && is_array($sf['options']) ? $sf['options'] : array();
        $multiple = !empty($sf['multiple']);
    ?>
    <div class="<?php echo esc_attr($row_class); ?> odr-search-row" data-type="standard" data-field-id="<?php echo esc_attr($field_id); ?>">
        <div class="section-labels pure-u-1 pure-u-md-7-24 pure-u-xl-7-24">
            <label for="<?php echo esc_attr($input_id); ?>"><?php echo esc_html($sf['label']); ?></label>
        </div>
        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
        <?php if (!empty($options)) { ?>
            <select class="pure-u-1 odr-sf-input" id="<?php echo esc_attr($input_id); ?>"<?php if ($multiple) { ?> multiple size="<?php echo esc_attr(min(max(count($options), 2), 6)); ?>"<?php } ?>>
                <?php if (!$multiple) { ?><option value="">(any)</option><?php } ?>
                <?php foreach ($options as $opt) { ?>
                <option value="<?php echo esc_attr($opt['id']); ?>"><?php echo esc_html($opt['name']); ?></option>
                <?php } ?>
            </select>
        <?php } else { ?>
            <input type="text" class="pure-u-1 odr-sf-input" id="<?php echo esc_attr($input_id); ?>" value="">
        <?php } ?>
        </div>
    </div>

    <?php } ?>
<?php } ?>

<?php if (!empty($sort_fields)) { ?>
    <div class="rruff-search-form-section pure-u-1">
        <div class="section-labels pure-u-1 pure-u-md-7-24 pure-u-xl-7-24">
            <label for="sel_sort">Sort By</label>
        </div>
        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
            <div class="pure-u-10-24 pure-u-md-6-24 pure-u-xl-6-24">
                <select name="sel_sort" id="sel_sort" size="1" class="pure-u-1">
                    <?php foreach ($sort_fields as $sort) { ?>
                    <option value="<?php echo esc_attr($sort['field_id']); ?>"><?php echo esc_html($sort['label']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="pure-u-10-24 pure-u-md-6-24 pure-u-xl-6-24">
                <select name="sel_sort_dir" id="sel_sort_dir" size="1" class="pure-u-1">
                    <option value="asc">asc</option>
                    <option value="desc">desc</option>
                </select>
            </div>
        </div>
    </div>
<?php } ?>

    <div class="rruff-search-form-section pure-u-1">
        <div class="pure-u-1 pure-u-md-7-24 pure-u-xl-7-24">
            <label for="submit"></label>
        </div>

        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
            <input id="rruff-search-form-submit" name="submit" type="button" value="search">&nbsp;
            <input id="reset_sample_search" type="button" name="reset_sample_search" value="reset">&nbsp;
            <i id="rruff-search-help-toggle" class="fa-regular fa-circle-question" style="cursor: pointer; font-size: 1.2em;" title="Search Help"></i>
        </div>
    </div>

    <div id="rruff-search-help" class="rruff-search-form-section pure-u-1" style="display: none;">
        <div class="pure-u-1 pure-u-md-7-24 pure-u-xl-7-24"></div>
        <div class="pure-u-1 pure-u-md-16-24 pure-u-xl-16-24">
            <div style="padding: 10px;">
                <?php echo wp_kses_post(isset($odr_rruff_search_plugin_options['help_text']) ? $odr_rruff_search_plugin_options['help_text'] : ''); ?>
            </div>
        </div>
    </div>
</div>
</form>

<?php if ($has_chemistry) { ?>
<div id="div_periodic_table" style="overflow: visible;">
    <div id="div_periodic_table_contents">
        <table id="rruff-periodic-table">
            <tbody><tr>
                <td><div style="display: block; text-align:center; cursor: pointer;  background:#a0ffa0;" class="periodic_table chem_ele_unselected" id="periodic_table_H">H</div></td>
                <td colspan="16" align="center" id="periodic_table_instructions">Click an element once to include, twice to exclude.</td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_He">He</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Li">Li</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Be">Be</div></td>
                <td colspan="1"></td>
                <td colspan="9">
                    <div style="width: 90%;text-align:center; cursor: pointer;  background:#ffdead;" class="periodic_table chem_ele_unselected" id="periodic_table_clear">Clear Chemistry</div>
                </td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_B">B</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_C">C</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_N">N</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_O">O</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_F">F</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Ne">Ne</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Na">Na</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Mg">Mg</div></td>
                <td colspan="1"></td>
                <td colspan="9">
                    <div style="width: 90%;text-align:center; cursor: pointer;  background:#ffdead;" class="periodic_table chem_ele_unselected" id="periodic_table_all">Exclude&nbsp;all&nbsp;non-selected</div>
                </td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Al">Al</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Si">Si</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_P">P</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_S">S</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_Cl">Cl</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Ar">Ar</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_K">K</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Ca">Ca</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Sc">Sc</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ti">Ti</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_V">V</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Cr">Cr</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Mn">Mn</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Fe">Fe</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Co">Co</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ni">Ni</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Cu">Cu</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Zn">Zn</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Ga">Ga</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Ge">Ge</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_As">As</div></td>
                <td><div class="pt_nonmetal periodic_table chem_ele_unselected" id="periodic_table_Se">Se</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_Br">Br</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Kr">Kr</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Rb">Rb</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Sr">Sr</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Y">Y</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Zr">Zr</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Nb">Nb</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Mo">Mo</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Tc">Tc</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ru">Ru</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Rh">Rh</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Pd">Pd</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ag">Ag</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Cd">Cd</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_In">In</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Sn">Sn</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Sb">Sb</div></td>
                <td><div class="pt_metalloid periodic_table chem_ele_unselected" id="periodic_table_Te">Te</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_I">I</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Xe">Xe</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Cs">Cs</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Ba">Ba</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffb888; font-style: italic;" class="periodic_table chem_ele_unselected" id="periodic_table_lanthanides" title="Shortcut for the lanthanide elements.">Ln</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Hf">Hf</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ta">Ta</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_W">W</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Re">Re</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Os">Os</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Ir">Ir</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Pt">Pt</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Au">Au</div></td>
                <td><div class="pt_transition_metals periodic_table chem_ele_unselected" id="periodic_table_Hg">Hg</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Tl">Tl</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Pb">Pb</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Bi">Bi</div></td>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Po">Po</div></td>
                <td><div class="pt_halides periodic_table chem_ele_unselected" id="periodic_table_At">At</div></td>
                <td><div class="pt_noble_gases periodic_table chem_ele_unselected" id="periodic_table_Rn">Rn</div></td>
            </tr>
            <tr>
                <td><div class="pt_alkali_metals periodic_table chem_ele_unselected" id="periodic_table_Fr">Fr</div></td>
                <td><div class="pt_alkaline_earth_metals periodic_table chem_ele_unselected" id="periodic_table_Ra">Ra</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ff99cc; font-style: italic;" class="periodic_table chem_ele_unselected" id="periodic_table_actinides" title="Shortcut for the actinide elements.">An</div></td>
                <!-- <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Rf" >104<br />Rf</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Db" >105<br />Db</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Sg" >106<br />Sg</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Bh" >107<br />Bh</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Hs" >108<br />Hs</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Mt" >109<br />Mt</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Ds" >110<br />Ds</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Ra" >111<br />Rg</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffc0c0;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uub" >112<br />Uub</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uut" >113<br />Uut</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uuq" >114<br />Uuq</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uup" >115<br />Uup</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffbbdd;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uuh" >116<br />Uuh</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#ffff99;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uus" >117<br />Uus</div></td>
                <td><div style="text-align:center; cursor: pointer;  background:#c0ffff;"    class="periodic_table chem_ele_unselected" id="periodic_table_Uuo">118<br />Uuo</div></td> -->
            </tr>
            <tr>
                <td colspan="2" style="text-align:right; font-size: 10px;">&nbsp;</td>
                <td><div style="text-align:center; cursor: pointer; font-style: italic;" class="periodic_table" id="periodic_table_lanthanides_alt" title="Shortcut for the lanthanide elements.">Ln</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_La">La</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Ce">Ce</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Pr">Pr</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Nd">Nd</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Pm">Pm</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Sm">Sm</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Eu">Eu</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Gd">Gd</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Tb">Tb</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Dy">Dy</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Ho">Ho</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Er">Er</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Tm">Tm</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Yb">Yb</div></td>
                <td><div class="pt_lanthanides periodic_table chem_ele_unselected" id="periodic_table_Lu">Lu</div></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right; font-size: 10px;">&nbsp;</td>
                <td><div style="text-align:center; cursor: pointer; font-style: italic;" class="periodic_table" id="periodic_table_actinides_alt" title="Shortcut for the actinide elements.">An</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Ac">Ac</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Th">Th</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Pa">Pa</div></td>
                <td><div class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_U">U</div></td>
                <!-- <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Np" >Np</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Pu" >94<br />Pu</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Am" >95<br />Am</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Cu" >96<br />Cm</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Bk" >97<br />Bk</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Cf" >98<br />Cf</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Es" >99<br />Es</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Fm" >100<br />Fm</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Md" >101<br />Md</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_No" >102<br />No</div></td>
                <td class="pt_actinides periodic_table chem_ele_unselected" id="periodic_table_Lr" >103<br />Lr</div></td> -->
            </tr>
            </tbody>
        </table>
   </div>
</div>
<?php } ?>

<?php if ($has_mineral) { ?>
<div id="ODRMineralList" class="modal">
    <table>
        <tr>
            <td colspan="4" class="AMCSDMineralAlpha">
                <span class="AMCSDMineralNameLetter">A</span>
                <span class="AMCSDMineralNameLetter">B</span>
                <span class="AMCSDMineralNameLetter">C</span>
                <span class="AMCSDMineralNameLetter">D</span>
                <span class="AMCSDMineralNameLetter">E</span>
                <span class="AMCSDMineralNameLetter">F</span>
                <span class="AMCSDMineralNameLetter">G</span>
                <span class="AMCSDMineralNameLetter">H</span>
                <span class="AMCSDMineralNameLetter">I</span>
                <span class="AMCSDMineralNameLetter">J</span>
                <span class="AMCSDMineralNameLetter">K</span>
                <span class="AMCSDMineralNameLetter">L</span>
                <span class="AMCSDMineralNameLetter">M</span>
                <span class="AMCSDMineralNameLetter">N</span>
                <span class="AMCSDMineralNameLetter">O</span>
                <span class="AMCSDMineralNameLetter">P</span>
                <span class="AMCSDMineralNameLetter">Q</span>
                <span class="AMCSDMineralNameLetter">R</span>
                <span class="AMCSDMineralNameLetter">S</span>
                <span class="AMCSDMineralNameLetter">T</span>
                <span class="AMCSDMineralNameLetter">U</span>
                <span class="AMCSDMineralNameLetter">V</span>
                <span class="AMCSDMineralNameLetter">W</span>
                <span class="AMCSDMineralNameLetter">X</span>
                <span class="AMCSDMineralNameLetter">Y</span>
                <span class="AMCSDMineralNameLetter">Z</span>
                <span class="AMCSDCloseModal"><a href="#close-modal" rel="modal:close">[ close ]</a></span>
                <span class="AMCSDCloseModal" onclick="rruffClearMineralNameList()">[ clear ]</span>
            </td>
        </tr>
        <?php
        try {
            $mineral_names = [];
            $rruff_mineral_names = [];
            include(__DIR__ . '/../../../../data-publisher/web/uploads/IMA/mineral_names.php');
            include(__DIR__ . '/../../../../data-publisher/web/uploads/IMA/mineral_names_update.php');
            include(__DIR__ . '/../../../../data-publisher/web/uploads/IMA/rruff_mineral_names.php');
            include(__DIR__ . '/../../../../data-publisher/web/uploads/IMA/rruff_mineral_names_update.php');
            $count = 0;
            $column_count = 0;
            $current_letter = 'a';
            $mineral_names = array_unique($mineral_names);
            asort($mineral_names, SORT_LOCALE_STRING);
            foreach ($mineral_names as $mineral_id => $mineral_name) {
                // Check if we match current letter
                if(mb_strtolower(substr($mineral_name,0, 1)) !== $current_letter) {
                    // Force count to 4
                    $current_letter =  mb_strtolower(substr($mineral_name,0, 1));
                    $count = 4;
                    $column_count = 0;
                }

                if ($count % 4 === 0) {
                    ?><tr><?php
                }
                ?><td class="AMCSDMineralName <?php
                    if(!isset($rruff_mineral_names[$mineral_id])) {
                        ?> AMCSDNotFound<?php
                    }
                    else {
                        ?> AMCSDFound<?php
                    }
                ?>"><?php print $mineral_name ?></td><?php
                $column_count++;
                if ($column_count === 4) {
                    ?></tr><?php
                    $column_count = 0;
                }
                $count++;
            }
        } catch (Exception $e) {
        }
        ?>
    </table>
</div>
<?php } ?>

<script type="text/javascript">
    // ============================================================
    // ODR RRUFF Search — sessionStorage persistence for form state
    // ------------------------------------------------------------
    // Saves the search form state when the user clicks the search
    // button, and restores it on page load if the saved state is
    // less than one day old. sessionStorage is tab-scoped so each
    // tab keeps an independent search state.
    // ============================================================
    (function ($) {
        var STORAGE_KEY = 'odr_rruff_search_state_v1';
        var TTL_MS = 30 * 24 * 60 * 60 * 1000; // one month

        var MAIN_INPUTS = [
            '#txt_mineral',
            '#txt_tag_ids',
            '#txt_chemistry_incl',
            '#txt_chemistry_excl',
            '#txt_general',
            // The sort dropdowns are part of the search too, so a restored
            // search comes back ordered the way it was run.
            '#sel_sort',
            '#sel_sort_dir'
        ];

        function parseSelectedNames(raw) {
            var out = [];
            if (!raw) return out;
            var re = /"([^"]+)"/g;
            var m;
            while ((m = re.exec(raw)) !== null) out.push(m[1]);
            if (out.length === 0) {
                raw.split(',').forEach(function (part) {
                    var t = part.trim().replace(/^"|"$/g, '');
                    if (t.length) out.push(t);
                });
            }
            return out;
        }

        function syncMineralSelection() {
            var raw = $('#txt_mineral').val() || '';
            var names = parseSelectedNames(raw).map(function (n) { return n.toLowerCase(); });
            $('.AMCSDMineralName').each(function () {
                var label = $(this).html();
                if (label && names.indexOf(String(label).toLowerCase()) !== -1) {
                    $(this).addClass('AMCSDMineralNameSelected');
                } else {
                    $(this).removeClass('AMCSDMineralNameSelected');
                }
            });
        }

        function capturePeriodicTable() {
            var state = {};
            $('.periodic_table').each(function () {
                var id = this.id;
                if (!id) return;
                var classes = [];
                if ($(this).hasClass('included')) classes.push('included');
                if ($(this).hasClass('excluded')) classes.push('excluded');
                if (classes.length) state[id] = classes;
            });
            return state;
        }

        function restorePeriodicTable(state) {
            if (!state) return;
            $('.periodic_table').removeClass('included excluded');
            for (var id in state) {
                if (!state.hasOwnProperty(id)) continue;
                var $el = $('#' + id);
                if (!$el.length) continue;
                state[id].forEach(function (cls) { $el.addClass(cls); });
            }
            if (typeof setChemistryFields === 'function') {
                try { setChemistryFields(); } catch (e) {}
            }
        }

        /**
         * Restores one field. For a <select>, a saved value that no longer
         * exists (the admin changed the configured fields) would leave the
         * dropdown showing nothing, so keep its default instead.
         */
        function setSavedValue($el, value) {
            if (!$el.length) return;
            if ($el.is('select') && !$el.prop('multiple')) {
                var exists = $el.find('option').filter(function () {
                    return this.value === String(value);
                }).length > 0;
                if (!exists) return;
            }
            $el.val(value);
        }

        function captureState() {
            var state = { ts: Date.now(), main: {} };
            MAIN_INPUTS.forEach(function (sel) {
                var $el = $(sel);
                if ($el.length) state.main[sel] = $el.val();
            });
            // Configured standard fields (text inputs and option selects)
            $('.odr-sf-input').each(function () {
                if (this.id) state.main['#' + this.id] = $(this).val();
            });
            state.periodic = capturePeriodicTable();
            return state;
        }

        function applyState(state) {
            if (!state) return;
            MAIN_INPUTS.forEach(function (sel) {
                if (state.main && typeof state.main[sel] !== 'undefined')
                    setSavedValue($(sel), state.main[sel]);
            });
            $('.odr-sf-input').each(function () {
                var sel = '#' + this.id;
                if (this.id && state.main && typeof state.main[sel] !== 'undefined')
                    setSavedValue($(this), state.main[sel]);
            });
            restorePeriodicTable(state.periodic);
            syncMineralSelection();

            // If the restored search used chemistry inclusions/exclusions or
            // had periodic-table elements selected, expand the periodic table
            // panel so the user sees the saved chemistry state immediately.
            var hasChem = ($('#txt_chemistry_incl').val() || '').trim().length > 0
                       || ($('#txt_chemistry_excl').val() || '').trim().length > 0;
            var hasPeriodic = state.periodic && Object.keys(state.periodic).length > 0;
            if (hasChem || hasPeriodic) {
                $('#div_periodic_table').slideDown('100');
            }
        }

        function saveState() {
            try {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify(captureState()));
            } catch (e) {
                if (window.console && console.warn) {
                    console.warn('RRUFF: could not save form state', e);
                }
            }
        }

        function loadState() {
            try {
                var raw = sessionStorage.getItem(STORAGE_KEY);
                if (!raw) return;
                var state = JSON.parse(raw);
                if (!state || !state.ts) return;
                if (Date.now() - state.ts > TTL_MS) {
                    sessionStorage.removeItem(STORAGE_KEY);
                    return;
                }
                applyState(state);
            } catch (e) {
                if (window.console && console.warn) {
                    console.warn('RRUFF: could not load form state', e);
                }
            }
        }

        function wireListeners() {
            // Persist only when the user actually runs a search.
            // The existing search-submit handler does `return false`, which in
            // jQuery stops propagation — so a delegated $(document).on('click')
            // listener would never fire. Use a native capture-phase listener
            // on document instead, which runs BEFORE any target-phase handlers
            // and is unaffected by jQuery's stopPropagation.
            document.addEventListener('click', function (e) {
                var t = e.target;
                if (t && (t.id === 'rruff-search-form-submit'
                    || (t.closest && t.closest('#rruff-search-form-submit')))) {
                    saveState();
                }
                if (t && (t.id === 'reset_sample_search'
                    || (t.closest && t.closest('#reset_sample_search')))) {
                    try { sessionStorage.removeItem(STORAGE_KEY); } catch (err) {}
                    $('#div_periodic_table').slideUp('100');
                    setTimeout(syncMineralSelection, 0);
                }
            }, true);

            // Enter inside any form input also submits via submitSearchForm()
            // — without a click event — so the click listener above misses
            // those submissions and sessionStorage retains the previous
            // saved state. Catch Enter here in capture phase too.
            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' && e.which !== 13) return;
                var t = e.target;
                if (t && t.closest && t.closest('#rruff-search-form-wrapper')) {
                    saveState();
                }
            }, true);

            // Refresh bold-highlight on already-selected entries when the
            // mineral modal opens, after restore, or after manual edits.
            $(document).on('click', 'a[href="#ODRMineralList"]', function () {
                setTimeout(syncMineralSelection, 0);
            });
            $(document).on('input change', '#txt_mineral', syncMineralSelection);
        }

        $(wireListeners);

        // Defer restore to after window.load + a tick so any other
        // window.load handlers (e.g. URL-hash parsing) finish first.
        $(window).on('load', function () {
            setTimeout(loadState, 50);
        });
    })(jQuery);
</script>