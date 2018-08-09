<?php
/**
 * Sub layout
 */
if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
?>

<div class="mmcpcontent">
	<div class="mmcp-grid-wrapper">
		<?php if (count($sub_layout['sub_layout'])){ ?>
			<?php 
				$row_max_id = 0;
				$index_id = 0;
				foreach($sub_layout['sub_layout'] as $key => $item) {
					$this->get_max_element_id($item['id'], $row_max_id);
					if($item['type'] == self::DEFAULT_TYPE_ROW) {
					?>
					<div class="mmcp-grid-row" id="<?php echo $item['id']?>">
						<input type="hidden" name="row[<?php echo $item['id']?>][width]" value="<?php echo $this->get_config_value($item, 'width')?>"/>
						<input type="hidden" name="row[<?php echo $item['id']?>][class]" value="<?php echo $this->get_config_value($item, 'class') ?>" />
						<input type="hidden" name="row[<?php echo $item['id']?>][hide_on_mobile]" value="<?php echo $this->get_config_value($item, 'hide_on_mobile')?>"/>
						<input type="hidden" name="row[<?php echo $item['id']?>][hide_on_desktop]" value="<?php echo $this->get_config_value($item, 'hide_on_desktop')?>" />						
						<div class="mmcp-box-action">
							<span class="mmcp-icon-sort-column">
								<i class="fas fa-sort"></i>
								<?php _e('Row') ?>
							</span>
							<span title="config row id <?php echo $item['id']?>" class="mmcp-settings-row" >
								<i class="fas fa-cog"></i>
							</span>
							<span title="delete row id <?php echo $item['id']?>" class="mmcp-delete-row">
								<i class="fas fa-trash-alt"></i>
							</span>							
							<button class="mmcp-add_column btn btn-default btn-sm" type="button">
								<span class="mmcp-add-column">
									<i class="fas fa-plus-circle"></i>
									<?php _e('Column') ?>
								</span>

							</button>
							<div class="clear"></div>
						</div>
						<div class="mmcp-box-bottom">
							<?php 
								$column_max_id = 0;
								foreach($item['data'] as $_key => $_item) {
									$this->get_max_element_id($_item['id'], $column_max_id);
									if ($_item['type'] == self::DEFAULT_TYPE_COLUMN) {
									?>
										<div id="<?php echo $_item['id']?>" class="mmcp-grid-col mmcp-col-<?php echo $this->get_config_value($_item, 'width')?>">
											<input type="hidden" name="column[<?php echo $_item['id']?>][width]" value="<?php echo $this->get_config_value($_item, 'width')?>"/>
											<input type="hidden" name="column[<?php echo $_item['id']?>][class]" value="<?php echo $this->get_config_value($_item, 'class') ?>" />
											<input type="hidden" name="column[<?php echo $_item['id']?>][hide_on_mobile]" value="<?php echo $this->get_config_value($_item, 'hide_on_mobile')?>"/>
											<input type="hidden" name="column[<?php echo $_item['id']?>][hide_on_desktop]" value="<?php echo $this->get_config_value($_item, 'hide_on_desktop')?>" />			
											<div class="mmcp-col-content">
												<div class="mmcp-box-action column-action">
													<span class="mmcp-icon-sort-column">
														<i class="fas fa-arrows-alt"></i>
														<?php _e('Column') ?>
													</span>
													<span title="config column id <?php echo $_item['id']?>" class="mmcp-settings-column" >
														<i class="fas fa-cog"></i>
													</span>
													<span title="delete column id <?php echo $_item['id']?>" class="mmcp-delete-column">
														<i class="fas fa-trash-alt"></i>
													</span>
												</div>
												<div class="mmcp-col-content-widget">
												<?php
													$item_max_id = 0;
													foreach($_item['data'] as $__item) {
														$index_id++;
														$this->get_max_element_id($__item['id'], $item_max_id);
														if($__item['type'] == self::DEFAULT_TYPE_ITEM && $__item['cate'] == 'widget') { ?>
															<div class="mmcp-widget widget" id="<?php echo $__item['id']?>" data-wgorder="<?php echo $__item['order']?>" data-typeitem="<?php echo $__item['cate']?>">
																<div class="widget-top">
																	<div class="widget-title-action">
																		<button type="button" class="widget-action hide-if-no-js widget-form-open" aria-expanded="false">
																			<span class="screen-reader-text"><?php printf( __( 'Edit widget: %s' ), $this->mmcp_get_widget_name($__item) ); ?></span>
																			<span class="toggle-indicator" aria-hidden="true"></span>
																		</button>
																	</div>
																	<div class="widget-title">
																		<h3><?php echo $this->mmcp_get_widget_name($__item); ?><span class="in-widget-title"></span></h3>
																	</div>
																</div>
																<div class="widget-inner widget-inside">
																	<?php  
																		$nonce = wp_create_nonce('mmcp_save_widget_' . $__item['cate_id']); 
																		$nonce_delete = wp_create_nonce('mmcp_delete_widget_' . $__item['cate_id']);?>
																	<form method='post'  class="mmcp_widget_form">
																		<input type='hidden' name='action' value='mmcp_update_widget' />
																		<input type="hidden" name="widget-id" class="widget-id" value="<?php echo $__item['cate_id'] ?>" />
																		<input type='hidden' name='id_base' class="id_base" value='<?php echo $this->mmcp_get_id_base_for_widget_id($__item['cate_id']) ?>' />
																		<input type='hidden' name='widget_id' value='<?php  echo $__item['cate_id'] ?>' />
																		<input type='hidden' name='_wpnonce' value='<?php echo $nonce ?>' />
																		<div class='widget-content'>
																			<?php $this->show_content_widget_form($__item['cate_id']) ?>
														                    <div class='widget-controls'>
														                        <a class='mmcp_delete' data-deltewidget="<?php echo $nonce_delete?>" id="delete_widget_<?php echo $index_id ?>" href='javascript:void(0);'>
														                        	<?php _e('Delete', 'mmcp'); ?>
														                        </a> |
														                        <a class='mmcp_close' href='#close'>
														                        	<?php _e('Close', 'mmcp'); ?>
														                        </a>
														                    </div>
																			
														                    <?php
														                    submit_button( __( 'Save' ), 'button-primary alignright', 'savewidget', false, array('id' => 'savewidget_'.$index_id) );
														                    ?>
														                    <div class="mmcp-item-loading"></div>
														                    <div class="clear"></div>
																		</div>
																	</form>
																</div>
															</div>
														<?php } elseif($__item['type'] == self::DEFAULT_TYPE_ITEM && $__item['cate'] == self::MENU_ITEM) { ?>
															<div class="mmcp-widget widget" id="<?php echo $__item['id']?>" data-wgorder="<?php echo $__item['order']?>" data-typeitem="<?php echo $__item['cate']?>">
																<div class="widget-top">
																	<div class="widget-title">
																		<h3><?php echo  $old_sub_menu_item[$__item['cate_id']]['title'] ; ?><span class="in-widget-title"></span></h3>
																	</div>
																</div>
																<div class="widget-inner widget-inside">
																	<form method='post'  class="mmcp_widget_form">
																		<input type="hidden" name="widget-id" class="widget-id" value="<?php echo $__item['cate_id'] ?>" />
																	</form>
																</div>
															</div>
														<?php }
												?>

												<?php } ?>
												</div>
												<input type="hidden" name="item_max_id" value="<?php echo $item_max_id?>"/>
											</div>
										</div>
									<?php
									}
							?>
							<?php } ?>
							<input type="hidden" name="column_max_id" value="<?php echo $column_max_id?>"/>
						</div>
					</div>
					<?php

					}
			?>
			<?php } ?>
			<input type="hidden" name="row_max_id" value="<?php echo $row_max_id?>"/>
		<?php } else { ?>
			<div class="mmcp-grid-row" id="row_<?php echo $menu_item_id?>_1">
				<div class="mmcp-box-action">
					<span class="mmcp-icon-sort-column">
						<i class="fas fa-sort"></i>
						<?php _e('Row') ?>
					</span>
				</div>
				<div class="mmcp-box-bottom">
					<div id="column_<?php echo $menu_item_id?>_1_1" class="mmcp-grid-col mmcp-col-4">
						<div class="mmcp-col-content">
							<div class="mmcp-box-action column-action">
								<span class="mmcp-icon-sort-column">
									<i class="fas fa-arrows-alt"></i>
									<?php _e('Column') ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="box-addrow">
		<button type="button" class="btn btn-default"><i class="fas fa-plus-circle"></i> <?php _e('Add Row')?></button>
	</div>	
</div>