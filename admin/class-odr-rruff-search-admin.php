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
			$this->plugin_name,
			'ODR RRUFF Search',
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

	function odrRegisterSettings()
	{
		register_setting(
			'odr_rruff_search_plugin_options',
			'odr_rruff_search_plugin_options'
		);
		add_settings_section(
			'field_settings',
			'Field Settings',
			array($this, 'odr_rruff_search_plugin_section_text'),
			$this->plugin_name
		);

		add_settings_field(
			'odr_rruff_search_help_text',
			'Search Help Text',
			array($this, 'odr_rruff_search_help_text'),
			$this->plugin_name,
			'field_settings'
		);
	}

	function odr_rruff_search_plugin_section_text()
	{
		echo '<p>Configure the RRUFF Search plugin settings</p>';
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

	}

}
