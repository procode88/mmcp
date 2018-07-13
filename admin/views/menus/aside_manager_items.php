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
					<h5 class="aside-title" id="mmcpAsideLabel"><?php _e('Setting Type Of Item Menu')?></h5>
					<input type="hidden" name="ajaxtab-check" value="<?php echo wp_create_nonce( "mmcp_check_ajax_tab_data_security" )?>" />
				</div>
			</div>
			<div class="row aside-content">
				<div class="taps-seting col-12">
					<div class="row">
						<div class="col-2 tab-title">
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
						<div class="col-10 tab-content">
							<div class="tab-content" style="height:auto">
								<div class="tab-pane fade active show" style="height: auto" id="mmcpPages">
									Pages
								</div>
								<div class="tab-pane fade" id="mmcpPosts">
									Posts
								</div>
								<div class="tab-pane fade" id="mmcpCategories">
									Categories
								</div>
								<div class="tab-pane fade" id="mmcpTags">
									Tags
								</div>
								<div class="tab-pane fade" id="mmcpCustomlinks">
									Custom Links
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 ">
							<div class="row-botton">
								<button type="button" class="btn btn-primary addtomenu"><?php _e('Add To Menu')?></button>
								<button type="button" class="btn btn-secondary mmcp-close">
									<?php _e('Close') ?>
								</button>
							</div>
			
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>