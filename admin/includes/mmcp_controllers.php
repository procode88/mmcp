<?php
if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
if (!class_exists('MMCP_Controllers'))
{
	class MMCP_Controllers {

	 	private $messages = array();

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
	 		if (is_admin() && isset($_REQUEST['action'])) {
				//$this->processing_action_request(); 			
	 		}

	 	}
		/**
		 * Processing Action request
		 *
		 * @param  string $action
		 * @param int $nav_menu_selected_id
		 *
		 * return void
		 */
		public function processing_action_request() {
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';
			$nav_menu_selected_id = isset($_REQUEST['menu']) ? (int) $_REQUEST['menu'] : 0;
			// Container for any messages displayed to the user
			// Get existing menu locations assignments
			$locations = get_registered_nav_menus();
			$menu_locations = get_nav_menu_locations();
			$num_locations = count( array_keys( $locations ) );
			switch ( $action ) {
				case 'update':
					check_admin_referer( 'mmcp-update-nav_menu', 'mmcp-update-nav-menu-nonce' );
					// Remove menu locations that have been unchecked.
					foreach ( $locations as $location => $description ) {
						if ( ( empty( $_POST['menu-locations'] ) || empty( $_POST['menu-locations'][ $location ] ) ) && isset( $menu_locations[ $location ] ) && $menu_locations[ $location ] == $nav_menu_selected_id )
							unset( $menu_locations[ $location ] );
					}

					// Merge new and existing menu locations if any new ones are set.
					if ( isset( $_POST['menu-locations'] ) ) {
						$new_menu_locations = array_map( 'absint', $_POST['menu-locations'] );
						$menu_locations = array_merge( $menu_locations, $new_menu_locations );
					}

					// Set menu locations.
					set_theme_mod( 'nav_menu_locations', $menu_locations );
					if ( 0 == $nav_menu_selected_id ) {
						$new_menu_title = trim( esc_html( $_POST['menu-name'] ) );
						if ( $new_menu_title ) {
							$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );
							if ( is_wp_error( $_nav_menu_selected_id ) ) {
								$this->messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
							} else {
								$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
								$nav_menu_selected_id = $_nav_menu_selected_id;
								$nav_menu_selected_title = $_menu_object->name;
								if ( isset( $_REQUEST['menu-item'] ) )
									wp_save_nav_menu_items( $nav_menu_selected_id, absint( $_REQUEST['menu-item'] ) );
								if ( isset( $_REQUEST['zero-menu-state'] ) ) {
									// If there are menu items, add them
									wp_nav_menu_update_menu_items( $nav_menu_selected_id, $nav_menu_selected_title );
									// Auto-save nav_menu_locations
									$locations = get_nav_menu_locations();
									foreach ( $locations as $location => $menu_id ) {
											$locations[ $location ] = $nav_menu_selected_id;
											break; // There should only be 1
									}
									set_theme_mod( 'nav_menu_locations', $locations );
								}
								if ( isset( $_REQUEST['use-location'] ) ) {
									$locations = get_registered_nav_menus();
									$menu_locations = get_nav_menu_locations();
									if ( isset( $locations[ $_REQUEST['use-location'] ] ) )
										$menu_locations[ $_REQUEST['use-location'] ] = $nav_menu_selected_id;
									set_theme_mod( 'nav_menu_locations', $menu_locations );
								}

								wp_redirect( add_query_arg( array( 'menu' => $_nav_menu_selected_id ), admin_url( 'admin.php?page=megamenucreatorpro' ) ) );
								exit();
							}						
						} else {
							$this->messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
						}
					} else {
						$_menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

						$menu_title = trim( esc_html( $_POST['menu-name'] ) );
						if ( ! $menu_title ) {
							$this->messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
							$menu_title = $_menu_object->name;
						}

						if ( ! is_wp_error( $_menu_object ) ) {
							$_nav_menu_selected_id = wp_update_nav_menu_object( $nav_menu_selected_id, array( 'menu-name' => $menu_title ) );
							if ( is_wp_error( $_nav_menu_selected_id ) ) {
								$_menu_object = $_nav_menu_selected_id;
								$this->messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
							} else {
								$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
								$nav_menu_selected_title = $_menu_object->name;
							}
						}

						// Update menu items.
						if ( ! is_wp_error( $_menu_object ) ) {
							$this->messages = array_merge( $this->messages, wp_nav_menu_update_menu_items( $_nav_menu_selected_id, $nav_menu_selected_title ) );

							// If the menu ID changed, redirect to the new URL.
							if ( $nav_menu_selected_id != $_nav_menu_selected_id ) {
								wp_redirect( add_query_arg( array( 'menu' => intval( $_nav_menu_selected_id ) ), admin_url( 'admin.php?page=megamenucreatorpro' ) ) );
								exit();
							}
						}
					}
					break;
			}
		}

		/**
		 * get All Messages of controllers
		 * 
		 * Return Array
		 */
		public function get_messages() {
			return $this->messages;
		}

	 }

	if (is_admin() && isset($_REQUEST['action'])) {
		MMCP_Controllers::instance()->processing_action_request();
	}
}