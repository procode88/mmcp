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
		<h5 class="modal-title" id="mmcpModalLabel"><?php _e('Delete Parmanently')?></h5>
		<input type="hidden" name="menu_item_id" value="" />
		<?php wp_nonce_field( 'mmcp-delete-item_menu', 'mmcp_delete_item_menu_nonce' ); ?>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		    <p  class="text-danger"><?php _e('Are you sure about this ?') ?></p>
		    <p class="item_name_category"></p>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('No') ?></button>
		<button type="button" class="btn btn-primary btn_delete_item_menu"><?php _e('Yes')?></button>
	  </div>
	</div>
  </div>
</div>