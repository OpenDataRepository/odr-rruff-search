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

        add_action( 'admin_init', array( $this, 'odrRegisterSettings' ) );
        add_action( 'admin_menu', array( $this, 'addPluginAdminMenu' ), 9);

    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Odr_Rruff_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Odr_Rruff_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/odr-rruff-search-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Odr_Rruff_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Odr_Rruff_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/odr-rruff-search-admin.js', array( 'jquery' ), $this->version, false );

        add_action( 'admin_menu', 'odr_search_add_settings_page' );
	}

    public function addPluginAdminMenu() {
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(
            $this->plugin_name,
            'ODR Search',
            'administrator',
            $this->plugin_name,
            array( $this, 'displayPluginAdminDashboard' ),
            'dashicons-chart-area',
            26
        );

        //add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        add_submenu_page(
            $this->plugin_name,
            'ODR Search Settings',
            'Settings',
            'administrator',
            $this->plugin_name.'-settings',
            array( $this, 'displayPluginAdminSettings' )
        );
    }

    public function displayPluginAdminDashboard() {
        // List all records IMA
        // https://beta.rruff.net/odr/api/v1/search/database/0f59b751673686197f49f4e117e9/records/0/0.json
        require_once 'partials/'.$this->plugin_name.'-admin-display.php';
    }

    public function displayPluginAdminSettings() {
        // set this var to be used in the settings-display view
        // IMA List UUID
        // RRUFF Cell Parameters
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
        if(isset($_GET['error_message'])){
            // add_action('admin_notices', array($this,'pluginNameSettingsMessages'));
            // do_action( 'admin_notices', $_GET['error_message'] );
        }
        require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
    }

    public function odr_search_plugin_options_validate( $input ) {
        return $input;
    }

    function odrRegisterSettings() {
        register_setting(
            'odr_search_plugin_options',
            'odr_search_plugin_options',
            'odr_search_plugin_options_validate'
        );
        add_settings_section(
            'field_settings',
            'Field Settings',
            'odr_search_plugin_section_text',
            $this->plugin_name
        );

        /**
         *
         * [
         *   odr-rruff-search-display datatype_id = "738"
         *   general_search = "gen"
         *   chemistry_incl = "7055"
         *   mineral_name = "7052"
         *   sample_id = "7069"
         *   redirect_url = "/odr/rruff_sample#/odr/search/display/2010"
         * ]
         *
         */
        add_settings_field(
            'odr_search_datatype_id',
            'Datatype ID (numeric)',
            array( $this, 'odr_search_datatype_id' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_general_search',
            'General Search (gen)',
            array( $this, 'odr_search_general_search' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_chemistry_incl',
            'Chemistry Incl Field',
            array( $this, 'odr_search_chemistry_incl' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_mineral_name',
            'Mineral Name Field',
            array( $this, 'odr_search_mineral_name' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_rruff_id',
            'RRUFF ID Field',
            array( $this, 'odr_search_rruff_id' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_sample_id',
            'Sample ID Field',
            array( $this, 'odr_search_sample_id' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_redirect_url',
            'Redirect URL',
            array( $this, 'odr_search_redirect_url' ),
            $this->plugin_name,
            'field_settings'
        );
        // default_search = "2229"
        add_settings_field(
            'odr_search_default_search',
            'Default Search Theme ID',
            array( $this, 'odr_search_default_search' ),
            $this->plugin_name,
            'field_settings'
        );
        // search_pictures = "2010"
        add_settings_field(
            'odr_search_search_pictures',
            'Picture Search Theme ID',
            array( $this, 'odr_search_search_pictures' ),
            $this->plugin_name,
            'field_settings'
        );
        // search_spectra = "111"
        add_settings_field(
            'odr_search_search_spectra',
            'Spectra Search Theme ID',
            array( $this, 'odr_search_search_spectra' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_sort_name_field',
            'Sort by Mineral Name Field ID',
            array( $this, 'odr_search_sort_name_field' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_sort_rruff_id_field',
            'Sort by RRUFF ID Field ID',
            array( $this, 'odr_search_sort_rruff_id_field' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_sort_ideal_chemistry_field',
            'Sort by Ideal Chemistry Field ID',
            array( $this, 'odr_search_sort_ideal_chemistry_field' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_sort_source_field',
            'Sort by Source Field ID',
            array( $this, 'odr_search_sort_source_field' ),
            $this->plugin_name,
            'field_settings'
        );
        add_settings_field(
            'odr_search_sort_locality_field',
            'Sort by Locality Field ID',
            array( $this, 'odr_search_sort_locality_field' ),
            $this->plugin_name,
            'field_settings'
        );
    }

    function odr_search_plugin_section_text() {
        echo '<p>Here you can set all the options for using the API</p>';
    }

    function odr_search_datatype_id() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_datatype_id' name='odr_search_plugin_options[datatype_id]' type='text' value='" . esc_attr( $options['datatype_id'] ) . "' />";
    }
    function odr_search_general_search() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_general_search' name='odr_search_plugin_options[general_search]' type='text' value='" . esc_attr( $options['general_search'] ) . "' />";
    }
    function odr_search_chemistry_incl() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_chemistry_incl' name='odr_search_plugin_options[chemistry_incl]' type='text' value='" . esc_attr( $options['chemistry_incl'] ) . "' />";
    }
    function odr_search_mineral_name() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_mineral_name' name='odr_search_plugin_options[mineral_name]' type='text' value='" . esc_attr( $options['mineral_name'] ) . "' />";
    }
    function odr_search_sample_id() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_sample_id' name='odr_search_plugin_options[sample_id]' type='text' value='" . esc_attr( $options['sample_id'] ) . "' />";
    }
    function odr_search_rruff_id() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_rruff_id' name='odr_search_plugin_options[rruff_id]' type='text' value='" . esc_attr( $options['rruff_id'] ) . "' />";
    }
    function odr_search_redirect_url() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_redirect_url' name='odr_search_plugin_options[redirect_url]' type='text' value='" . esc_attr( $options['redirect_url'] ) . "' />";
    }
    function odr_search_default_search() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_default_search' name='odr_search_plugin_options[default_search]' type='text' value='" . esc_attr( $options['default_search'] ) . "' />";
    }
    function odr_search_search_pictures() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_search_pictures' name='odr_search_plugin_options[search_pictures]' type='text' value='" . esc_attr( $options['search_pictures'] ) . "' />";
    }
    function odr_search_search_spectra() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_search_spectra' name='odr_search_plugin_options[search_spectra]' type='text' value='" . esc_attr( $options['search_spectra'] ) . "' />";
    }
    function odr_search_sort_name_field() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_sort_name_field' name='odr_search_plugin_options[sort_name_field]' type='text' value='" . esc_attr( $options['sort_name_field'] ) . "' />";
    }
    function odr_search_sort_rruff_id_field() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_sort_rruff_id_field' name='odr_search_plugin_options[sort_rruff_id_field]' type='text' value='" . esc_attr( $options['sort_rruff_id_field'] ) . "' />";
    }
    function odr_search_sort_ideal_chemistry_field() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_sort_ideal_chemistry_field' name='odr_search_plugin_options[sort_ideal_chemistry_field]' type='text' value='" . esc_attr( $options['sort_ideal_chemistry_field'] ) . "' />";
    }
    function odr_search_sort_source_field() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_sort_source_field' name='odr_search_plugin_options[sort_source_field]' type='text' value='" . esc_attr( $options['sort_source_field'] ) . "' />";
    }
    function odr_search_sort_locality_field() {
        $options = get_option( 'odr_search_plugin_options' );
        echo "<input id='odr_search_sort_locality_field' name='odr_search_plugin_options[sort_locality_field]' type='text' value='" . esc_attr( $options['sort_locality_field'] ) . "' />";
    }

}
