<?php
/**
 * Struct Data Menu items
 */

if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
if (empty($menu_items)) {
?>
<div class="title">
	<h3><?php _e('No Menu Found') ?></h3>
</div>
<ul id="menu-to-edit" class="menu ui-sortable menu-manager-plus-menu-wrapper"></ul>
<?php
} else {
?>
<ul id="menu-to-edit" class="menu ui-sortable">
	<?php 
    $depth = 0;
    $flag_array = array();
    $menu_item_ids = '';	
	for ($index = 0; $index < count($menu_items); $index++) {
		if ($menu_items[$index]->menu_item_parent == 0) {
			$depth = 0;
			unset($flag_array);
		}
		if (0 != $index) {
			if ($menu_items[$index]->menu_item_parent == $menu_items[$index - 1]->ID) {
				$depth++;
			}
		}
		$menu_item_ids .= $menu_items[$index]->object_id . ",";
        if (!empty($menu_items[$index + 1]->menu_item_parent) && $menu_items[$index]->ID == $menu_items[$index + 1]->menu_item_parent) {
            $flag_array[$depth] = $menu_items[$index]->ID;
        }
	?>
	<li id="menu-item-<?php if (isset($menu_items[$index]->db_id)) echo $menu_items[$index]->db_id; ?>" class="menu-item menu-item-depth-<?php echo $depth; ?> menu-item-page menu-item-edit-inactive" data-depth="<?php echo $depth; ?>" >
		<div class="menu-item-bar">
			<div class="menu-item-handle ui-sortable-handle">
				<span class="item-title">
				<?php
                $menu_not_exist = '';
                if ('post_type' == $menu_items[$index]->type) {
                    $post_status = get_post_status($menu_items[$index]->object_id);
                    if ('publish' != $post_status) {
                        //post exist or not
                        $menu_not_exist = 'post_item_deleted';
                    }
                    ?>
                    <span class="menu-item-title <?php echo esc_attr( $menu_not_exist ); ?> ">
                        <?php _e($menu_items[$index]->title); ?>
                        <span class="amm_main_menu_item_edit <?php echo esc_attr( $menu_not_exist ); ?>" title="Edit this item">&nbsp;</span>
                    </span>
                <?php } else { ?>
                    <span class="menu-item-title"><?php _e($menu_items[$index]->title); ?></span>
                <?php } ?>

                <span class="is-submenu"><?php if ($menu_items[$index]->menu_item_parent <> 0) _e('sub item'); ?></span>
				<span class="button-edit edit_item_menu" data-title="<?php _e($menu_items[$index]->title) ?>" title="<?php _e('Edit Item') ?>">
                    <i class="far fa-edit"></i>
                </span>
                <span class="button-edit delete_item_menu" data-title="<?php _e($menu_items[$index]->title) ?>" title="<?php _e('Delete Item') ?>">
                    <i class="far fa-trash-alt"></i>
                </span>
				</span>
                <span class="item-controls"> 
                    <span class="view_menu_id" style="display:none">#menu-item-<?php echo $menu_items[$index]->db_id; ?> </span>
                    <span class="item-type"><?php echo esc_html( $menu_items[$index]->type_label ); ?></span>
                    <!--span class="menu_sub_details" title="View Attributes">&nbsp;</span-->
                    <!--span data-attr-menu-item='<?php echo esc_attr( $menu_items[$index]->db_id ); ?>' class="delete_node" title="Delete this item">X</span-->
                </span>
                <input type="hidden" class="edit-menu-item-title" name="menu-item-title[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->title); ?>">

                <input class="menu-item-data-db-id" name="menu-item-db-id[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->db_id); ?>" type="hidden">
                <input class="menu-item-data-object-id" name="menu-item-object-id[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->object_id); ?>" type="hidden">

                <input class="menu-item-data-object" name="menu-item-object[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->object); ?>" type="hidden">
                <input class="menu-item-data-parent-id" name="menu-item-parent-id[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->menu_item_parent); ?>" type="hidden">
                <input class="menu-item-data-position" name="menu-item-position[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->menu_order); ?>" type="hidden">
                <input class="menu-item-data-type" name="menu-item-type[<?php echo esc_attr( $menu_items[$index]->db_id ); ?>]" value="<?php echo esc_attr($menu_items[$index]->type); ?>" type="hidden">                
			</div>

		</div>
        <ul class="menu-item-transport"></ul>
	</li>
	<?php
        if (!empty($menu_items[$index + 1]->menu_item_parent) && $menu_items[$index + 1]->menu_item_parent <> $menu_items[$index]->ID) {
            if (in_array($menu_items[$index + 1]->menu_item_parent, $flag_array)) {
                $a = array_search($menu_items[$index + 1]->menu_item_parent, $flag_array);
                //$depth = $a - ($i-1);
                $depth = $a - (-1);
            }
        }
	}
	?>
</ul>
<?php
}
?>
<div class="block-button-addmenu">
	<button type="button" class="btn btn-default"><i class="fas fa-plus-circle"></i> <?php _e('Add Item')?></button>
</div>
<!--div class="add-item tooltipcss">
	<i class="fas fa-plus-circle"></i>
	<span class="tooltipcss-text"><?php _e('Add Item To Menu', 'mmcp') ?></span>
</div-->