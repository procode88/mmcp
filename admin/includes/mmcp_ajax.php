<?php
/**
 * MMCP Handling Ajax
 */
if ( ! defined( 'ABSPATH' ) ) {
 	header('HTTP/1.0 403 Forbidden');
    exit; // disable direct access
}
if (!class_exists('MMCP_Ajax'))
{
	class MMCP_Ajax {

		protected static $_instance = null;

		protected $authors_arr = array();

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
			add_action('wp_ajax_mmcp_menu_detail', array($this, 'load_menu_detail'));
			add_action('wp_ajax_mmcp_tab_data', array($this, 'load_tab_data'));
		}

		/**
		 *
		 * Load detail menu
		 */
		public function load_menu_detail() {
			check_ajax_referer( 'mmcp_check_ajax_menu_detail_security', 'mmcp_nonce' );
		}

		/**
		 * 
		 * @param String $callback
		 * @param String $title
		 * @return Array
		 */
		private function get_data_pages_post_categories_tags($callback, $title) {
			global $wp_meta_boxes;
	        wp_nav_menu_setup();
	        wp_initial_nav_menu_meta_boxes();
			$continue = true;
			$result = array();
			if (!$callback || !$title) return $result;
			if (isset( $wp_meta_boxes['nav-menus']['side'])) {
				foreach ($wp_meta_boxes['nav-menus']['side'] as $key => $value) {
					
					if (in_array($key, array('high', 'core', 'default', 'low')) &&
						isset($wp_meta_boxes['nav-menus']['side'][$key])
					) {
						
						foreach ( $wp_meta_boxes['nav-menus']['side'][$key] as $box ) {
							if (false == $box || !$box['title'])
								continue;
							if ($box['callback'] == $callback && $callback == 'wp_nav_menu_item_post_type_meta_box' && $box['title'] == $title) {
								$result = $this->_get_data_pages_post($box);
								break;
							} elseif( $box['callback'] == $callback && $callback == 'wp_nav_menu_item_taxonomy_meta_box' && $box['title'] == $title) {
								$result = $this->_get_data_categories_tags($box);
								break;
							}
						}
						//if (!$continue) break;
					}
				}
			}
			
			return $result;
		}

		/**
		 * @param object $item   Menu item data object.
		 * @return array
		 */
		private function build_hidden_input($item) {
			global $_nav_menu_placeholder;
			$output = '';
			$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
			$possible_object_id = isset( $item->post_type ) && 'nav_menu_item' == $item->post_type ? $item->object_id : $_nav_menu_placeholder;
			//$possible_db_id = ( ! empty( $item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $item->ID : 0;
			$possible_db_id = $item->ID;

			$menu_item = array('menu_item' => array($possible_object_id => 
				array(
					'menu-item-object-id' => esc_attr( $item->object_id ),
					'menu-item-db-id' => $possible_db_id,
					'menu-item-object' => esc_attr( $item->object ),
					'menu-item-parent-id' => esc_attr( $item->menu_item_parent ),
					'menu-item-type' => esc_attr( $item->type ),
					'menu-item-title' => esc_attr( $item->title ),
					'menu-item-url' => esc_attr( $item->url ),
					'menu-item-target' => esc_attr( $item->target ),
					'menu-item-attr_title' => esc_attr( $item->attr_title ),
					'menu-item-classes' => esc_attr( implode( ' ', $item->classes ) ),
					'menu-item-xfn' => esc_attr( $item->xfn )
				)
			));

			return $menu_item;
		}

		/**
	     * Build Data response
	     * @param object $element
	     * @param array $_authorIds
	     * @return array
		 */
		private function build_data_response($element, &$_authorIds) {
			if (!is_object($element)) return array();
			$id = $element->ID;
			$input_hidden = $this->build_hidden_input($element);
			$item = array(
				'id' => $id, 
				'item_select' => false, 
				'title' => $element->title, 
				'item_slug' => '',
				'input_hidden' => $input_hidden
			);
			if($element->type === 'post_type') {
				$author_name = get_the_author_meta('display_name', $element->post_author);
				$item['author'] = ($element->post_type == 'page')? $author_name : (int)$element->post_author;
				$item['public_date'] = $element->post_date;
				$item['item_slug'] = $element->post_name;
			} elseif($element->type === 'taxonomy') {
				$original_title = get_term_field( 'name', $element->object_id, $element->object, 'raw' );
				$item['description'] = $element->description;
				$item['item_slug'] = $element->slug;
			} elseif($element->type === 'custom') {

			} elseif($element->type === 'post_type_archive') {

			}

			if ($element->post_type !== 'page' && is_array($_authorIds) && (!count($_authorIds) || !in_array($element->post_author, $_authorIds))) {
				array_push($_authorIds, $element->post_author);
				array_push($this->authors_arr, array('id' => $element->post_author, 'name' => $author_name));
			}
			return $item;
		}

		/**
	     * Parse Data
	     * @param object $elements
	     * @return array
		 */
		private function parse_data($elements) {
			$_data = array();
			$_authorIds = array();
			if(count($elements)) {
				$parent_field = 'post_parent';
				/*
				 * Need to display in hierarchical order.
				 * Separate elements into two buckets: top level and children elements.
				 * Children_elements is two dimensional array, eg.
				 * Children_elements[10][] contains all sub-elements whose parent is 10.
				 */
				$top_level_elements = array();
				$children_elements  = array();
				foreach ( $elements as $e) {
					if ( empty( $e->$parent_field ) )
						$top_level_elements[] = $e;
					else
						$children_elements[ $e->$parent_field ][] = $e;
				}

				foreach ($top_level_elements as $post_data){
					$id = $post_data->ID;
					$item = $this->build_data_response($post_data, $_authorIds);
					array_push($_data, $item);
					unset($item);
					foreach ( $children_elements[ $id ] as $child ){
						$item = $this->build_data_response($child, $_authorIds);
						array_push($_data, $item);
						unset($item);
					}
				}
			}
			return $_data;
		}
		/**
		 * Get All data catgories and tags
		 * @param object $box
		 * @return array
		 */
		private function _get_data_categories_tags($box) {
			$reponse = array();
			$taxonomy_name = $box['args']->name;
			// Paginate browsing for large numbers of objects.
			$per_page = 100;
			$pagenum = 1;
			$offset = 0 ;

			$args = array(
				'child_of' => 0,
				'exclude' => '',
				'hide_empty' => false,
				'hierarchical' => 1,
				'include' => '',
				'number' => $per_page,
				'offset' => $offset,
				'order' => 'ASC',
				'orderby' => 'name',
				'pad_counts' => false,
			);

			$terms = get_terms( $taxonomy_name, $args );

			if ( ! $terms || is_wp_error($terms) ) {
				return $reponse;
			}
			$db_fields = false;
			if ( is_taxonomy_hierarchical( $taxonomy_name ) ) {
				$db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
			}

			$walker = new Walker_Nav_Menu_Checklist( $db_fields );
			$args['walker'] = $walker;
			$reponse = $this->parse_data(array_map('wp_setup_nav_menu_item', $terms));
			return array('data' => $reponse, 'num_pages' => $num_pages, 'item_per_page' => $per_page);

		}

		/**
		 * Get All data pages and post
		 * @param object $box
		 * @return array
		 */
		private function _get_data_pages_post($box) {
			global $_nav_menu_placeholder;
			$reponse = array();
			$post_type_name = $box['args']->name;
			$per_page = 100;
			$pagenum = 1;
			$offset = 0 ;
			$args = array(
				'offset' => $offset,
				'order' => 'ASC',
				'orderby' => 'title',
				'posts_per_page' => $per_page,
				'post_type' => $post_type_name,
				'suppress_filters' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false
			);
			if ( isset( $box['args']->_default_query ) )
				$args = array_merge($args, (array) $box['args']->_default_query );
			// @todo transient caching of these results with proper invalidation on updating of a post of this type
			$get_posts = new WP_Query;
			$posts = $get_posts->query( $args );
			
			if ( ! $get_posts->post_count ) {
				return $reponse;
			}

			$num_pages = $get_posts->max_num_pages;
			$db_fields = false;
			if ( is_post_type_hierarchical( $post_type_name ) ) {
				$db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
			}
			$walker = new Walker_Nav_Menu_Checklist( $db_fields );	
			$args['walker'] = $walker;

			/*
			* If we're dealing with pages, let's put a checkbox for the front
			* page at the top of the list.
			*/
			if ( 'page' == $post_type_name ) {
				$front_page = 'page' == get_option('show_on_front') ? (int) get_option( 'page_on_front' ) : 0;
				if ( ! empty( $front_page ) ) {
					$front_page_obj = get_post( $front_page );
					$front_page_obj->front_or_home = true;
					array_unshift( $posts, $front_page_obj );
				} else{
					$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
				}
			}


			$post_type = get_post_type_object( $post_type_name );
			if ( $post_type->has_archive ) {
				$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
				array_unshift( $posts, (object) array(
					'ID' => 0,
					'object_id' => $_nav_menu_placeholder,
					'object'     => $post_type_name,
					'post_content' => '',
					'post_excerpt' => '',
					'post_title' => $post_type->labels->archives,
					'post_type' => 'nav_menu_item',
					'type' => 'post_type_archive',
					'url' => get_post_type_archive_link( $post_type_name ),
				) );
			}
			/**
			 * Filter the posts displayed in the 'View All' tab of the current
			 * post type's menu items meta box.
			 *
			 * The dynamic portion of the hook name, `$post_type_name`, refers
			 * to the slug of the current post type.
			 *
			 * @since 3.2.0
			 *
			 * @see WP_Query::query()
			 *
			 * @param array  $posts     The posts for the current post type.
			 * @param array  $args      An array of WP_Query arguments.
			 * @param object $post_type The current post type object for this menu item meta box.
			 */
			$posts = apply_filters( "nav_menu_items_{$post_type_name}", $posts, $args, $post_type );

			$reponse = $this->parse_data(array_map('wp_setup_nav_menu_item', $posts));
			return array('data' => $reponse, 'num_pages' => $num_pages, 'item_per_page' => $per_page);
		}

		/**
	     * Handling request ajax
	     * @return data type json
		 */
		public function load_tab_data() {
			check_ajax_referer( 'mmcp_check_ajax_tab_data_security', 'mmcp_nonce' );
			$response = array('data' => array(), 'num_pages' => 1, 'item_per_page' => 10);
			if (isset($_REQUEST['tab'])) {
				switch ($_REQUEST['tab']) {
					case 'Pages':
						$data = $this->get_data_pages_post_categories_tags('wp_nav_menu_item_post_type_meta_box', 'Pages');
						$response['data'] = $data['data'];
						$response['num_pages'] = $data['num_pages'];
						$response['item_per_page'] = $data['item_per_page'];					
						break;
					case 'Posts':
						$data = $this->get_data_pages_post_categories_tags('wp_nav_menu_item_post_type_meta_box', 'Posts');
						$response['data'] = $data['data'];
						$response['num_pages'] = $data['num_pages'];
						$response['item_per_page'] = $data['item_per_page'];
						if (count($this->authors_arr)) {
							$response['authors'] = $this->authors_arr;
							$this->authors_arr = array();
						}
						break;
					case 'Categories':
						$data = $this->get_data_pages_post_categories_tags('wp_nav_menu_item_taxonomy_meta_box', 'Categories');
						$response['data'] = $data['data'];
						$response['num_pages'] = $data['num_pages'];
						$response['item_per_page'] = $data['item_per_page'];
						break;
					case 'Tags':
						$data = $this->get_data_pages_post_categories_tags('wp_nav_menu_item_taxonomy_meta_box', 'Tags');
						$response['data'] = $data['data'];
						$response['num_pages'] = $data['num_pages'];
						$response['item_per_page'] = $data['item_per_page'];					
						break;
					case 'Customlinks':
						break;
					default:
						$data = array();
				}
			}
			wp_send_json_success( json_encode( $response ) );
		}
	}
	MMCP_Ajax::instance();
}