<?php
/**
 * Panel settings
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>

<div class="wrap mmcp-wrap">
	<h1><?php _e('Mega Menu Creator Pro Settings', 'mmcp')?></h1>
	<!--i class="fas fa-address-book"></i>
	<i class="fas unicode-fas"></i-->
	<div class="block-menugroup">
		<div class="mmcp-wrap-content">
			<?php $menu_manager->generate_menu() ?>
		</div>
	</div>
</div>