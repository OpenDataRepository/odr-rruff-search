<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://opendatarepository.org
 * @since      1.0.0
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Odr_Rruff_Search
 * @subpackage Odr_Rruff_Search/public
 * @author     Nathan Stone <nate.stone@opendatarepository.org>
 */
class Odr_Rruff_Search_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

        add_shortcode('odr-rruff-search-display', array($this, 'odr_render_html'));

        // TODO Need to read configuration variables for other pages
        wp_register_style( $this->plugin_name . '-modal-style', plugin_dir_url( __FILE__ ) . 'css/jquery.modal.0.9.1.css', array(), $this->version, 'all' );
        wp_register_style( $this->plugin_name . '-style', plugin_dir_url( __FILE__ ) . 'css/odr-rruff-search-public.css', array(), $this->version, 'all' );

        wp_enqueue_style( $this->plugin_name . '-modal-style');
        wp_enqueue_style( $this->plugin_name . '-style');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

        wp_register_script( $this->plugin_name . '-js', plugin_dir_url( __FILE__ ) . 'js/odr-rruff-search-public.js', array( 'jquery' ), $this->version, false );
        wp_register_script( $this->plugin_name . '-modal-js', plugin_dir_url( __FILE__ ) . 'js/jquery.modal.0.9.1.js', '', $this->version, false );


    }

	/**
     * Include the HTML and output it to the screen
     */
	public function odr_render_html($attributes = [], $content = null, $tag = '') {

        $attributes = array_change_key_case( (array) $attributes, CASE_LOWER );

        // override default attributes with user attributes
        $odr_rruff_search_vars = shortcode_atts(
            array(
                'redirect_url' => '/odr/rruff_sample#/odr/search/display/7',
                datatype_id => "77",
                general_search => "gen",
                chemistry_incl => "199",
                mineral_name => "18",
                sample_id => "34"
            ), $attributes, $tag
        );

        wp_enqueue_script( $this->plugin_name . '-js');
        wp_enqueue_script( $this->plugin_name . '-modal-js');

        ob_start();
        include_once('partials/odr-rruff-search-public-display.php');
        $search_block = ob_get_contents();
        ob_end_clean();
        return $search_block;
    }
}
