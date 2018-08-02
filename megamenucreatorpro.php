<?php
/*
 Plugin Name: Mega Menu Creator Pro
 Description: Easy to use drag & drop WordPress Mega Menu plugin.
 Version:     0.1.0
 Author:      haunv88
 Text Domain: mmcp
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}

if ( !class_exists( 'Mega_Menu_Creator_Pro' ) ) {

	final class Mega_Menu_Creator_Pro {

		public static $plugin_slug;
		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basename;
		public static $version;
		public $settings;
		protected $hook_suffix = 'toplevel_page_megamenucreatorpro';
		
		protected static $_instance = null;
		
		/**
		 * Main Plugin Instance
		 *
		 * Ensures only one instance of plugin is loaded or can be loaded.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Construct.
		 */
		public function __construct() {
			self::$plugin_slug = basename(dirname(__FILE__));
			self::$plugin_prefix = 'mmcp_';
			self::$plugin_basename = plugin_basename(__FILE__);
			self::$plugin_url = plugin_dir_url(self::$plugin_basename);
			self::$plugin_path = trailingslashit(dirname(__FILE__));
			self::$version = '0.1.0';
			$this->define_constants();
			$this->load_classes();
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 * @since  2.3
		 */
		private function init_hooks() {
			add_action( 'wp_loaded', array($this, 'load_controller' ) );
			add_action( 'plugins_loaded', array( $this, 'mmcp_load_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_css' ), 11 );
            add_action( 'admin_print_footer_scripts-'.$this->hook_suffix, array( $this, 'megamenucreatorpro_footer_scripts' ) );
            add_action( 'admin_print_scripts-'.$this->hook_suffix, array( $this, 'megamenucreatorpro_scripts' ) );
            add_action( 'admin_print_styles-'.$this->hook_suffix, array( $this, 'megamenucreatorpro_styles' ) );			
			add_action( 'admin_menu', array( $this, 'wp_admin_menus' ));
			if ($this->is_ajax_action('add-menu-item') && isset($_REQUEST['mmcp-menu'])) {
				add_action('admin_init', array($this, 'load_nav_menus'));
			}
		}

        public function megamenucreatorpro_footer_scripts( $hook ) {
            do_action( 'admin_footer-widgets.php' );
        }		

        /**
         * Added admin scripts, to support media script. Supporting form wp-4.8
         */
        public function megamenucreatorpro_scripts( $hook ) {
            do_action( 'admin_print_scripts-widgets.php' );
        }
        /**
         * Added admin style, to support media style. Supporting form wp-4.8
         */
        public function megamenucreatorpro_styles( $hook ) {
            do_action( 'admin_print_styles-widgets.php' );
        }		

		public function load_controller() {
	 		if (is_admin()) {
				include_once(ABSPATH . 'wp-admin/includes/nav-menu.php');
				if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'megamenucreatorpro') 
					include_once(self::$plugin_path.'admin/includes/mmcp_controllers.php');
	 		}

		}

		/**
		 * Load classes
		 * @return void
		 */
		public function load_classes() {
			if (is_admin()) {
				include_once(self::$plugin_path.'admin/includes/mmcp_ajax.php');
				include_once(self::$plugin_path.'admin/includes/mmcp_widget.class.php');
				include_once(self::$plugin_path.'admin/includes/class_mmcp_walker_admin_menu.php');
			}
		}

		/**
		 * Load nav menus page functionality.
		 * 
		 * @access public
		 * @return void
		 */
		public function load_nav_menus()
		{
			add_filter('wp_edit_nav_menu_walker', array($this, 'wp_edit_nav_menu_walker'), 99);
		}		

		/**
		 * Add menu to admin
		 * @return void
		 */
		public function wp_admin_menus() {
			add_menu_page(__('Mega Menu Creator Pro', 'mmcp'), __('Mega Menu Creator Pro', 'mmcp'), 'manage_options', 'megamenucreatorpro', array($this, 'load_template_setting'));
		}

		/**
		 * Load template for seting menus
		 * @return void
		 */
		public function load_template_setting() {
			include_once(self::$plugin_path.'admin/includes/menu_manager.php');
			$menu_manager = Menu_Manager::instance();
			include_once(self::$plugin_path.'admin/views/setting.php');
		}

		/**
		 * admin enqueue script and css
		 * @return void
		 */
		public function admin_enqueue_scripts_css($hook) {
            //
            if ( isset($_REQUEST['page']) && $_REQUEST['page'] == 'megamenucreatorpro') {

	            if ($hook === $this->hook_suffix){
	                do_action( 'sidebar_admin_setup' );
	                do_action( 'admin_enqueue_scripts', 'widgets.php' );
	                do_action( 'admin_print_styles-widgets.php' );
	            }            	
            	wp_enqueue_style('megamenucreatorpro_boostrap', self::$plugin_url .'bootstrap/css/bootstrap.min.css', false, '4.0.0');            	
            	wp_enqueue_style('megamenucreatorpro_fontawesome', self::$plugin_url .'fonts/awesome-5.1.0/all.css', false, '5.1.0');
	       		wp_enqueue_style( 'megamenucreatorpro_jsgrid_css', self::$plugin_url . 'admin/assets/libs/jsgrid/jsgrid.min.css', '1.5.3' );            	
	       		wp_enqueue_style( 'megamenucreatorpro_jsgrid_theme_css', self::$plugin_url . 'admin/assets/libs/jsgrid/jsgrid-theme.min.css', '1.5.3' );
			    wp_register_style( 'mmcp-jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
			    wp_enqueue_style( 'mmcp-jquery-ui' );
	       		wp_enqueue_style( 'megamenucreatorpro_css', self::$plugin_url . 'admin/assets/css/megamenucreatorpro.css', false, self::$version );
		        wp_enqueue_script( 'megamenucreatorpro_boostrap_js', self::$plugin_url.'bootstrap/js/bootstrap.min.js', array(
		            'jquery'
		        ), '4.0.0' );
		        wp_enqueue_script( 'megamenucreatorpro_jsgrid_js', self::$plugin_url.'admin/assets/libs/jsgrid/jsgrid.min.js', array(
		            'jquery'
		        ), '1.5.3' );
		        wp_enqueue_script( 'megamenucreatorpro_sidebar_js', self::$plugin_url.'admin/assets/libs/sidebar/jquery.sidebar.min.js', array(
		            'jquery'
		        ), '3.3.2' );
		        wp_enqueue_script( 'megamenucreatorpro_js', self::$plugin_url.'admin/assets/js/megamenucreatorpro.js', array(
		            'jquery',
		            'jquery-ui-core',
		            'jquery-ui-sortable',
		            'jquery-ui-accordion',
		            'jquery-ui-datepicker',
		            'wp-util'
		        ), self::$version );
            }
		}

		/**
		 * Define MMCP Constants.
		 */
		private function define_constants() {
			$this->define( 'MMCP_ABSPATH', dirname( __FILE__ ) . '/' );
			/*$this->define( 'MMCP_PLUGIN_SLUG', self::$plugin_slug );
			$this->define( 'MMCP_PLUGIN_PREFIX', self::$plugin_prefix );
			$this->define( 'MMCP_PLUGIN_BASENAME', self::$plugin_basename );
			$this->define( 'MMCP_PLUGIN_URL', self::$plugin_url );
			$this->define( 'MMCP_PLUGIN_PATH', self::$plugin_path );
			$this->define( 'MMCP_VERSION', self::$version );*/
		}

		/**
		 * Check to see if an AJAX action is being executed.
		 * 
		 * @since 1.4.0 Made function static.
		 * @since 1.1.2
		 * 
		 * @access public static
		 * @param  string  $action Action to check for.
		 * @return boolean         True if the action is being executed.
		 */
		public function is_ajax_action($action)
		{
			if (!defined('DOING_AJAX') || !DOING_AJAX) return false;

			$current_action = (isset($_GET['action'])) ? $_GET['action'] : '';
			$current_action = (empty($current_action) && isset($_POST['action'])) ? $_POST['action'] : $current_action;

			return ($action == $current_action);
		}

		/**
		 * Filter the Walker class used when adding nav menu items.
		 * 
		 * @since 1.1.0
		 * 
		 * @access public
		 * @return string Class name of the Walker to use.
		 */
		public function wp_edit_nav_menu_walker()
		{
			return 'MMCP_Walker_Nav_Menu_Edit';
		}		

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	    public function mmcp_load_textdomain()
	    {
	        load_plugin_textdomain('mmcp', false, dirname(self::$plugin_basename) . '/languages/');	
	    }
	}

	/**
	 * Returns the main instance of Mega Menu Createor Pro to prevent the need to use globals.
	 *
	 * @return Mega_Menu_Creator_Pro
	 */
	function MMCPRO() {
		return Mega_Menu_Creator_Pro::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['mmcpro'] = MMCPRO();

}
