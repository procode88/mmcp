<?php
/**
 * MMCP Handling Ajax
 */
if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}

class MMCP_Ajax {

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

	public function __construct() {
		add_action('wp_ajax_mmcp_menu_detail', array($this, 'load_menu_detail'));
	}

	public function load_menu_detail() {
		check_ajax_referer( 'mmcp_check_ajax_security', 'mmcp_nonce' );
	}
}
MMCP_Ajax::instance();