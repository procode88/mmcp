<?php
/**
 * Modal Edit Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>
<div class="modal fade" id="mmcp_configcolumnModal" tabindex="-1" role="dialog" aria-labelledby="mmcpModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="mmcpColumnModalLabel"><?php _e('Config Column')?> <strong></strong></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  	<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="mmcp-form-config">
					<input type="hidden" name="row_id" value=""/>
					<input type="hidden" name="type" value="column" />
					<input type="hidden" name="column_id" value="" />
					<input type="hidden" name="action" value="mmcp_save_config" />
					<input type="hidden" name="mmcp_save_config_data_noce" value="<?php echo wp_create_nonce('mmcp_check_ajax_save_config_data')?>" />
					<div class="form-group">
						<label for="custom-class" class="col-form-label"><?php _e('Custom class:')?></label>
						<input type="text" class="form-control custom-class" id="custom-class" name="class">
					</div>					
					<div class="form-group">
						<label for="custom-width" class="col-form-label"><?php _e('Width Column:')?></label>
						<select class="form-control" id="custom-width" name="width">
							<option value="">-- Select --</option>
							<option value="1">col-1</option>
							<option value="2">col-2</option>
							<option value="3">col-3</option>
							<option value="4">col-4</option>
							<option value="5">col-5</option>
							<option value="6">col-6</option>
							<option value="7">col-7</option>
							<option value="8">col-8</option>
							<option value="9">col-9</option>
							<option value="10">col-10</option>
							<option value="11">col-11</option>
							<option value="12">col-12</option>
						</select>
					</div>
					<div class="form-check">
						<input type="checkbox" class="form-check-input" name="mmcp_hide_on_mobile" id="mmcp_hide_on_mobile">
						<label class="form-check-label" for="mmcp_hide_on_mobile"><?php _e('Hide on mobile')?></label>
					</div>
					<div class="form-check">
						<input type="checkbox" class="form-check-input" name="mmcp_hide_on_desktop" id="mmcp_hide_on_desktop">
						<label class="form-check-label" for="mmcp_hide_on_desktop"><?php _e('Hide on desktop')?></label>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Cancel') ?></button>
				<button type="button" class="btn btn-primary btn_submit_config_data"><?php _e('Submit')?></button>
			</div>
		</div>
	</div>
</div>