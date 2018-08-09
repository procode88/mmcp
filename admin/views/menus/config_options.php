<?php
/**
 * Config Options Menu item
 */
if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>
<div class="mmcp-configoptions">

	<form class="mmcp-form-options">
		<h6 class="mmcp-align-left"><?php _e('Menu item settings', 'mmcp') ?></h6>
		<div class="form-group row">
			<label for="mmcp-hideText" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Hide Text', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="checkbox" class="form-check-input" <?php if (isset($sub_layout['hide_text']) && $sub_layout['hide_text']) echo 'checked' ?> name="mmcp_hidetext" id="mmcp-hideText" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-hideArrow" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Hide Arrow', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="checkbox" class="form-check-input" <?php if (isset($sub_layout['hide_arrow']) && $sub_layout['hide_arrow']) echo 'checked' ?> name="mmcp_hidearrow" id="mmcp-hideArrow" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-disableLink" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Disable Link', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="checkbox" class="form-check-input" <?php if (isset($sub_layout['disable_link']) && $sub_layout['disable_link']) echo 'checked' ?> name="mmcp_disablelink" id="mmcp-disableLink" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-hideonmobile" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Hide Item on Mobile', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="checkbox" class="form-check-input" name="mmcp_hide_on_mobile" <?php if (isset($sub_layout['hide_on_mobile']) && $sub_layout['hide_on_mobile']) echo 'checked' ?> id="mmcp-hideonmobile" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-hideondesktop" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Hide Item on Desktop', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="checkbox" class="form-check-input" name="mmcp_hide_on_desktop" <?php if (isset($sub_layout['hide_on_desktop']) && $sub_layout['hide_on_desktop']) echo 'checked' ?> id="mmcp-hideondesktop" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-itemalignment" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Menu Item Alignment', 'mmcp') ?>
			</label>
			<div class="col-sm-2">
				<select class="form-control" id="mmcp-itemalignment" name="mmcp_item_alignment">
					<?php 
						$item_align_data = $this->get_item_align_data();
						foreach($item_align_data as $key => $value) {
							?>
								<option value="<?php echo $key ?>" <?php if (isset($sub_layout['item_align']) && $sub_layout['item_align'] == $key) echo 'selected' ?>><?php echo $value ?></option>
							<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-iconposition" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Icon Position', 'mmcp') ?>
			</label>
			<div class="col-sm-2">
				<select class="form-control" id="mmcp-iconposition" name="mmcp_icon_position">
					<?php 
						$icon_positions_data = $this->get_icon_position_data();
						foreach($icon_positions_data as $key => $value) {
							?>
								<option value="<?php echo $key ?>" <?php if (isset($sub_layout['icon_position']) && $sub_layout['icon_position'] == $key) echo 'selected' ?>><?php echo $value ?></option>
							<?php
						}
					?>			
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-badgetext" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Badge Text', 'mmcp') ?>
			</label>
			<div class="col-sm-3 ">
				<input type="text" class="form-control" value="<?php if(isset($sub_layout['badge_text'])) echo $sub_layout['badge_text'] ?>" name="mmcp_badge_text" id="mmcp-badgetext" />
			</div>
			<label for="mmcp-badgetype" class="col-sm-1 col-form-label mmcp-align-left">
				<?php _e('Type', 'mmcp') ?>
			</label>			
			<div class="col-sm-2">
				<select class="form-control" name="mmcp_badgetype" id="mmcp-badgetype">
					<?php 
						$badge_type_data = $this->get_badge_type_data();
						foreach($badge_type_data as $key => $value) {
							?>
							<option value="<?php echo $key ?>" <?php if (isset($sub_layout['badge_type']) && $sub_layout['badge_type'] == $key) echo 'selected' ?>><?php echo $value ?></option>							
							<?php
						}
					?>
				</select>				
			</div>
		</div>	
		<h6 class="mmcp-align-left"><?php _e('Sub item settings', 'mmcp') ?></h6>
		<div class="form-group row">
			<label for="mmcp-submenualign" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Sub Menu Align', 'mmcp') ?>
			</label>
			<div class="col-sm-2">
				<select class="form-control" name="mmcp_submenu_align" id="mmcp-submenualign">
					<?php 
						$submenu_align_data = $this->get_submenu_align_data();
						foreach($submenu_align_data as $key => $value) {
							?>
							<option value="<?php echo $key ?>" <?php if (isset($sub_layout['dropdown_align']) && $sub_layout['dropdown_align'] == $key) echo 'selected' ?>><?php echo $value ?></option>							
							<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-customclass" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Custom Class', 'mmcp') ?>
			</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="mmcp_custom_class" value="<?php if(isset($sub_layout['custom_class'])) echo $sub_layout['custom_class'] ?>" id="mmcp-customclass" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-widthsubmenu" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Width Sub Menu', 'mmcp') ?>
			</label>
			<div class="col-sm-2">
				<select class="form-control" id="custom-width-row" name="mmcp_width_submenu">
					<option value="">-- Select --</option>
					<?php 
						$col_width_data = $this->get_col_width_data();
						foreach($col_width_data as $key => $value) {
							?>
							<option value="<?php echo $key ?>" <?php if (isset($sub_layout['width_submenu']) && $sub_layout['width_submenu'] == $key) echo 'selected' ?>><?php echo $value ?></option>							
							<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-effect" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Effect Show Sub', 'mmcp') ?>
			</label>
			<div class="col-sm-2">
				<select class="form-control" name="mmcp_effect" id="mmcp-effect">
					<option value="dropdown"><?php _e('Dropdown', 'mmcp') ?></option>
					<option value="accordion"><?php _e('Accordion', 'mmcp') ?></option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-hidesubmenuonmobile" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Hide Sub Menu on Mobile', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="checkbox" class="form-check-input" name="mmcp_hide_submenu_on_mobile" <?php if (isset($sub_layout['hide_sub_menu_on_mobile']) && $sub_layout['hide_sub_menu_on_mobile']) echo 'checked' ?> id="mmcp-hidesubmenuonmobile" />
			</div>
		</div>
		<div class="form-group row">
			<label for="mmcp-backgroundsubmenu" class="col-sm-4 col-form-label mmcp-align-left">
				<?php _e('Background Image Sub Menu', 'mmcp') ?>
			</label>
			<div class="col-sm-2 mmcp-align-left">
				<input type="button" id="mmcp-backgroundsubmenu" class="mmcp_upload_image_button button" value="<?php _e( 'Upload image', 'mmcp' ); ?>" /> <br />
				<div class="block_mmcp_files">
					<?php if(isset($sub_layout['background_image_submenu']) && $sub_layout['background_image_submenu']) { ?>
						<img class="mmcp_upload_img" src="<?php echo $sub_layout['background_image_submenu']?>" />
						<span class="mmcp_delete_img_upload">
							<i class="far fa-trash-alt"></i>
						</span>
					<?php } ?>
				</div>
				<input type="hidden" name="mmcp_background_upload_img" value="<?php if(isset($sub_layout['background_image_submenu'])) echo $sub_layout['background_image_submenu'] ?>"/>
			</div>
		</div>
	</form>
</div>