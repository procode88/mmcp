<?php
/**
 * Customized nav menu edit walker.
 * 
 */
 if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}

if (!class_exists('MMCP_Walker_Nav_Menu_Edit'))
{
	/**
	 * Customized nav menu edit walker.
	 *
	 * @since 1.0.0
	 *
	 * @uses Walker_Nav_Menu
	 */
	class MMCP_Walker_Nav_Menu_Edit extends Walker_Nav_Menu {
		/**
		 * Starts the list before the elements are added.
		 *
		 * @see Walker_Nav_Menu::start_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @see Walker_Nav_Menu::end_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {}

		/**
		 * Start the element output.
		 *
		 * @see Walker_Nav_Menu::start_el()
		 * @since 3.0.0
		 *
		 * @global int $_wp_nav_menu_max_depth
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 * @param int    $id     Not used.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) { 
			global $_wp_nav_menu_max_depth;
			$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

			ob_start();
			$item_id = esc_attr( $item->ID );

			$original_title = false;
			if ( 'taxonomy' == $item->type ) {
				$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
				if ( is_wp_error( $original_title ) )
					$original_title = false;
			} elseif ( 'post_type' == $item->type ) {
				$original_object = get_post( $item->object_id );
				$original_title = get_the_title( $original_object->ID );
			} elseif ( 'post_type_archive' == $item->type ) {
				$original_object = get_post_type_object( $item->object );
				if ( $original_object ) {
					$original_title = $original_object->labels->archives;
				}
			} elseif('custom' == $item->type) {

			}

			$classes = array(
				'menu-item menu-item-depth-' . $depth,
				'menu-item-' . esc_attr( $item->object ),
				'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
			);

			$title = $item->title;

			if ( ! empty( $item->_invalid ) ) {
				$classes[] = 'menu-item-invalid';
				/* translators: %s: title of menu item which is invalid */
				$title = sprintf( __( '%s (Invalid)' ), $item->title );
			} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
				$classes[] = 'pending';
				/* translators: %s: title of menu item in draft status */
				$title = sprintf( __('%s (Pending)'), $item->title );
			}

			$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

			$submenu_text = '';
			if ( 0 == $depth )
				$submenu_text = 'style="display: none;"';
			?>
				<li id="menu-item-<?php echo $item_id; ?>" data-depth="<?php echo $depth ?>" class="<?php echo implode(' ', $classes ); ?>">
					<div class="menu-item-bar">
						<div class="menu-item-handle">
							<span class="item-title">
								<span class="menu-item-title">
									<?php echo esc_html( $title ); ?>
								</span> 
								<span class="is-submenu" <?php echo $submenu_text; ?>>
									<?php _e( 'sub item' ); ?>
								</span>
								<span class="button-edit" data-title="<?php _e($title) ?>" title="<?php _e('Edit Item') ?>"><i class="far fa-edit"></i> </span>
							</span>
							<span class="item-controls">
								<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
							</span>
							<input class="menu-item-title" type="hidden" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $title ); ?>" />	
							<?php
								if('custom' == $item->type) {
							?>
							<input class="menu-item-url" type="hidden" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo $item->url; ?>" />							
							<?php } ?>
							<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
							<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
							<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
							<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
							<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
							<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />							
						</div>
					</div>
					<ul class="menu-item-transport"></ul>
				</li>
			<?php
				$output .= ob_get_clean();
		}		
	}
}