<?php
/**
 * Modal Edit Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>
<div class="modal fade" id="mmcp_wrapperModal" tabindex="-1" role="dialog" aria-labelledby="mmcpModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="mmcpModalLabel">Menu Detail</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
	  	<div class="load_content"><div class="wpmm-item-loading"></div></div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Close') ?></button>
		<button type="button" class="btn btn-primary"><?php _e('Save')?></button>
	  </div>
	</div>
  </div>
</div>