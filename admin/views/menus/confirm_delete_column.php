<?php
/**
 * Modal Edit Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>
<div class="modal fade" id="mmcp_wrapperConfirmColumnModal" tabindex="-1" role="dialog" aria-labelledby="mmcpConfirmColumnModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="mmcpConfirmColumnModalLabel"><?php _e('Delete Parmanently')?></h5>
		<input type="hidden" name="menu_item_id" value="" />
		<input type="hidden" name="" value="" />
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		    <p class="column_alert"><?php _e('Do you want to proceed delete item ', 'mmcp')?> <strong></strong></p>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('No') ?></button>
		<button type="button" class="btn btn-primary btn_delete_agree"><?php _e('Yes')?></button>
	  </div>
	</div>
  </div>
</div>