<?php
/**
 * Aside Manger Items
 */

 if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>
<div class="mmcp-sidebars mmcp-manager-typeitem-menus">
	<div class="mmcp-sidebar mmcp-left mmcp-manager-typeitem-menu">
		<div class="container sidebar-content">
			<div class="row">
				<div class="col-12 aside-title">
					<h5 class="mmcp-aside-title" id="mmcpAsideLabel"><?php _e('Setting Type Of Item Menu')?></h5>
					<input type="hidden" name="ajaxtab-check" value="<?php echo wp_create_nonce( "mmcp_check_ajax_tab_data_security" )?>" />
					<?php wp_nonce_field('add-menu_item', 'menu-settings-column-nonce'); ?>
				</div>
			</div>
			<div class="row aside-content">
				<div class="taps-seting col-12">
					<div class="row">
						<div class="col-2 tab-title">
							<div class="mmcp-box-typeitem">
								<ul class="nav nav-tabs" id="typeItemTab" role="tablist">
					                <li>
					                	<a class="" href="#mmcpPages" data-toggle="tab">
					                		<?php _e('Pages') ?>
					                	</a>
					                </li>
					                <li><a href="#mmcpPosts" data-toggle="tab"><?php _e('Posts') ?></a></li>
					                <li>
					                	<a href="#mmcpCategories" data-toggle="tab">
					                		<?php _e('Categories') ?>
					                	</a>
					                </li>
					                <li>
					                	<a href="#mmcpTags" data-toggle="tab">
					                		<?php _e('Tags') ?>
					                	</a>
					                </li>
					                <li>
					                	<a href="#mmcpCustomlinks" data-toggle="tab">
					                		<?php _e('Custom Links') ?>
					                	</a>
					                </li>
								</ul>
							</div>
						</div>
						<div class="col-10 tab-content">
							<div class="row">
								<div class="col-12 ">
									<div class="row-botton">
										<span class="spinner" style="float: none;"></span>
										<button type="button" class="btn btn-primary addtomenu"><?php _e('Add To Menu')?></button>
										<button type="button" class="btn btn-secondary mmcp-close">
											<?php _e('Close') ?>
										</button>
									</div>
					
								</div>
							</div>							
							<div class="tab-content" style="height:auto">
								<div class="tab-pane mmcp-padding-top fade active show" style="height: auto" id="mmcpPages">
									
								</div>
								<div class="tab-pane mmcp-padding-top fade" id="mmcpPosts">
									
								</div>
								<div class="tab-pane mmcp-padding-top fade" id="mmcpCategories">
									
								</div>
								<div class="tab-pane mmcp-padding-top fade" id="mmcpTags">
									
								</div>
								<div class="tab-pane mmcp-padding-top fade" id="mmcpCustomlinks">
									<?php $_nav_menu_placeholder = -1; ?>
									<!--span class="spinner"></span-->
									<div class="inside">
										<div class="customlinkdiv" id="customlinkdiv">
											<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />
											<p id="menu-item-url-wrap" class="wp-clearfix">
												<label class="howto" for="custom-menu-item-url"><?php _e( 'URL' ); ?></label>
												<input id="custom-menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" type="text" class="code menu-item-textbox" value="http://" />
											</p>

											<p id="menu-item-name-wrap" class="wp-clearfix">
												<label class="howto" for="custom-menu-item-name"><?php _e( 'Link Text' ); ?></label>
												<input id="custom-menu-item-name" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" />
											</p>
											<input type="hidden"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" />
										</div>
									</div>									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>