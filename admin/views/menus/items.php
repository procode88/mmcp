<?php
/**
 * Items Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>
<div class="block-menu-items">
	<div id="menu-management" class="content-menu-item">
		<form id="update-nav-menu" action="<?php echo esc_url( admin_url( 'admin.php?page=megamenucreatorpro' ) );?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="update" />
			<?php wp_nonce_field( 'mmcp-update-nav_menu', 'mmcp-update-nav-menu-nonce' ); ?>
			<input type="hidden" name="menu" id="menu" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<div id="nav-menu-header">
				<div class="major-publishing-actions wp-clearfix">
					<label class="menu-name-label" for="menu-name"><?php _e( 'Menu Name' ); ?></label>
					<input name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox" value="<?php if (!empty($current_selected_menu_name)) echo esc_attr( $current_selected_menu_name ); ?>" />
					<div class="publishing-action">
						<?php submit_button( empty( $nav_menu_selected_id ) ? __( 'Create Menu' ) : __( 'Save Menu' ), 'primary large menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
					</div>
				</div>
			</div>
			<div class="block-menustruct">
				<div class="block-content">
					<?php if ( ! $add_new_screen ) { ?>
						<h3><?php _e( 'Menu Structure' ); ?></h3>
						<?php $starter_copy =  __( 'Drag each item into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options.' ); ?>
						<div class="drag-instructions post-body-plain">
							<p><?php echo $starter_copy; ?></p>
						</div>
						<?php 
							$menu_items = wp_get_nav_menu_items($nav_menu_selected_id);
							include_once(MMCPRO()->plugin_path().'/admin/views/menus/menu_struct.php'); 
							include_once(MMCPRO()->plugin_path().'/admin/views/menus/menu_location.php');
							include_once(MMCPRO()->plugin_path().'/admin/views/menus/menu_sidebar.php');
							include_once(MMCPRO()->plugin_path().'/admin/views/menus/aside_manager_items.php');
						?>
					<?php } ?>
				</div>
			</div>
		</form>
	</div>
</div>