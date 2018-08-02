<?php
/**
 * Manager Widget Custom
 */
 if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}

if (!class_exists('MMCP_Manager_Widget'))
{
	class MMCP_Manager_Widget {

		const DEFAULT_ROW_WIDTH = 12;
		const DEFAULT_COLUMN_WIDTH = 4;
        const DEFAULT_TYPE_ITEM = 'item';
        const DEFAULT_TYPE_COLUMN = 'column';
        const DEFAULT_TYPE_ROW = 'row';
        const DEFAULT_CATE_ITEM = 'widget';

		protected static $_instance = null;

		/**
		 * Main Plugin Instance
		 *
		 * Ensures only one instance of plugin is loaded or can be loaded.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			add_action('wp_ajax_mmcp_layout_item_menu', array($this, 'load_item_menu_layout'));
			add_action('wp_ajax_mmcp_add_item_widget', array($this, 'add_new_item_widget'));
            add_action('wp_ajax_mmcp_update_widget', array($this, 'update_item_widget'));
            add_action('wp_ajax_mmcp_delete_widget', array($this, 'mmcp_delete_item_widget'));
            add_action('wp_ajax_mmcp_sort_item_widget', array($this, 'mmcp_sortable_widget_item'));
            add_action('wp_ajax_mmcp_add_column_layout', array($this, 'mmcp_add_column_layout'));
            add_action('wp_ajax_mmcp_sort_column_layout', array($this, 'mmcp_sort_column_layout'));
            add_action('wp_ajax_mmcp_add_row_layout', array($this, 'mmcp_add_row_layout'));
            add_action('wp_ajax_mmcp_sort_row_layout', array($this, 'mmcp_sort_row_layout'));
		}

        /**
         * get all item register available widget
         */
        public function get_all_items_register_widget(){
            global $wp_widget_factory;

            $widgets = array();
            foreach( $wp_widget_factory->widgets as $widget ) {
                array_push($widgets, array('name' => $widget->name, 'id_base' => $widget->id_base));
            }
            return $widgets;
        }

        /**
         * Sort Row in the layout
         *
         */
        public function mmcp_sort_row_layout() {
            check_ajax_referer('mmcp_check_ajax_sort_row_layout', 'mmcp_sort_row_layout_noce');
            $menu_id = $this->mmcp_get_request('menu_id');
            $menu_item_id = $this->mmcp_get_request('menu_item_id');
            $row_id = $this->mmcp_get_request('row_id');
            $row_index = $this->mmcp_get_request('row_index');
            if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Could\'t sort row in the layout', 'mmcp')));
            }

            if (!$row_id) {
                wp_send_json_error(array('message' => __('Could\'t sort row in the layout', 'mmcp')));
            }

            if (is_array($sub_layout)) {
                if (isset($sub_layout['sub_layout'])) {
                    $layout = $sub_layout['sub_layout'];
                    if(count($layout)) {
                        $index_row;
                        $row_data = array();
                        $this->get_data_column_row($layout, $index_row, $row_id);
                        $row_data = $layout[$index_row];
                        $row_data['order'] = $row_index;
                        $data_item = array();
                        $data_element_diff = array();
                        $has_update = false;
                        $is_continue = false;
                        $order_status = false;
                        $order = 0;
                        $row_new_data = array();
                        foreach($layout as $key => $item) {
                            if ($item['id'] == $row_id) {
                                $is_continue = true;
                                continue;
                            }
                            if ($item['type'] == self::DEFAULT_TYPE_ROW) {
                                if ($item['order'] == $row_index && !$has_update) {
                                    array_push($data_item, $row_data);
                                    $has_update = true;
                                }
                                $item['order'] = $order;
                                if ($has_update && !$order_status) {
                                    $order+=1;
                                    if (!$is_continue) {
                                        $item['order'] = $order;
                                    }
                                    $order_status = true;
                                }
                                array_push($data_item, $item);
                                $order++;
                            } else {
                                array_push($data_element_diff, $item);
                            }
                        }
                        usort($data_item, array($this, 'mmcp_sort_array'));
                        $row_new_data = array_merge($data_item, $data_element_diff);
                        $sub_layout['sub_layout'] = $row_new_data;
                        update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                        wp_send_json_success(array('message' => __('Sort Row in the layout success!', 'mmcp')));
                    } else {
                        wp_send_json_error(array('message' => __('Could\'t sort row in the layout', 'mmcp')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Could\'t sort row in the layout', 'mmcp')));
                }
            } else {
                wp_send_json_error(array('message' => __('Could\'t sort row in the layout', 'mmcp')));
            }
        }

        /**
         * Add row to layout
         *
         */
        public function mmcp_add_row_layout() {
            check_ajax_referer('mmcp_check_ajax_add_row_layout', 'mmcp_add_row_layout_noce');
            $menu_id = $this->mmcp_get_request('menu_id');
            $menu_item_id = $this->mmcp_get_request('menu_item_id');
            if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Could\'t add row in the layout', 'mmcp')));
            }

            if (is_array($sub_layout)) {
                if (isset($sub_layout['sub_layout'])) {
                    $layout = $sub_layout['sub_layout'];
                    if(count($layout)) {
                        $max_row = $sub_layout['max_row'] + 1;
                        $data_item = array();
                        $data_element_diff = array();
                        $data_row_new = array();
                        $row_struct_data = $this->get_struct_data_row($menu_item_id, $max_row);
                        $order = 0;
                        foreach($layout as $key => $item) {
                            if ($item['type'] == self::DEFAULT_TYPE_ROW) {
                                $item['order'] = $order;
                                array_push($data_item, $item);
                                $order++;
                            } else {
                                array_push($data_element_diff, $item);
                            }
                        }
                        $row_struct_data['order'] = $order;
                        array_push($data_item, $row_struct_data);
                        $data_row_new = array_merge($data_item, $data_element_diff);
                        $sub_layout['sub_layout'] = $data_row_new;
                        $sub_layout['max_row'] = $max_row;
                        update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                        wp_send_json_success(array('message' => __('Add Row in the layout success!', 'mmcp')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Could\'t add row in the layout', 'mmcp')));
                }
            } else {
                wp_send_json_error(array('message' => __('Could\'t add row in the layout', 'mmcp')));
            }
        }

        /**
         * Sort position of column in the layout
         *
         */
        public function mmcp_sort_column_layout() {
            check_ajax_referer('mmcp_check_ajax_sort_column_layout', 'mmcp_sort_column_noce');
            $menu_id = $this->mmcp_get_request('menu_id');
            $menu_item_id = $this->mmcp_get_request('menu_item_id');
            $row_id = $this->mmcp_get_request('row_id');
            $column_id = $this->mmcp_get_request('column_id');
            $column_index = $this->mmcp_get_request('column_index');
            if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Could\'t sort column in the layout', 'mmcp')));
            }

            if (!$row_id || !$column_id) {
                wp_send_json_error(array('message' => __('Could\'t sort column in the layout', 'mmcp')));
            }
            if (is_array($sub_layout)) {
                if (isset($sub_layout['sub_layout'])) {
                    $layout = $sub_layout['sub_layout'];
                    if(count($layout)) {
                        $index_row = null;
                        $index_column = null;
                        $this->get_data_item_column($layout, $index_row, $index_column, $row_id, $column_id);
                        $row_data = $layout[$index_row];
                        $column_data = $row_data['data'][$index_column];
                        $column_data['order'] = $column_index;
                        $new_column_data = array();
                        $data_item = array();
                        $data_element_diff = array();
                        $order = 0;
                        $has_update = false;
                        $item_continue = false;
                        $order_status = false;
                        foreach ($row_data['data'] as $key => $value) {
                            if ($value['id'] == $column_id) {
                                $item_continue = true;
                                continue;
                            }
                            if ($value['type'] == self::DEFAULT_TYPE_COLUMN) {
                                if ($value['order'] == $column_index && !$has_update) {
                                    array_push($data_item, $column_data);
                                    $has_update = true;
                                }
                                $value['order'] = $order;
                                if ($has_update && !$order_status) {
                                    $order +=1;
                                    if (!$item_continue) {
                                        $value['order'] = $order;
                                    }
                                    $order_status = true;
                                }
                                array_push($data_item, $value);
                                $order++;
                            } else {
                                array_push($data_element_diff, $value);
                            }
                        }
                        usort($data_item, array($this, 'mmcp_sort_array'));
                        $new_column_data = array_merge($data_item, $data_element_diff);
                        $sub_layout['sub_layout'][$index_row]['data'] = $new_column_data;
                        update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                        wp_send_json_success(array('message' => __('Sort column in the layout success!', 'mmcp')));
                    } else {
                        wp_send_json_error(array('message' => __('Could\'t sort column in the layout', 'mmcp')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Could\'t sort column in the layout', 'mmcp')));
                }
            } else {
                wp_send_json_error(array('message' => __('Could\'t sort column in the layout', 'mmcp')));
            }
        }

        /**
         * Add new column to sublayout
         *
         */
        public function mmcp_add_column_layout() {
            check_ajax_referer('mmcp_check_ajax_add_column_layout', 'mmcp_add_column_noce');
            $menu_id = $this->mmcp_get_request('menu_id');
            $menu_item_id = $this->mmcp_get_request('menu_item_id');
            $current_row_id = $this->mmcp_get_request('current_row_id');
            if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Could\'t add column to layout', 'mmcp')));
            }

            if (!$current_row_id) {
                wp_send_json_error(array('message' => __('Could\'t add column to layout', 'mmcp')));
            }
            if (is_array($sub_layout)) { 
                if (isset($sub_layout['sub_layout'])) {
                    $layout = $sub_layout['sub_layout'];
                    if(count($layout)) {
                        $index_row = null;
                        $row_id_number = array_pop(explode('_', $current_row_id));
                        
                        $this->get_data_column_row($layout, $index_row, $current_row_id);
                        $row_data = $layout[$index_row];
                        $data_column = array();
                        $data_element_diff = array();
                        $new_column_data = array();
                        $order = 0;
                        foreach ($row_data['data'] as $key => $item) {
                            if ($item['type'] == self::DEFAULT_TYPE_COLUMN) {
                                array_push($data_column, $item);
                                $order++;
                            } else {
                                array_push($data_element_diff, $item);
                            }
                        }
                        $column_index = $row_data['max_column']+1;
                        $new_data_column = $this->get_struct_data_column($menu_item_id, $row_id_number, $column_index);
                        $new_data_column['order'] = $order;
                        array_push($data_column, $new_data_column);
                        unset($new_data_column);
                        $new_column_data = array_merge($data_column, $data_element_diff);
                        //array_push($row_data['data'], $new_data_column);
                        $sub_layout['sub_layout'][$index_row]['max_column'] = $column_index;
                        $sub_layout['sub_layout'][$index_row]['data'] = $new_column_data;
                        update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                        wp_send_json_success(array('message' => __('Add column to layout success!', 'mmcp')));
                    } else {
                        wp_send_json_error(array('message' => __('Could\'t add column to layout', 'mmcp')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Could\'t add column to layout', 'mmcp')));
                }
            } else {
                wp_send_json_error(array('message' => __('Could\'t add column to layout', 'mmcp')));
            }
        }

        /**
         * Sort Item of widget into sublayout
         *
         */
        public function mmcp_sortable_widget_item() {
            check_ajax_referer('mmcp_check_ajax_sortable_item_widget', 'mmcp_sortable_widget_noce');
            $menu_id = $this->mmcp_get_request('menu_id');
            $menu_item_id = $this->mmcp_get_request('menu_item_id');
            $widget_id = $this->mmcp_get_request('widget_id');
            $current_index_item = $this->mmcp_get_request('current_index_item');
            $old_row_id = $this->mmcp_get_request('old_row_id');
            $old_column_id = $this->mmcp_get_request('old_column_id');
            $current_row_id = $this->mmcp_get_request('current_row_id');
            $current_column_id = $this->mmcp_get_request('current_column_id');
            $item_widget_id = $this->mmcp_get_request('item_widget_id');
            $type_item = $this->mmcp_get_request('type_item');
            if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Could\'t sortable widget item', 'mmcp')));
            }

            if (!$old_row_id || !$old_column_id || !$current_row_id || !$current_column_id || !$item_widget_id) {
                wp_send_json_error(array('message' => __('Could\'t sortable widget item', 'mmcp')));
            }

            if (is_array($sub_layout)) {
                if (isset($sub_layout['sub_layout'])) {
                    $layout = $sub_layout['sub_layout'];
                    if(count($layout)) {
                        $item_obj = array('id' => $item_widget_id, 'type' => self::DEFAULT_TYPE_ITEM, 'order' => $current_index_item, 'cate' => $type_item, 'cate_id' => "{$widget_id}");
                        if ($old_row_id === $current_row_id && $old_column_id === $current_column_id) {
                            $index_old_row = null;
                            $index_old_column = null;
                            $data_item = array();
                            $data_element_diff = array();
                            $new_column_data = array();
                            $this->get_data_item_column($layout, $index_old_row, $index_old_column, $old_row_id, $old_column_id);
                            $row_data = $layout[$index_old_row];
                            $columns_data = $row_data['data'][$index_old_column];
                            $__order = 0;
                            $has_update = false;
                            $item_continue = false;
                            $order_status = false;
                            foreach ($columns_data['data'] as $key => $item) {
                                if ($item['id'] == $item_widget_id) {
                                    $item_continue = true;
                                    continue;
                                }
                                if ($item['type'] == self::DEFAULT_TYPE_ITEM) {
                                    if ($item['order'] == $current_index_item && !$has_update) {
                                        array_push($data_item, $item_obj);
                                        $has_update = true;
                                    }
                                    $item['order'] = $__order;
                                    if ($has_update && !$order_status) {
                                        $__order +=1;
                                        if (!$item_continue) {
                                            $item['order'] = $__order;
                                        }
                                        $order_status = true;
                                    }
                                    array_push($data_item, $item);
                                    $__order++;
                                } else {
                                    array_push($data_element_diff, $item);
                                }
                            }
                            usort($data_item, array($this, 'mmcp_sort_array'));
                            $new_column_data = array_merge($data_item, $data_element_diff);
                            $sub_layout['sub_layout'][$index_old_row]['data'][$index_old_column]['data'] = $new_column_data;
                            update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                            wp_send_json_success(array('message' => __('Sort widget item success!', 'mmcp')));
                        } else {
                            $index_old_row = null;
                            $index_old_column = null;
                            $index_current_row = null;
                            $index_current_column = null;
                            $this->get_data_item_column($layout, $index_old_row, $index_old_column, $old_row_id, $old_column_id);

                            $row_old_data = $layout[$index_old_row];
                            $columns_old_data = $row_old_data['data'][$index_old_column];
                            $_old_order = 0;
                            $data_item_old = array();
                            $data_element_diff_old = array();
                            $new_column_data_old = array();
                            
                            foreach ($columns_old_data['data'] as $_key => $_item) {
                                if ($_item['type'] == self::DEFAULT_TYPE_ITEM ) {
                                    if ($_item['id'] === $item_widget_id) continue;
                                    $_item['order'] = $_old_order;
                                    array_push($data_item_old, $_item);
                                    $_old_order++;
                                } else {
                                    array_push($data_element_diff_old, $_item);
                                }
                            }
                            $new_column_data_old = array_merge($data_item_old, $data_element_diff_old);
                            $sub_layout['sub_layout'][$index_old_row]['data'][$index_old_column]['data'] = $new_column_data_old;
                            $data_item_current = array();
                            $data_element_diff_current = array();
                            $new_column_data_current = array();
                            $_current_order = 0;
                            $this->get_data_item_column($layout, $index_current_row, $index_current_column, $current_row_id, $current_column_id);
                            $row_current_data = $layout[$index_current_row];
                            $columns_current_data = $row_current_data['data'][$index_current_column];
                            $max_item = $columns_current_data['max_item'];
                            $has_update = false;
                            $order_status = false;
                            foreach ($columns_current_data['data'] as $key => $item) {
                                if ($item['type'] == self::DEFAULT_TYPE_ITEM) {
                                    if ($item['order'] == $current_index_item && !$has_update) {
                                        array_push($data_item_current, $item_obj);
                                        $has_update = true;
                                    }
                                    $item['order'] = $_current_order;
                                    if ($has_update && !$order_status) {
                                        $_current_order += 1;
                                        $item['order'] = $_current_order;
                                        $order_status = true;
                                    }
                                    array_push($data_item_current, $item);
                                    $_current_order++;
                                } else {
                                    array_push($data_element_diff_current, $item);
                                }
                            }
                            if (!$has_update) {
                                array_push($data_item_current, $item_obj);
                            }
                            $new_column_data_current = array_merge($data_item_current, $data_element_diff_current);
                            $sub_layout['sub_layout'][$index_current_row]['data'][$index_current_column]['max_item'] = $max_item+1;
                            $sub_layout['sub_layout'][$index_current_row]['data'][$index_current_column]['data'] = $new_column_data_current;
                            update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                            wp_send_json_success(array('message' => __('Sort widget item success!', 'mmcp')));
                        }
                    } else {
                        wp_send_json_error(array('message' => __('Could\'t sortable widget item', 'mmcp')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Could\'t sortable widget item', 'mmcp')));
                }
            } else {
                 wp_send_json_error(array('message' => __('Could\'t sortable widget item', 'mmcp')));
            }
        }

        /**
         * Deletes a widget from WordPress
         */
        public function mmcp_delete_item_widget() {
            $id_base = $this->mmcp_get_request('id_base');
            $widget_id = $this->mmcp_get_request('widget_id');
            check_ajax_referer( 'mmcp_delete_widget_' . $widget_id );

            $menu_id = $this->mmcp_get_request('menu_id');
            $menu_item_id = $this->mmcp_get_request('menu_item_id');
            $row_id = $this->mmcp_get_request('row_id');
            $column_id = $this->mmcp_get_request('column_id');
            $item_widget_id = $this->mmcp_get_request('item_widget_id');
            if (!$id_base || !$menu_item_id || !$widget_id || !$row_id || !$column_id) {
                wp_send_json_error(array('message' => __('Failed to delete widget', 'mmcp')));
            }

            if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Failed to delete widget', 'mmcp')));
            }

            $widget_option = get_option('widget_'.$id_base);
            $widget_index = array_pop(explode('-', $widget_id));

            $index_row = null;
            $index_column = null;
            if (is_array($sub_layout)) {
                if (isset($sub_layout['sub_layout'])) {
                    $layout = $sub_layout['sub_layout'];
                    if(count($layout)) {
                        $this->get_data_item_column($layout, $index_row, $index_column, $row_id, $column_id);
                        $row_data = $layout[$index_row];
                        $columns_data = $row_data['data'][$index_column];
                        $data_item = array();
                        $__order = 0;
                        $has_delete = false;
                        //usort($columns_data['data'], array($this, 'mmcp_sort_array'));
                        $data_element_diff = array();
                        $new_column_data = array();
                        foreach ($columns_data['data'] as $key => $item) {
                            if ($item['type'] == self::DEFAULT_TYPE_ITEM) {
                                if ($item['id'] == $item_widget_id && $item['cate'] == self::DEFAULT_CATE_ITEM && $item['cate_id'] == $widget_id) {
                                    $has_delete = true;
                                    continue;
                                }
                                $item['order'] = $__order;
                                array_push($data_item, $item);
                                $__order++;
                            } else {
                                array_push($data_element_diff, $item);
                            }
                        }
                        $new_column_data = array_merge($data_item, $data_element_diff);
                        $sub_layout['sub_layout'][$index_row]['data'][$index_column]['data'] = $new_column_data;
                        update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                        if (isset($widget_option[$widget_index]) && $has_delete) {
                           unset($widget_option[$widget_index]);
                           update_option('widget_'.$id_base, $widget_option);
                        }                        
                        wp_send_json_success(array('message' => __('Delete widget success!', 'mmcp')));
                    }
                } else {
                    wp_send_json_error(array('message' => __('Failed to delete widget', 'mmcp')));
                }
            } else {
                wp_send_json_error(array('message' => __('Failed to delete widget', 'mmcp')));
            }
            
        }

        /**
         * Save a item widget
         *
         * @since 1.0
         */
        public function update_item_widget() {
            $widget_id = $this->mmcp_get_request('widget_id');
            $id_base = $this->mmcp_get_request('id_base');

            check_ajax_referer( 'mmcp_save_widget_' . $widget_id );

            $update = $this->mmcp_update_widget( $id_base );
            if ( $update ) {
                wp_send_json_success(array('message' => __('Widget saved success', 'mmcp')));
            } else {
                wp_send_json_error(array('message' => __('Failed to save widget', 'mmcp')));
            }
        }

        /**
         * Saves a widget. Calls the update callback on the widget.
         * The callback inspects the post values and updates all widget instances which match the base ID.
         *
         * @since 1.0
         * @param string $id_base - e.g. 'meta'
         * @return bool
         */
        private function mmcp_update_widget( $id_base ) {
            global $wp_registered_widget_updates;
            $control = $wp_registered_widget_updates[$id_base];
            
            if ( is_callable( $control['callback'] ) ) {
                call_user_func_array( $control['callback'], $control['params'] );
                return true;
            }
            return false;
        }

        /**
         * @param String $param
         * @return any
         */
        private function mmcp_get_request($param) {
        	try {
	        	if (isset($_POST[$param])) {
	        		return sanitize_text_field($_POST[$param]);
	        	} elseif ( isset($_GET[$param])) {
	        		return sanitize_text_field($_GET[$param]);
	        	} else {
	        		throw new Exception("Params {$param} not exists ", 1);
	        	}
        	} catch (Exception $e) {
        		$error = 'Caught exception: '.  $e->getMessage()."\n";
        		wp_die($error);
        	}
        }

        /**
         * @param array $a
         * @param array $b
         * @return int
         */
        public function mmcp_sort_array ($a, $b) {
        	try {
        		if (!isset($a['order']) || !isset($b['order'])) {
        			throw new Exception("Error Processing sort", 1);
        		}
			    if ($a['order'] == $b['order']) {
			        return 0;
			    }
			    return ($a['order'] < $b['order']) ? -1 : 1;
        	} catch (Exception $e) {
        		$error = 'Caught exception: '.  $e->getMessage()."\n";
        		wp_die($error);
        	}
        }

        /**
         * Get data item column
         *
         * @param array $layout
         * @param int $index_row
         * @param int $row_id
         * @return void
         */
        private function get_data_column_row($layout, &$index_row, $row_id) {
            $row_data = array();
            try {
                if(count($layout)) {
                    $row_exists = false;
                    $column_exists = false;
                    foreach($layout as $key => $value) {
                        if ($value['type'] == self::DEFAULT_TYPE_ROW && $value['id'] == $row_id) {
                            $row_exists = true;
                            $index_row = $key;
                            break;
                        }
                    }
                    if ($row_exists && $index_row !== null) {
                        $row_data = $layout[$index_row];
                    } else {
                        throw new Exception("Error Row Id not exists", 1);
                    }
                } else {
                    throw new Exception("Error Layout empty", 1);
                }
            } catch (Exception $e) {
                $error = 'Caught exception: '.$e->getMessage()."\n";
                wp_send_json_error(array('message' => __($error)));
            }
        }


        /**
         * Get data item column
         *
         * @param array $layout
         * @param int $index_row
         * @param int $index_column
         * @param int $row_id
         * @param int $column_id
         * @return void
         */
        private function get_data_item_column($layout, &$index_row, &$index_column, $row_id, $column_id) {
            try {
                if(count($layout)) {
                    $row_exists = false;
                    $column_exists = false;
                    foreach($layout as $key => $value) {
                        if ($value['type'] == self::DEFAULT_TYPE_ROW && $value['id'] == $row_id) {
                            $row_exists = true;
                            $index_row = $key;
                            break;
                        }
                    }
                    if ($row_exists && $index_row !== null) {
                        $row_data = $layout[$index_row];
                        foreach ($row_data['data'] as $key => $value) {
                            if ($value['type'] == self::DEFAULT_TYPE_COLUMN && $value['id'] == $column_id) {
                                $column_exists = true;
                                $index_column = $key;
                                break;
                            }
                        }
                        if(!$column_exists) {
                            throw new Exception("Error Column Id not exists", 1);
                        }
                    } else {
                        throw new Exception("Error Row Id not exists", 1);
                    }
                } else {
                    throw new Exception("Error Layout empty", 1);
                }
            } catch (Exception $e) {
                $error = 'Caught exception: '.$e->getMessage()."\n";
                wp_send_json_error(array('message' => __($error)));
            }
        }

        /**
         * add new item widget
         */
        public function add_new_item_widget() {
        	check_ajax_referer('mmcp_check_ajax_add_item_widget', 'mmcp_add_widget_noce');
        	require_once( ABSPATH . 'wp-admin/includes/widgets.php' );
        	$menu_item_id = $this->mmcp_get_request('menu_item_id');
        	$widget_id = $this->mmcp_get_request('widget_id');
        	$current_index_item = $this->mmcp_get_request('current_index_item');
        	$last_item_index = $this->mmcp_get_request('last_item_index');
        	//$order_row = $this->mmcp_get_request('order_row');
        	//$order_column = $this->mmcp_get_request('order_column');
        	$row_id = $this->mmcp_get_request('row_id');
        	$column_id = $this->mmcp_get_request('column_id');        	
        	$type_item = $this->mmcp_get_request('type_item');
            if ($widget_id) {
                $next_widget_id = next_widget_id_number( $widget_id );
            } else {
                wp_send_json_error(array('message' => __('Could\'t add widget item', 'mmcp')));
            }
        	if ($menu_item_id) {
                $sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
            } else {
                wp_send_json_error(array('message' => __('Could\'t add widget item', 'mmcp')));
            }

            if (!$row_id || !$column_id ) {
                wp_send_json_error(array('message' => __('Could\'t add widget item', 'mmcp')));
            }
        	
        	$row_exists = false;
        	$column_exists = false;
        	$index_row = null;
        	$index_column = null;
        	$has_update = false;
        	if (is_array($sub_layout)) {
        		if (isset($sub_layout['sub_layout'])) {
        			$layout = $sub_layout['sub_layout'];
        			if(count($layout)) {
                        $this->get_data_item_column($layout, $index_row, $index_column, $row_id, $column_id);
                        $row_data = $layout[$index_row];
                        $widget_option = get_option("widget_{$widget_id}");
                        $widget_option[$next_widget_id] = array();
                        update_option("widget_{$widget_id}", $widget_option);
                        $columns_data = $row_data['data'][$index_column];
                        $row_id_number = array_pop(explode('_',$row_id));
                        $column_id_number = array_pop(explode('_',$column_id));
                        $data_item = array();
                        $data_element_diff = array();
                        $new_column_data = array();
                        $__order = 0;
                        $last_item_index = $columns_data['max_item'];
                        $item_id = $last_item_index + 1;
                        $order_status = false;
                        $item_obj = array('id' => "item_{$menu_item_id}_{$row_id_number}_{$column_id_number}_{$item_id}", 'type' => 'item', 'order' => $current_index_item, 'cate' => $type_item, 'cate_id' => "{$widget_id}-{$next_widget_id}");
                        foreach ($columns_data['data'] as $key => $value) {
                            if ($value['type'] == self::DEFAULT_TYPE_ITEM) {
                                if ($value['order'] == $current_index_item && !$has_update) {
                                    array_push($data_item, $item_obj);
                                    $has_update = true;
                                }
                                $value['order'] = $__order;
                                if($has_update && !$order_status) {
                                    $__order += 1;
                                    $value['order'] = $__order;
                                    $order_status = true;
                                }
                                $__order++;
                                array_push($data_item, $value);
                            } else {
                                array_push($data_element_diff, $value);
                            }
                        }
                        if (!$has_update) {
                            array_push($data_item, $item_obj);
                        }
                        usort($data_item, array($this, 'mmcp_sort_array'));
                        $new_column_data = array_merge($data_item, $data_element_diff);
                        $sub_layout['sub_layout'][$index_row]['data'][$index_column]['max_item'] = $item_id;
                        $sub_layout['sub_layout'][$index_row]['data'][$index_column]['data'] = $new_column_data;
                        update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
                        wp_send_json_success(array('message' => __('Add widget success!', 'mmcp')));
        				/*foreach($layout as $key => $value) {
        					if ($value['type'] == 'row' && $value['id'] == $row_id) {
    							$row_exists = true;
    							$index_row = $key;
    							break;
        					}
        				}
        				if ($row_exists && $index_row !== null) {
        					$row_data = $layout[$index_row];
        					foreach ($row_data['data'] as $key => $value) {
        						if ($value['type'] == 'column' && $value['id'] == $column_id) {
									$column_exists = true;
									$index_column = $key;
									break;
        						}
        					}
        					if($column_exists && $index_column !== null) {
					            $widget_option = get_option("widget_{$widget_id}");
					            $widget_option[$next_widget_id] = array();
					            update_option("widget_{$widget_id}", $widget_option);
        						$columns_data = $row_data['data'][$index_column];
        						$item_id = $last_item_index + 1;
        						$row_id_number = array_pop($row_idarr);
        						$column_id_number = array_pop($column_idarr);
        						$item_obj = array('id' => "item_{$menu_item_id}_{$row_id_number}_{$column_id_number}_{$item_id}", 'type' => 'item', 'order' => $current_index_item, 'cate' => $type_item, 'cate_id' => "{$widget_id}-{$next_widget_id}");
        						$data_item = array();
        						$data_element_diff = array();
        						$new_column_data = array();
        						$__order = 0;
        						foreach ($columns_data['data'] as $key => $value) {
        							if ($value['type'] == 'item') {
        								if ($value['order'] == $current_index_item && !$has_update) {
        									array_push($data_item, $item_obj);
        									$has_update = true;
        								}
        								$value['order'] = $__order;
        								if($has_update) {
        									$value['order'] = $__order + 1;
        								}
        								$__order++;
        								array_push($data_item, $value);
        							} else {
        								array_push($data_element_diff, $value);
        							}
        						}
        						if (!$has_update) {
        							array_push($data_item, $item_obj);
        						}
        						usort($data_item, array($this, 'mmcp_sort_array'));
        						$new_column_data = array_merge($data_item, $data_element_diff);
        						$sub_layout['sub_layout'][$index_row]['data'][$index_column]['data'] = $new_column_data;
        						update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
        						wp_send_json_success(array('message' => __('Add widget success!')));
        					} else {
        						wp_send_json_error(array('message' => __('Could\'t add widget item')));
        					}
        				} else {
							wp_send_json_error(array('message' => __('Could\'t add widget item')));
        				}*/
        			} else {
						wp_send_json_error(array('message' => __('Could\'t add widget item', 'mmcp')));
        			}
        		}
        	} else {
        		wp_send_json_error(array('message' => __('Could\'t add widget item', 'mmcp')));
        	}
        }

        /**
         * @param $widget_id
         * @return bool|string
         */
        private function mmcp_get_widget_name_by_widget_id( $widget_id ) {
            global $wp_registered_widget_controls;
            if ( ! isset( $wp_registered_widget_controls[ $widget_id ] ) ) {
                return false;
            }
            $control = $wp_registered_widget_controls[ $widget_id ];
            $name = isset( $control['name'] ) ? $control['name'] : '';
            return $name;
        }

        /**
         * @param $widget_id
         *
         * Generate Widget form.
         *
         * @since v.1.0
         */
        public function show_content_widget_form( $widget_id ) {
            global $wp_registered_widget_controls;
            $control = $wp_registered_widget_controls[$widget_id];

            if ( is_callable( $control['callback'] ) ) {
                call_user_func_array( $control['callback'], $control['params'] );
            }
        }

        /**
         * @param $widget_id
         * @return bool
         *
         * Get base widget id
         */
        public function mmcp_get_id_base_for_widget_id( $widget_id ) {
            global $wp_registered_widget_controls;

            if ( ! isset( $wp_registered_widget_controls[ $widget_id ] ) ) {
                return false;
            }
            $control = $wp_registered_widget_controls[ $widget_id ];
            $id_base = isset( $control['id_base'] ) ? $control['id_base'] : $control['id'];
            return $id_base;
        }        

        /**
         * @param array $item
         * @return string
         */
        protected function mmcp_get_widget_name($item) {
        	try {
	        	if ($item['type'] == 'item' && $item['cate'] == 'widget' && isset($item['cate_id'])) {
	        		return $this->mmcp_get_widget_name_by_widget_id($item['cate_id']);
	        	} else {
	        		throw new Exception("Error Item not widget");
	        	}  
        	} catch (Exception $e) {
        		$error = 'Caught exception: '.$e->getMessage()."\n";
        		wp_die($error);
        	}

        }

        /**
         * @param array $item
         * @param int $type
         * @return int
         */
        protected function get_config_width_value($item, $type = 1) {
        	try {
	        	if (isset($item['config']) && isset($item['config']['width'])) {
	        		$width = $item['config']['width'];
	        		$default = $type ? self::DEFAULT_ROW_WIDTH : self::DEFAULT_COLUMN_WIDTH;
	        		return $width ? $width : $default;
	        	} else {
	        		throw new Exception("Error Not get value width");
	        	}   		
        	} catch (Exception $e) {
        		$error = 'Caught exception: '.$e->getMessage()."\n";
        		wp_die($error);
        	}
        }

        /**
		 * @param string $id
         * @param int $old_id
		 * @return void
         */
        protected function get_max_element_id($id, &$old_id) {
        	if ($id) {
        		$_id_number = array_pop(explode('_', $id));
        		$old_id = ($_id_number > $old_id) ? $_id_number : $old_id;
        	}
        }

        /**
         * Build Struct data for Column
         *
         * @param int $menu_item_id
         * @return Array
         */
        protected function get_struct_data_column($menu_item_id, $row_id, $index) {
            $column_data = array(
                'type' => 'column',
                'id' => "column_{$menu_item_id}_{$row_id}_{$index}",
                'order' => 0,
                'max_item' => 0,
                'config' => array(
                    'width' => '',
                    'class' => '',
                    'hide_on_mobile' => 0,
                    'hide_on_desktop' => 0
                ),
                'data' => array()
            );

            return $column_data;
        }

        /**
         * Build Struct data for Row
         *
         * @param int $menu_item_id
         * @return Array
         */
        protected function get_struct_data_row($menu_item_id, $index) {
            $row_data = array(
                'type' => 'row',
                'id' => "row_{$menu_item_id}_{$index}",
                'order' => 0,
                'max_column' => 1,
                'config' => array(
                    'width' => '',
                    'class' => '',
                    'hide_on_mobile' => 0,
                    'hide_on_desktop' => 0
                ),
                'data' => array()
            );
            $column_data = $this->get_struct_data_column($menu_item_id, $index, 1);
            array_push($row_data['data'], $column_data);

            return $row_data;
        }

        /**
         * Render layout submenu
         */
        public function load_item_menu_layout() {
        	check_ajax_referer( 'mmcp_check_ajax_layout_item_menu_security', 'mmcp_tab_nonce' );
        	$menu_id = $this->mmcp_get_request('menu_id');
        	$menu_item_depth = $this->mmcp_get_request('menu_item_depth');
        	$menu_item_id = $this->mmcp_get_request('menu_item_id');
        	$reload_layout = $this->mmcp_get_request('reload_layout');
        	$sub_layout = get_post_meta($menu_item_id, 'mmcp_sub_layout', true);
        	if (!is_array($sub_layout)) {
                $sub_layout = array(
                    'custom_class' => '',
                    'width_submenu' => '',
                    'background_image_submenu' => '',
                    'effect' => '',
                    'hide_on_mobile' => 0,
                    'hide_on_desktop' => 0,
                    'item_align' => 'left',
                    'dropdown_align' => 'right',
                    'hide_sub_menu_on_mobile' => 0,
                    'icon' => '',
                    'icon_position' => 'left',
                    'max_row' => '1',
                    'sub_layout' => array()
                );
                $row_struct_data = $this->get_struct_data_row($menu_item_id, 1);
                array_push($sub_layout['sub_layout'], $row_struct_data);
        		/*$sub_layout = array(
        			'custom_class' => '',
        			'width_submenu' => '',
        			'background_image_submenu' => '',
        			'effect' => '',
        			'hide_on_mobile' => 0,
        			'hide_on_desktop' => 0,
        			'item_align' => 'left',
        			'dropdown_align' => 'right',
        			'hide_sub_menu_on_mobile' => 0,
        			'icon' => '',
        			'icon_position' => 'left',
                    'max_row' => '1',
        			'sub_layout' => array(
        				array(
        					'type' => 'row',
        					'id' => "row_{$menu_item_id}_1",
        					'order' => 0,
                            'max_column' => 1,
        					'config' => array(
        						'width' => '',
        						'class' => '',
        						'hide_on_mobile' => 0,
        						'hide_on_desktop' => 0
        					),
        					'data' => array(
        						array(
        							'type' => 'column',
        							'id' => "column_{$menu_item_id}_1_1",
        							'order' => 0,
                                    'max_item' => 1,
        							'config' => array(
		        						'width' => '',
		        						'class' => '',
		        						'hide_on_mobile' => 0,
		        						'hide_on_desktop' => 0
        							),
        							'data' => array()
        						)
        					)
        				)
        			)
        		);*/
				update_post_meta($menu_item_id, 'mmcp_sub_layout', $sub_layout);
        	}
        	echo '<div><div id="ajax_response_layout">';
        	include_once(MMCPRO()->plugin_path().'/admin/views/menus/sub_layout.php');
        	if ($reload_layout == 2) {
        		echo '<div id="ajax_response_settings"></div>';
        	}
        	echo '</div>';
        	wp_die();
        }
	}
	MMCP_Manager_Widget::instance();
}