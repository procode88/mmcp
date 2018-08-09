<?php 
/**
 * Menu Sidebar
 */
if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>

<div class="mmcp-sidebars mmcp-menuwidget-layouts">
	<div class="mmcp-sidebar mmcp-left mmcp-menuwidget-layout">
		<div class="container sidebar-content">
			<?php wp_nonce_field( 'mmcp_check_ajax_layout_item_menu_security', 'mmcp_tab_nonce' ); ?>
			<?php wp_nonce_field( 'mmcp_check_ajax_add_item_widget', 'mmcp_add_widget_noce' ); ?>
			<?php wp_nonce_field( 'mmcp_check_ajax_sortable_item_widget', 'mmcp_sortable_widget_noce' ); ?>
			<?php wp_nonce_field( 'mmcp_check_ajax_add_column_layout', 'mmcp_add_column_noce' ); ?>
			<?php wp_nonce_field( 'mmcp_check_ajax_sort_column_layout', 'mmcp_sort_column_noce' ); ?>
			<?php wp_nonce_field( 'mmcp_check_ajax_add_row_layout', 'mmcp_add_row_layout_noce' ); ?>
			<?php wp_nonce_field( 'mmcp_check_ajax_sort_row_layout', 'mmcp_sort_row_layout_noce' ); ?>
			<input type="hidden" name="mmcp_save_options_icon_noce" value="<?php echo wp_create_nonce('mmcp_check_ajax_save_options_icon')?>" />
			<div class="row">
				<div class="col-12 aside-title">
					<h5 class="modal-title" id="mmcpModalLabel"><?php _e('Menu Detail', 'mmcp')?></h5>
				</div>
			</div>

			<div class="row aside-content">
				<div class="taps-seting col-12">
					<div class="row">
						<div class="col col-lg-2 col-xl-2 col-md-2 col-sm-12 col-12 tab-title">
							<div class="mmcp-box-widget">
					            <ul class="nav nav-tabs" id="myTab" role="tablist">
					                <li>
					                	<a class="active" href="#mmcplayout" data-toggle="tab">
					                	<?php _e('Layout', 'mmcp')?>
					                	</a>
					                	<?php if(isset($all_register_widget_items)) {
					                	?>
					                	<ul class="mmcp-list-widget">
					                		<?php foreach($all_register_widget_items as $widget) {?>
					                			<li class="widget-item">
					                				<div class="mmcp-item-widget mmcp-item-drag-widget" data-widgetid="<?php _e($widget['id_base'])?>">
					                					<?php _e($widget['name'])?>
					                					<i class="fas fa-arrows-alt"></i>
					                				</div>
					                			</li>
					                		<?php } ?>
					                	</ul>
					                	<?php } ?>
					                </li>
					                <li>
					                	<a href="#mmcpsettings" data-toggle="tab"><?php _e('Settings', 'mmcp')?></a>
					                </li>
					                <li>
					                	<a href="#mmcpicons" data-toggle="tab"><?php _e('Icons', 'mmcp')?></a>
					                </li>
					            </ul>
				            </div>
						</div>
						<div class="col col-lg-10 col-xl-10 col-md-10 col-sm-12 col-12 tab-content">
							<div class="mmcp-box-top">
								<div class="row-botton">
									<button type="button" class="btn btn-dark mmcp-close">
										<i class="fas fa-times-circle"></i>
										<?php _e('Close') ?>
									</button>
									<button type="button" class="btn btn-primary mmcp_save_config">
										<i class="fas fa-save"></i>
										<?php _e('Save Change') ?>
									</button>					
								</div>								
							</div>
							<div class="tab-content mmcp-tab-content">
								<div class="mmcp-messages"></div>
						
				                <div class="tab-pane fade active show" id="mmcplayout">
				                </div>
				                <div class="tab-pane fade" id="mmcpsettings">
				                	
				                </div>
				                <div class="tab-pane fade" id="mmcpicons">
				                	Icons menu.
				            	</div>
								<div class="load_content" style="display:none">
									<div class="mmcp-item-loading"></div>
								</div>				            	
							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>
</div>
<div id="asideOverlay" style="opacity: 0.9; cursor: pointer; visibility: visible;"></div>