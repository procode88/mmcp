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
			<div class="row">
				<div class="col-12 aside-title">
					<h5 class="modal-title" id="mmcpModalLabel">Menu Detail</h5>
				</div>
			</div>

			<div class="row aside-content">
				<div class="taps-seting col-12">
					<div class="row">
						<div class="col-2 tab-title">
				            <ul class="nav nav-tabs" id="myTab" role="tablist">
				                <li><a class="active" href="#mmcplayout" data-toggle="tab">Layout</a></li>
				                <li><a href="#mmcpsettings" data-toggle="tab">Settings</a></li>
				                <li><a href="#mmcpicons" data-toggle="tab">Icons</a></li>
				            </ul>
						</div>
						<div class="col-10 tab-content">
							<div class="tab-content">
				                <div class="tab-pane fade active show" id="mmcplayout">Layout menu</div>
				                <div class="tab-pane fade" id="mmcpsettings">Settings menu.</div>
				                <div class="tab-pane fade" id="mmcpicons">Icons menu.</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 row-botton">
					<button type="button" class="btn btn-secondary mmcp-close">
						<?php _e('Close') ?>
					</button>				
				</div>
			</div>			
		</div>
		<div class="load_content" style="display:none">
			<div class="wpmm-item-loading"></div>
		</div>
	</div>
</div>
<div id="asideOverlay" style="opacity: 0.9; cursor: pointer; visibility: visible;"></div>