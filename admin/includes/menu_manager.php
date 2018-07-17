<?php

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
if (!class_exists('Menu_Manager'))
{
	class Menu_Manager {

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
			$this->includes();
			//$this->generate_menu();
		}

		/**
		 * Includes Class PHP
		 * return void
		 */
		public function includes() {
			//require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');
		}

		/**
		 * Processing Action request
		 *
		 * @param  string $action
		 * @param int $nav_menu_selected_id
		 *
		 * return Array
		 */
		public function processing_action_request($action, $nav_menu_selected_id) {
			// Container for any messages displayed to the user
			$messages = array();		
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
								$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
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
							$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
						}
					} else {
						$_menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

						$menu_title = trim( esc_html( $_POST['menu-name'] ) );
						if ( ! $menu_title ) {
							$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
							$menu_title = $_menu_object->name;
						}

						if ( ! is_wp_error( $_menu_object ) ) {
							$_nav_menu_selected_id = wp_update_nav_menu_object( $nav_menu_selected_id, array( 'menu-name' => $menu_title ) );
							if ( is_wp_error( $_nav_menu_selected_id ) ) {
								$_menu_object = $_nav_menu_selected_id;
								$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
							} else {
								$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
								$nav_menu_selected_title = $_menu_object->name;
							}
						}

						// Update menu items.
						if ( ! is_wp_error( $_menu_object ) ) {
							$messages = array_merge( $messages, wp_nav_menu_update_menu_items( $_nav_menu_selected_id, $nav_menu_selected_title ) );

							// If the menu ID changed, redirect to the new URL.
							if ( $nav_menu_selected_id != $_nav_menu_selected_id ) {
								wp_redirect( add_query_arg( array( 'menu' => intval( $_nav_menu_selected_id ) ), admin_url( 'admin.php?page=megamenucreatorpro' ) ) );
								exit();
							}
						}
					}
					break;
			}
			return $messages;
		}

		/**
		 * Get all Users
		 * @return array
		 */
		protected function get_all_user_data() {
			$user_data = array(array('id' => 0, 'name' => ''));
			$all_user = get_users();
			foreach($all_user as $user) {
				$item = array(
					'id' => $user->ID,
					'name' => $user->display_name
				);
				array_push($user_data, $item);
			}
			return $user_data;
		}

		/**
		 * Generate struct data menu
		 * Return string
		 */
		public function generate_menu() {
			$nav_menus = wp_get_nav_menus();
			$menu_count = count($nav_menus);

			// Allowed actions: add, update, delete
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

			$nav_menu_selected_id = isset($_REQUEST['menu']) ? (int) $_REQUEST['menu'] : 0;

			// Are we on the add new screen?
			$add_new_screen = ( isset( $_GET['menu'] ) && 0 == $_GET['menu'] ) ? true : false;
	        // Get recently edited nav menu.
	        $recently_edited = absint(get_user_option('nav_menu_recently_edited'));
	        if (empty($recently_edited) && is_nav_menu($nav_menu_selected_id))
	            $recently_edited = $nav_menu_selected_id;

	        // Use $recently_edited if none are selected.
	        if (empty($nav_menu_selected_id) && !isset($_GET['menu']) && is_nav_menu($recently_edited))
	            $nav_menu_selected_id = $recently_edited;

	        if (empty($nav_menu_selected_id) && !empty($nav_menus) && !$add_new_screen) {
	            // if we have no selection yet, and we have menus, set to the first one in the list.
	            $nav_menu_selected_id = $nav_menus[0]->term_id;
	        }

	        // Update the user's setting.
	        if ($nav_menu_selected_id != $recently_edited && is_nav_menu($nav_menu_selected_id))
	            update_user_meta(get_current_user_id(), 'nav_menu_recently_edited', $nav_menu_selected_id);

	        //if menu hase change on dropdwon  and save menu than recently menu option will update
	        if (!empty($_POST['menu'])) {
	            $nav_menu_selected_id = (int) $_POST['menu'];
	            $user_id = get_current_user_id();
	            update_user_meta($user_id, 'nav_menu_recently_edited', $nav_menu_selected_id);
	        }
	        $menu_locations = get_nav_menu_locations();
	        $locations = get_registered_nav_menus();

	        $users_data = array('udata' => $this->get_all_user_data());

			$nav_menus_l10n = array(
				'oneThemeLocationNoMenus' => '',
				'moveUp'       => __( 'Move up one' ),
				'moveDown'     => __( 'Move down one' ),
				'moveToTop'    => __( 'Move to the top' ),
				/* translators: %s: previous item name */
				'moveUnder'    => __( 'Move under %s' ),
				/* translators: %s: previous item name */
				'moveOutFrom'  => __( 'Move out from under %s' ),
				/* translators: %s:s previous item name */
				'under'        => __( 'Under %s' ),
				/* translators: %s: previous item name */
				'outFrom'      => __( 'Out from under %s' ),
				/* translators: 1: item name, 2: item position, 3: total number of items */
				'menuFocus'    => __( '%1$s. Menu item %2$d of %3$d.' ),
				/* translators: 1: item name, 2: item position, 3: parent item name */
				'subMenuFocus' => __( '%1$s. Sub item number %2$d under %3$s.' ),
			);
			       

	        //$messages = $this->processing_action_request($action, $nav_menu_selected_id);
	        $messages = MMCP_Controllers::instance()->get_messages();
	        wp_nav_menu_setup();
	        wp_initial_nav_menu_meta_boxes();
	        wp_enqueue_script('nav-menu');
			if ( wp_is_mobile() )
				wp_enqueue_script( 'jquery-touch-punch' );   
			wp_localize_script( 'nav-menu', 'menus', $nav_menus_l10n );
			wp_localize_script( 'nav-menu', 'mmcp_users', $users_data );
	        //wp_nav_menu_post_type_meta_boxes();
	        //wp_nav_menu_taxonomy_meta_boxes();        
	        include_once(MMCPRO()->plugin_path().'/admin/views/menus/group_menu.php');
	        include_once(MMCPRO()->plugin_path().'/admin/views/menus/items.php');
	        //include_once(MMCPRO()::$plugin_path.'admin/views/menus/group_menu.php');
		}
	}
}