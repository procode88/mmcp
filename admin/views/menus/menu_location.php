<?php
/*
 * Menu Locations
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>

<div class="menu-settings">
	<h3><?php _e( 'Menu Settings' ); ?></h3>
	<?php
	if ( ! isset( $auto_add ) ) {
		$auto_add = get_option( 'nav_menu_options' );
		if ( ! isset( $auto_add['auto_add'] ) )
			$auto_add = false;
		elseif ( false !== array_search( $nav_menu_selected_id, $auto_add['auto_add'] ) )
			$auto_add = true;
		else
			$auto_add = false;
	} ?>
	<fieldset class="menu-settings-group auto-add-pages">
		<legend class="menu-settings-group-name howto"><?php _e( 'Auto add pages' ); ?></legend>
		<div class="menu-settings-input checkbox-input">
			<input type="checkbox"<?php checked( $auto_add ); ?> name="auto-add-pages" id="auto-add-pages" value="1" /> <label for="auto-add-pages"><?php printf( __('Automatically add new top-level pages to this menu' ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></label>
		</div>
	</fieldset>
	<?php if ( current_theme_supports( 'menus' ) ) { ?>
		<fieldset class="menu-settings-group menu-theme-locations">
			<legend class="menu-settings-group-name howto"><?php _e( 'Display location' ); ?></legend>
			<?php foreach ( $locations as $location => $description ) { ?>
			<div class="menu-settings-input checkbox-input">
				<input type="checkbox"<?php checked( isset( $menu_locations[ $location ] ) && $menu_locations[ $location ] == $nav_menu_selected_id ); ?> name="menu-locations[<?php echo esc_attr( $location ); ?>]" id="locations-<?php echo esc_attr( $location ); ?>" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
				<label for="locations-<?php echo esc_attr( $location ); ?>"><?php echo $description; ?></label>
				<?php if ( ! empty( $menu_locations[ $location ] ) && $menu_locations[ $location ] != $nav_menu_selected_id ) { ?>
					<span class="theme-location-set"><?php
						/* translators: %s: menu name */
						printf( _x( '(Currently set to: %s)', 'menu location' ),
							wp_get_nav_menu_object( $menu_locations[ $location ] )->name
						);
					?></span>
				<?php } ?>
			</div>
			<?php } ?>
		</fieldset>
	<?php } ?>
</div>