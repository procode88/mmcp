<?php
/**
 * Group menu
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}

?>

<div class="group_menu">
    <input type="hidden" name="menu" id="nav-current-group-menu" value="<?php echo esc_attr($nav_menu_selected_id); ?>" />
    <div class="manage-menus">
    	<form action="<?php echo esc_url( admin_url( 'admin.php?page=megamenucreatorpro' ) );?>" name="menu_select" method="POST" >
    		<label for="menu" class="selected-menu"><?php _e('Select a menu to edit:'); ?></label>
    		<select name="menu" id="menu">
				<?php if ( $add_new_screen ) : ?>
					<option value="0" selected="selected"><?php _e( '&mdash; Select &mdash;' ); ?></option>
				<?php endif; ?>
    			<?php
					$current_selected_menu_name = '';
					foreach ($nav_menus as $list_nav_menus) { 
					?>
                        <option value='<?php echo $list_nav_menus->term_id; ?>' <?php if (!$add_new_screen && $nav_menu_selected_id == $list_nav_menus->term_id) { echo 'selected=selected'; $current_selected_menu_name = $list_nav_menus->name; } ?>>
                        <?php
                            _e($list_nav_menus->name);

                            if (!empty($menu_locations) && in_array($list_nav_menus->term_id, $menu_locations)) {
                                $locations_assigned_to_this_menu = array();
                                foreach (array_keys($menu_locations, $list_nav_menus->term_id) as $menu_location_key) {
                                    if (isset($locations[$menu_location_key])) {
                                        $locations_assigned_to_this_menu[] = $locations[$menu_location_key];
                                    }
                                }
                                /**
                                 * Filter the number of locations listed per menu in the drop-down select.
                                 *
                                 * @since 3.6.0
                                 *
                                 * @param int $locations Number of menu locations to list. Default 3.
                                 */
                                $assigned_locations = array_slice($locations_assigned_to_this_menu, 0, absint(apply_filters('wp_nav_locations_listed_per_menu', 3)));

                                // Adds ellipses following the number of locations defined in $assigned_locations.
                                if (!empty($assigned_locations)) {
                                    printf(' (%1$s%2$s)', implode(', ', $assigned_locations), count($locations_assigned_to_this_menu) > count($assigned_locations) ? ' &hellip;' : ''
                                    );
                                }
                            }
                        ?>
                        </option>
					<?php
					}
    			?>
    		</select>
            <span class="submit-btn">
            	<input id="menu_submit_button" class="button-secondary" value="Select" type="submit">
                <label for="menu" class="selected-menu_mymenu"> <span class="add-new-menu-action">
				<?php printf( __( 'or <a href="%s">create a new menu</a>.' ), esc_url( add_query_arg( array( 'menu' => 0 ), admin_url( 'admin.php?page=megamenucreatorpro' ) ) ) ); ?>
			</span>
                </label>
			</span><!-- /add-new-menu-action -->
    	</form>
    </div>
</div>