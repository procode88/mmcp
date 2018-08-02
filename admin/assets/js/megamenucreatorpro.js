/**
 * Mega Menu Creator Pro jQuery Plugin
 */
(function($) {
	"use strict";
	$.fn.megaMenuCreatorPro = function(options) {
		var plugin = $('<div />'), tab_active = [];
		var settings = $.extend({
			menu_item_id: null,
			menu_item_depth: 0,
			menu_id: null,
			menu_item_title: '',
			tab_id: ''
		}, options);
		var load_detail_menu = function() {
			var mmcp = {};
			$.post(ajaxurl, {
				action: 'mmcp_menu_detail',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				menu_item_depth: settings.menu_item_depth,
				mmcp_nonce: mmcp.mmcp_nonce
			}, function(data){
				$('[id=mmcpModalLabel]').text(settings.menu_item_title);
				$('.mmcp-sidebar.mmcp-menuwidget-layout .load_content').hide();
			}, 'json');
		};
		$(".mmcp-menuwidget-layout").trigger("sidebar:open")
		.on('sidebar:opened', function(event){
			$('.mmcp-sidebar.mmcp-menuwidget-layout .load_content').show();
			if( tab_active.indexOf(settings.tab_id) == -1) {
				tab_active.push(settings.tab_id);
				tab_active.push('mmcpsettings');
				disable_all_tabs();
				$('[id='+settings.tab_id+']').removeClass('disabled');
				load_content_layout();
			}
		})
		.on('sidebar:closed', function(event){
			$('.mmcp-sidebar.mmcp-menuwidget-layout .mmcp-tab-content > div.tab-pane').empty();
			$('[id=asideOverlay]').hide();
		});
		$(".mmcp-sidebar.mmcp-menuwidget-layout button.mmcp-close").on('click', function(event){
			$(".mmcp-sidebar.mmcp-menuwidget-layout").trigger("sidebar:close");
		});

		$('.mmcp-menuwidget-layouts a[data-toggle="tab"]').on('show.bs.tab', function(event){
			disable_all_tabs();
		}).on('shown.bs.tab', function(event){
			var element_id = $(this).attr('href').split('#')[1];
			$(this).removeClass('disabled');
			if (tab_active.indexOf(element_id) == -1) {
				tab_active.push(element_id);
			} else {
				remove_disable_all_tabs();
			}
		});

		function disable_all_tabs() {
			$('.mmcp-menuwidget-layouts a[data-toggle="tab"]').addClass('disabled');
		}

		function remove_disable_all_tabs() {
			$('.mmcp-menuwidget-layouts a[data-toggle="tab"]').removeClass('disabled');
		}

		function add_drag_widget_item(dataparam) {
			dataparam = dataparam || {};
			var params = {
				action: 'mmcp_add_item_widget',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				widget_id: dataparam.widget_id,
				current_index_item: dataparam.current_index_item,
				last_item_index: dataparam.last_item_index,
				row_id: dataparam.row_id,
				column_id: dataparam.column_id,
				type_item: dataparam.type_item,
				mmcp_add_widget_noce: $('input[type="hidden"][name="mmcp_add_widget_noce"]').val()
			};
			ajax_post_server(params);
		}

		function sortable_item_widget(dataparam) {
			dataparam = dataparam || {};
			var params = {
				action: 'mmcp_sort_item_widget',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				widget_id: dataparam.widget_id,
				current_index_item: dataparam.current_index_item,
				old_row_id: dataparam.old_row_id,
				old_column_id: dataparam.old_column_id,
				current_row_id: dataparam.current_row_id,
				current_column_id: dataparam.current_column_id,
				item_widget_id: dataparam.item_widget_id,
				type_item: dataparam.type_item,
				mmcp_sortable_widget_noce: $('input[type="hidden"][name="mmcp_sortable_widget_noce"]').val()
			};
			console.log('sort item widget >>>>>>', params);
			ajax_post_server(params);
		}

	    /**
	     * Save widget input
	     */
	    $(document).on('submit', 'form.mmcp_widget_form', function (e) {
	    	e.preventDefault();
	    	var form = $(this), params = form.serialize();
	    	$(this).find('.mmcp-item-loading').show();
	    	$.post(ajaxurl, params, function(data){
				form.find('.mmcp-item-loading').hide();
	    	});
	    });
	    function ajax_post_server(params) {
	    	params = params || {};
			disable_all_tabs();
			$('[id='+settings.tab_id+']').removeClass('disabled');
			$.post(ajaxurl, params, function(data){
				if (typeof data == 'object' && data.success) {
					$('.mmcp-sidebar.mmcp-menuwidget-layout .mmcp-tab-content > [id='+settings.tab_id+']').empty();
					$('.mmcp-sidebar.mmcp-menuwidget-layout .load_content').show();
					load_content_layout(1);
				} else {
					remove_disable_all_tabs();
				}
			});
	    }

	    $(document).on('click', 'button.mmcp-add_column', function (e){
			e.preventDefault();
			var params = {
				action: 'mmcp_add_column_layout',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				current_row_id: $(this).closest('.mmcp-grid-row').attr('id'),
				mmcp_add_column_noce: $('input[type="hidden"][name="mmcp_add_column_noce"]').val()
			};
			ajax_post_server(params);
	    });

	    $(document).on('click', 'form.mmcp_widget_form a.mmcp_delete', function (e) {
			e.preventDefault();
			
			var item_id = $(this).closest('.mmcp-widget').attr('id'), form = $(this).closest('form'),
			column_id = $(this).closest('.mmcp-grid-col').attr('id'),
			row_id = $(this).closest('.mmcp-grid-row').attr('id'),
			params = {
				action: 'mmcp_delete_widget',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				id_base: form.find('input[type="hidden"][name="id_base"]').val(),
				widget_id: form.find('input[type="hidden"][name="widget-id"]').val(),
				row_id: row_id,
				column_id: column_id,
				item_widget_id: item_id,
				_wpnonce: $(this).data('deltewidget')
			};
			form.find('.mmcp-item-loading').show();
			disable_all_tabs();
			$('[id='+settings.tab_id+']').removeClass('disabled');
	    	$.post(ajaxurl, params, function(data){
				form.find('.mmcp-item-loading').hide();
				if (data.success) {
					load_content_layout(1);
				} else {
					remove_disable_all_tabs();
				}
	    	});
	    });

	    function sort_column_layout(dataparam) {
	    	dataparam = dataparam || {};
	    	var params = {
				action: 'mmcp_sort_column_layout',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				row_id: dataparam.row_id,
				column_id: dataparam.column_id,
				column_index: dataparam.column_index,
				mmcp_sort_column_noce: $('input[type="hidden"][name="mmcp_sort_column_noce"]').val()
	    	};
	    	ajax_post_server(params);
	    }

	    function add_row_layout() {
	    	var params = {
	    		action: 'mmcp_add_row_layout',
	    		menu_id: settings.menu_id,
	    		menu_item_id: settings.menu_item_id,
	    		mmcp_add_row_layout_noce: $('input[type="hidden"][name="mmcp_add_row_layout_noce"]').val()
	    	};
	    	ajax_post_server(params);
	    }

	    function sort_row_layout(dataparam) {
	    	dataparam = dataparam || {};
	    	var params = {
	    		action: 'mmcp_sort_row_layout',
	    		menu_id: settings.menu_id,
	    		menu_item_id: settings.menu_item_id,
	    		row_id: dataparam.row_id,
	    		row_index: dataparam.row_index,
	    		mmcp_sort_row_layout_noce: $('input[type="hidden"][name="mmcp_sort_row_layout_noce"]').val()
	    	};
	    	ajax_post_server(params);
	    }

		function load_content_layout(type)  {
			type = type || 2;
			$.post(ajaxurl, {
				action: 'mmcp_layout_item_menu',
				menu_id: settings.menu_id,
				menu_item_id: settings.menu_item_id,
				menu_item_depth: settings.menu_item_depth,
				reload_layout: type,
				mmcp_tab_nonce: $('input[type="hidden"][name="mmcp_tab_nonce"]').val()
			}, function(html){
				remove_disable_all_tabs();
				var layout_html = $(html).children('[id="ajax_response_layout"]').html(), settings_html;
				//console.log(JSON.parse(settings_html).clearAriaLabel);
				$('[id="'+settings.tab_id+'"]').html(layout_html);
				if (type == 2) {
					settings_html = $(html).children('[id="ajax_response_settings"]').html();
					$('[id="mmcpsettings"]').html(settings_html);
				}
				
				$('.mmcp-sidebar.mmcp-menuwidget-layout .load_content').hide();
				$('.mmcp-grid-row').sortable({
					items: ".mmcp-grid-col",
					handle: ".mmcp-icon-sort-column",
					placeholder: "mmcp-column-highlight",
					start: function(event, ui) {
						if (typeof ui.item !== 'undefined') {
							var row_id = ui.item.closest('.mmcp-grid-row').attr('id');
							ui.item.attr('data-roidcr', row_id);
						}
					},
					update: function(event, ui) {
						if (typeof ui.item !== 'undefined') {
							var row_id = ui.item.data('roidcr'),
							column_id = ui.item.attr('id'), column_index = ui.item.index();
							ui.item.removeData('roidcr');
							sort_column_layout({row_id: row_id, column_id: column_id, column_index: column_index});
						}
					}
				}).disableSelection();
				$( ".mmcp-grid-col .mmcp-col-content-widget" ).sortable({
					connectWith: '.mmcp-col-content-widget',
					items: '.mmcp-widget',
					placeholder: "mmcp-items-highlight",
					start: function(event, ui) {
						if (typeof ui.item !== 'undefined') {
							if (!ui.item.hasClass('mmcp-item-drag-widget')) {
								var row_id_current, columnt_id_current;
								row_id_current = ui.item.closest('.mmcp-grid-row').attr('id');
								columnt_id_current = ui.item.closest('.mmcp-grid-col').attr('id');
								ui.item.attr('data-roidcr', row_id_current);
								ui.item.attr('data-coidcr', columnt_id_current);
							}
						}
					},
					receive: function(event, ui) {
						if (typeof ui.item !== 'undefined') {
							ui.item.data('receive', '1');
						}
					},
					update: function(event, ui) {
						if (typeof ui.item !== 'undefined' ) {
							if (ui.item.hasClass('mmcp-item-drag-widget')) {
								var widget_id = ui.item.data('widgetid'), current_index_item = ui.item.index(), 
								last_item_index = ui.item.parent().parent().children('input[type="hidden"][name="item_max_id"]').val(),
								row_id = ui.item.closest('.mmcp-grid-row').attr('id'), column_id = ui.item.closest('.mmcp-grid-col').attr('id'), type_item = 'widget';
								add_drag_widget_item({widget_id: widget_id, current_index_item: current_index_item, last_item_index: last_item_index, row_id: row_id, column_id: column_id, type_item: type_item});
							} else {
								var old_row_id, old_column_id, current_row_id, current_column_id, current_index_item, type_item = '', item_widget_id, widget_id;
								old_row_id = current_row_id = ui.item.closest('.mmcp-grid-row').attr('id');
								old_column_id = current_column_id = ui.item.closest('.mmcp-grid-col').attr('id');
								item_widget_id = ui.item.attr('id');
								widget_id = ui.item.find('form.mmcp_widget_form').children('input[type="hidden"][name="widget-id"]').val();
								current_index_item = ui.item.index();
								type_item = ui.item.data('typeitem');
								if (ui.item.data('receive')) {
									old_row_id =  ui.item.data('roidcr');
									old_column_id = ui.item.data('coidcr');
									ui.item.removeData('receive');
								}
								sortable_item_widget({old_row_id: old_row_id, old_column_id: old_column_id, current_row_id: current_row_id, current_column_id: current_column_id, current_index_item: current_index_item, item_widget_id: item_widget_id, widget_id: widget_id, type_item: type_item});
								ui.item.removeData('roidcr');
								ui.item.removeData('coidcr');
							}
						}
					}
				}).disableSelection();
				$('.mmcp-grid-wrapper').sortable({
		            items: '.mmcp-grid-row',
		            handle: '.mmcp-icon-sort-column',
		            update: function (event, ui) {
		            	if (typeof ui.item !== 'undefined') {
		            		var row_id = ui.item.attr('id'), row_index = ui.item.index();
		            		sort_row_layout({row_id: row_id, row_index: row_index});
		            	}
		            }
				}).disableSelection();
	            $(".mmcp-list-widget li .mmcp-item-widget").draggable({
	                connectToSortable: ".mmcp-col-content-widget",
	                helper: "clone",
	                revert: "invalid",
	                revertDuration: 0
	            }).disableSelection();
                // fix for WordPress 4.8 widgets when lightbox is opened, closed and reopened
                if (typeof wp.textWidgets !== 'undefined') {
                    wp.textWidgets.widgetControls = {}; // WordPress 4.8 Text Widget
                }

                if (typeof wp.mediaWidgets !== 'undefined') {
                    wp.mediaWidgets.widgetControls = {}; // WordPress 4.8 Media Widgets
                }

                if (typeof wp.customHtmlWidgets !== 'undefined') {
                    wp.customHtmlWidgets.widgetControls = {}; // WordPress 4.9 Custom HTML Widgets
                }	            
	            $('.mmcp-widget').each(function(){
	            	var widget = $(this);
	            	$(this).find('a.mmcp_close').on('click', function(e){
	            		 e.preventDefault();
	            		$(this).closest('.mmcp-widget').removeClass('open');
	            	});
	            	$(this).find('.widget-action').on('click', function() {
	            		//var widget = $(this).closest('.mmcp-widget');
	            		if (widget.hasClass('open')) {
	            			widget.removeClass('open');
	            		} else {
	                        setTimeout(function(){
	                            $( document ).trigger("widget-added", [widget]);
	                        }, 40);
	            			widget.addClass('open');
	            		}
	            	});
	            });

	            $('#mmcplayout .box-addrow > button').on('click', function(e){
	            	add_row_layout();
	            });

			});
		}
	}
}(jQuery));

/**
 * libs megamenucreatorpro
 */
jQuery(function($){
	"use strict";
	$('.delete-action a[class*="mmcp_delete_menu"]').on('click', function(event){
		var item_id = $('input[id="menu"]').val(),
			params = {
				menu_id: item_id, 
				action: 'delete'
			};
	    var template = wp.template('menu-form-delete');
	    $('body').append(template({method: 'post', id: 'delete_menu', action: $('.menustruct > form[id="update-nav-menu"]').attr('action'), params: params}));
	    $('form[id="delete_menu"]').submit();
	});
	$('[class*="modal-footer"] > .btn_delete_item_menu').on('click', function(event){
		var item_id = $('input[type="hidden"][name="menu_item_id"]').val(), flag = true,
		element_delete = $('[id="menu-item-'+item_id+'"]'), 
		class_current_depth = 'menu-item-depth-'+element_delete.attr('class').match(/\menu-item-depth-(\d+)\b/)[1], 
		current_parrent_id = element_delete.find('input.menu-item-data-parent-id').val();
		$('input[type="hidden"][name="menu_item_id"]').val('');
		element_delete.nextUntil(function(){
			if (!$(this).hasClass('menu-item-depth-0') && flag && !$(this).hasClass(class_current_depth)) {
				var child_parrent = $(this).find('input.menu-item-data-parent-id').val(), child_depth = $(this).attr('class').match(/\menu-item-depth-(\d+)\b/)[1],
				child_depth_next = Number(child_depth) - 1;
				$(this).removeClass('menu-item-depth-'+child_depth)
					.addClass('menu-item-depth-'+child_depth_next);
				if (child_parrent == item_id) {
					$(this).find('input.menu-item-data-parent-id').val(current_parrent_id);
				}
			} else {
				flag = false;
			}
		});
		element_delete.remove();
		$('[id=mmcp_wrapperModal]').modal('hide');
	});
	$(".mmcp-menuwidget-layout").sidebar({side: 'left'});
	$(".mmcp-manager-typeitem-menu").sidebar({side: 'left'});
	$('.block-menustruct [class*=delete_item_menu]').each(function(event){
		$(this).on('click', function(){
			$('[id=mmcp_wrapperModal]').modal('show');
			var self = this, title = $(self).data('title'), menuitem = $(self).closest('li.menu-item'),
				menu_id = $("input#menu").val(), menu_item_id = parseInt(menuitem.attr("id").match(/[0-9]+/)[0], 10);

			$('input[type="hidden"][name="menu_item_id"]').val(menu_item_id);
			$('.modal-body p[class*="item_name_category"]').text('Do you want to proceed delete item "'+title+'"?');
		});
	});
	$('.block-menustruct [class*=edit_item_menu]').each(function(event){
		$(this).on('click', function(){
			var self = this, title = $(self).data('title'), menu_id = $("input#menu").val(), 
			menuitem = $(self).closest('li.menu-item'),
			menu_item_id = parseInt(menuitem.attr("id").match(/[0-9]+/)[0], 10),
			menu_depth = menuitem.attr('class').match(/\menu-item-depth-(\d+)\b/)[1];
			$(".mmcp-menuwidget-layouts").css('display','block');
			$('[id=asideOverlay]').show();
			$('.mmcp-menuwidget-layouts a[href="#mmcplayout"]').tab('show'); 
			$(self).megaMenuCreatorPro({menu_item_id: menu_item_id, menu_item_depth: menu_depth, menu_id: menu_id, menu_item_title: title, tab_id: 'mmcplayout'});
			return false;
		});
	});

	$(".mmcp-manager-typeitem-menu button.mmcp-close").on('click', function(event){
		$(".mmcp-manager-typeitem-menu").trigger("sidebar:close");
		$('.mmcp-manager-typeitem-menus a.active[data-toggle="tab"]').removeClass('active');
	});

	$('.block-menustruct .block-button-addmenu > button').on('click', function(event){
		$('.mmcp-manager-typeitem-menus a[href="#mmcpPages"]').tab('show');
		$(".mmcp-manager-typeitem-menus").css('display','block');
		$('[id=asideOverlay]').show();
		$(".mmcp-manager-typeitem-menu").trigger("sidebar:open")
		.on('sidebar:opened', function(event){
		})
		.on('sidebar:closed', function(event){
			$('[id=asideOverlay]').hide();
		});
	});

	var tab_called = [];
    var MyDateField = function(config) {
        jsGrid.Field.call(this, config);
    };
    MyDateField.prototype = new jsGrid.Field({
		autosearch: true,
		flagsearch: {from: false, to: false},
		readOnly: false,
        sorter: function(date1, date2) {
            return new Date(date1) - new Date(date2);
        },

        itemTemplate: function(value) {
            return new Date(value).toDateString();
        },
		filterTemplate: function() {
			if(!this.filtering)
				return "";
			var grid = this._grid, now = new Date(), $result = this._filterPickerFrom = $("<input>").datepicker({
				changeMonth: true, 
				changeYear: true, 
				defaultDate: now.setFullYear(now.getFullYear() - 1)
			}), $result1 = this._filterPickerTo = $("<input class=\"dateto\">").datepicker({ 
				changeMonth: true, 
				changeYear: true, 
				defaultDate: now.setFullYear(now.getFullYear() + 1) 
			});
			if(this.autosearch) {
				$result.on("change", function(e) {
					grid.search();
					e.preventDefault();
				});						
				$result1.on("change", function(e) {
						grid.search();
						e.preventDefault();
				});
			}
			return $("<div class=\"boxfilter-date-time\">").append(this._filterPickerFrom).append(this._filterPickerTo);	
		},				

        filterValue: function() {
			return {
				from: this._filterPickerFrom.datepicker("getDate"),
				to: this._filterPickerTo.datepicker("getDate")
			};
        }
    });
    jsGrid.fields.myDateField = MyDateField;
    
    var MyCheckboxField = function(config) {
        jsGrid.Field.call(this, config);
    };

	MyCheckboxField.prototype = new jsGrid.Field({
		sorter: "number",
		sorting: false,
		align: "center",
		autosearch: true,
		itemTemplate: function(value) {
			return $("<input>").attr({type: "checkbox", class: 'custom_checkbox'}).prop({
				checked: value,
				disabled: false
			});
		},
		filterTemplate: function() {
			if(!this.filtering)
				return "";
				var grid = this._grid,
					$result = this.filterControl = $("<input>").attr("type", "checkbox");
			if(this.autosearch) {
				$result.on("click", function() {
					console.log('data >>>>>', grid.data);
					if ($(this).is(':checked')) {
						grid.data.filter(function(item){
							item.item_select = true;
							return item;
						});
					} else {
						grid.data.filter(function(item){
							item.item_select = false;
							return item;
						});
					}
					grid.refresh();
				});
			}
			return $result;
		},
		filterValue: function() {
			return this.filterControl.get(0).indeterminate
				? undefined
				: this.filterControl.is(":checked");
		}
	});
	jsGrid.fields.MyCheckboxField = MyCheckboxField;

	function row_click(item) {
		var self = this;
    	if (item && item.event) {
    		var element = item.event.currentTarget, indexItem = item.itemIndex, is_refresh = true;
    		var checkbox = $(element).find('input[class*="custom_checkbox"]');
    		if (checkbox) {
    			if (checkbox.is(':checked')) {
    				if(self.data[indexItem].item_select) {
    					is_refresh = false;
    				} else {
						self.data[indexItem].item_select = true;
    				}
    			} else {
    				if(self.data[indexItem].item_select) {
    					self.data[indexItem].item_select = false;
    				} else {
    					is_refresh = false;
    				}
    			}
    			if (is_refresh) {
					self.refresh();
    			}
    		}
    	}
	}
	function remove_class_tab_disabled() {
		$('.mmcp-manager-typeitem-menus a[data-toggle="tab"]').removeClass('disabled');
	};
	$('.mmcp-manager-typeitem-menus a[data-toggle="tab"]')
	.on('show.bs.tab', function(event){
		$('.mmcp-manager-typeitem-menus a[data-toggle="tab"]').addClass('disabled');
	}).on('shown.bs.tab', function(evet){
		var tab_name = $(this).attr('href').split(/#mmcp/)[1];
		$(this).removeClass('disabled');
		if (tab_called.indexOf(tab_name) == -1) {
			tab_called.push(tab_name);
			if (tab_name === 'Pages') {
				$('#mmcpPages').jsGrid({
	                height: "auto",
	                width: "100%",
	                filtering: true,
	                editing: false,
	                inserting: false,
	                sorting: true,
	                paging: true,
	                autoload: true,
	                pageSize: 10,
	                pageButtonCount: 5,
	                rowClick: function(item) {
						row_click.call(this, item);
	                },
	                controller: {
						loadData: function(filter) {
							var self = this;
							if (filter && self.data.length) {
								return $.grep(self.data, function(client) {
									if (typeof filter.item_select === 'boolean') client.item_select = filter.item_select;
										return (!filter.id || client.id == filter.id)
										 	&& (!filter.title || client.title.indexOf(filter.title) > -1)
										 	&& (!filter.item_slug || client.item_slug.indexOf(filter.item_slug) > -1)
										 	&& (!filter.author || client.author.indexOf(filter.author) > -1)
										 	&& (!filter.public_date.from || new Date(client.public_date) >= filter.public_date.from)
										 	&& (!filter.public_date.to || new Date(client.public_date) <= filter.public_date.to);
									});
							} else {
								var d = $.Deferred();
								$.ajax({
									url: ajaxurl,
									method: 'POST',
									data: {
										action: 'mmcp_tab_data',
										tab: tab_name,
										mmcp_nonce: $('input[name="ajaxtab-check"]').val()				
									},
									beforeSend: function() {

									},
									success: function(data) {
										var _data = JSON.parse(data.data);
										remove_class_tab_disabled();
										console.log('success >>>', _data);
									},
									error: function() {
										console.log('error >>>');
										remove_class_tab_disabled();
										d.resolve([]);
									}
								}).done(function(response) {
									var _data = JSON.parse(response.data);
									console.log('done >>>', _data);
									self.data = JSON.parse(JSON.stringify(_data.data));
									remove_class_tab_disabled();
									d.resolve(_data.data);
		                        });						
								return d.promise();
							}
						},
						data: []
	                },
	                fields: [
	                	{ name: "item_select", title:"", type: "MyCheckboxField", width: 20, align: 'center' },
						{ name: "id", title:"Id", type: "number", width: 40, align: 'center'},
						{ name: "title", title:"Title", type: "text", width: 150, align: 'left' },
						{ name: "item_slug", title: "Item Slug", type: "text", width: 150, align: 'left' },
						{ name: "author", title:"Author", type: "text", width: 100 },
						{ name: "public_date", title:"Public Date", type: "myDateField", width: 100 },
						{ type: "control", deleteButton: false, editButton: false, visible: true, css: "mmcp-hide" }                
	                ]
				});
			} else if(tab_name === 'Posts') {
				$('#mmcpPosts').jsGrid({
	                height: "auto",
	                width: "100%",
	                filtering: true,
	                editing: false,
	                inserting: false,
	                sorting: true,
	                paging: true,
	                autoload: true,
	                pageSize: 10,
	                pageButtonCount: 5,
	                rowClick: function(item) {
	                	row_click.call(this, item);
	                },
	                controller: {
						loadData: function(filter) {
							var self = this;
							console.log(filter, self.data);
							if (filter && self.data.length) {
								return $.grep(self.data, function(client) {
									if (typeof filter.item_select === 'boolean') client.item_select = filter.item_select;
										return (!filter.id || client.id == filter.id)
										 	&& (!filter.title || client.title.indexOf(filter.title) > -1)
										 	&& (!filter.item_slug || client.item_slug.indexOf(filter.item_slug) > -1)
										 	&& (!filter.author || client.author == filter.author)
										 	&& (!filter.public_date.from || new Date(client.public_date) >= filter.public_date.from)
										 	&& (!filter.public_date.to || new Date(client.public_date) <= filter.public_date.to);										 	
									});
							} else {
								var d = $.Deferred();
								$.ajax({
									url: ajaxurl,
									method: 'POST',
									data: {
										action: 'mmcp_tab_data',
										tab: tab_name,
										mmcp_nonce: $('input[name="ajaxtab-check"]').val()				
									},
									beforeSend: function() {

									},
									success: function(data) {
										var _data = JSON.parse(data.data);
										remove_class_tab_disabled();
										console.log('success >>>', _data);
									},
									error: function() {
										console.log('error >>>');
										remove_class_tab_disabled();
										d.resolve([]);
									}
								}).done(function(response) {
									var _data = JSON.parse(response.data);
									console.log('done >>>', _data);
									self.data = JSON.parse(JSON.stringify(_data.data));
									remove_class_tab_disabled();
									d.resolve(_data.data);
		                        });						
								return d.promise();
							}
						},
						data: []
	                },
	                fields: [
	                	{ name: "item_select", title:"", type: "MyCheckboxField", width: 20, align: 'center' },
						{ name: "id", title:"Id", type: "number", width: 40, align: 'center'},
						{ name: "title", title:"Title", type: "text", width: 150, align: 'left' },
						{ name: "item_slug", title: "Item Slug", type: "text", width: 150, align: 'left' },
						{ name: "author", title:"Author", type: "select", css: 'mmcp_select', items: mmcp_users.udata, valueField: "id", textField: "name", width: 100 },
						{ name: "public_date", title:"Public Date", type: "myDateField", width: 100 },
						{ type: "control", deleteButton: false, editButton: false, visible: true, css: "mmcp-hide" }                
	                ]
				});
			}else if(tab_name === 'Categories') {
				$('#mmcpCategories').jsGrid({
	                height: "auto",
	                width: "100%",
	                filtering: true,
	                editing: false,
	                inserting: false,
	                sorting: true,
	                paging: true,
	                autoload: true,
	                pageSize: 10,
	                pageButtonCount: 5,
	                rowClick: function(item) {
	                	row_click.call(this, item);
	                },
	                controller: {
						loadData: function(filter) {
							var self = this;
							console.log(filter, self.data);
							if (filter && self.data.length) {
								return $.grep(self.data, function(client) {
									if (typeof filter.item_select === 'boolean') client.item_select = filter.item_select;
										return (!filter.id || client.id == filter.id)
										 	&& (!filter.title || client.title.indexOf(filter.title) > -1)
										 	&& (!filter.item_slug || client.item_slug.indexOf(filter.item_slug) > -1);
									});
							} else {
								var d = $.Deferred();
								$.ajax({
									url: ajaxurl,
									method: 'POST',
									data: {
										action: 'mmcp_tab_data',
										tab: tab_name,
										mmcp_nonce: $('input[name="ajaxtab-check"]').val()
									},
									beforeSend: function() {

									},
									success: function(data) {
										var _data = JSON.parse(data.data);
										remove_class_tab_disabled();
										console.log('success >>>', _data);
									},
									error: function() {
										console.log('error >>>');
										remove_class_tab_disabled();
										d.resolve([]);
									}
								}).done(function(response) {
									var _data = JSON.parse(response.data);
									console.log('done >>>', _data);
									self.data = JSON.parse(JSON.stringify(_data.data));
									remove_class_tab_disabled();
									d.resolve(_data.data);
		                        });						
								return d.promise();
							}
						},
						data: []
	                },
	                fields: [
	                	{ name: "item_select", title:"", type: "MyCheckboxField", width: 20, align: 'center' },
						{ name: "id", title:"Id", type: "number", width: 40, align: 'center'},
						{ name: "title", title:"Title", type: "text", width: 150, align: 'left' },
						{ name: "item_slug", title: "Item Slug", type: "text", width: 150, align: 'left' },
						{ type: "control", deleteButton: false, editButton: false, visible: true, css: "mmcp-hide" }                
	                ]
				});
			} else if(tab_name === 'Tags') {
				$('#mmcpTags').jsGrid({
	                height: "auto",
	                width: "100%",
	                filtering: true,
	                editing: false,
	                inserting: false,
	                sorting: true,
	                paging: true,
	                autoload: true,
	                pageSize: 10,
	                pageButtonCount: 5,
	                rowClick: function(item) {
	                	row_click.call(this, item);
	                },
	                controller: {
						loadData: function(filter) {
							var self = this;
							console.log(filter, self.data);
							if (filter && self.data.length) {
								return $.grep(self.data, function(client) {
									if (typeof filter.item_select === 'boolean') client.item_select = filter.item_select;
										return (!filter.id || client.id == filter.id)
										 	&& (!filter.title || client.title.indexOf(filter.title) > -1)
										 	&& (!filter.item_slug || client.item_slug.indexOf(filter.item_slug) > -1);
									});
							} else {
								var d = $.Deferred();
								$.ajax({
									url: ajaxurl,
									method: 'POST',
									data: {
										action: 'mmcp_tab_data',
										tab: tab_name,
										mmcp_nonce: $('input[name="ajaxtab-check"]').val()				
									},
									beforeSend: function() {

									},
									success: function(data) {
										var _data = JSON.parse(data.data);
										remove_class_tab_disabled();
										console.log('success >>>', _data);
									},
									error: function() {
										console.log('error >>>');
										remove_class_tab_disabled();
										d.resolve([]);
									}
								}).done(function(response) {
									var _data = JSON.parse(response.data);
									console.log('done >>>', _data);
									remove_class_tab_disabled();
									self.data = JSON.parse(JSON.stringify(_data.data));
									d.resolve(_data.data);
		                        });						
								return d.promise();
							}
						},
						data: []
	                },
	                fields: [
	                	{ name: "item_select", title:"", type: "MyCheckboxField", width: 20, align: 'center' },
						{ name: "id", title:"Id", type: "number", width: 40, align: 'center'},
						{ name: "title", title:"Title", type: "text", width: 150, align: 'left' },
						{ name: "item_slug", title: "Item Slug", type: "text", width: 150, align: 'left' },
						{ type: "control", deleteButton: false, editButton: false, visible: true, css: "mmcp-hide" }                
	                ]
				});
			} else if(tab_name == 'Customlinks') {
				remove_class_tab_disabled();
			}
		} else {
			remove_class_tab_disabled();
		}
	});
	function get_data_select(tab_select) {
		var self = this;
		self.tab_active = tab_select;
		self.data = $(tab_select).jsGrid('option', 'data');
		$.each(self.data, function(index, value){
			if (value.item_select) {
				value.item_select = false;
				if(typeof value.input_hidden === 'object' && value.input_hidden.menu_item) {
					$.extend(self.menu_item, value.input_hidden.menu_item);
					self.ajax_valid = true;
				}
			}
		});
	}
	$('button[class*="addtomenu"]').on('click', function(){
		var admin_ajax = $('input[name="menu-settings-column-nonce"]').val(), is_ok = true;
		var menu_id = $('.group_menu input[type="hidden"][name="menu"]').val();
		var menu_obj = {
			tab_name: $('ul[id=typeItemTab] li > a.active').attr('href').split('#mmcp')[1],
			tab_active: '',
			data: [],
			ajax_valid: false,
			menu_item: {}
		};
		switch(menu_obj.tab_name) {
			case 'Pages':
				var _get_data = get_data_select.bind(menu_obj, '#mmcpPages');
				_get_data();
				break;
			case 'Posts':
				var _get_data = get_data_select.bind(menu_obj, '#mmcpPosts');
				_get_data();
				break;
			case 'Categories':
				var _get_data = get_data_select.bind(menu_obj, '#mmcpCategories');
				_get_data();
				break;
			case 'Tags':
				var _get_data = get_data_select.bind(menu_obj, '#mmcpTags');
				_get_data();
				break;
			case 'Customlinks':
				if($('div[id="mmcpCustomlinks"] input[type="hidden"][name="add-custom-menu-item"]').is(':disabled')) {
					is_ok = false;
				} else {
					var menu_item = {}, custom_url =$('div[id="mmcpCustomlinks"] [id=custom-menu-item-url]').val(),
					custom_title = $('div[id="mmcpCustomlinks"] [id=custom-menu-item-name]').val(),
					str_name = $('div[id="mmcpCustomlinks"] [id=custom-menu-item-url]').attr('name'), _index = String(str_name).match(/\d+/)[0];
					menu_item[_index*-1] = {
						'menu-item-type': 'custom',
						'menu-item-url': custom_url,
						'menu-item-title': custom_title
					};
					if(!custom_url || !custom_title || custom_url == 'http://') {
						is_ok = false;
						menu_obj.ajax_valid = false;
						$('div[id="mmcpCustomlinks"] [id=custom-menu-item-url]').css('border-color','red');
						$('div[id="mmcpCustomlinks"] [id=custom-menu-item-name]').css('border-color','red');
					} else {
						$('div[id="mmcpCustomlinks"] [id=custom-menu-item-url]').removeAttr('style');
						$('div[id="mmcpCustomlinks"] [id=custom-menu-item-name]').removeAttr('style');
						menu_obj.ajax_valid = true;
						$.extend(menu_obj.menu_item, menu_item);
					}
				}
				break;
		}
		if (menu_obj.ajax_valid && is_ok) {
			$('.row-botton > .spinner').addClass('is-active');
			var params = {
					'action': 'add-menu-item',
					'menu': menu_id,
					'mmcp-menu': 1,
					'menu-settings-column-nonce': admin_ajax,
					'menu-item': menu_obj.menu_item
			};
			$.post( ajaxurl, params, function(menuMarkup) {
				menuMarkup = $.trim( menuMarkup );
				wpNavMenu.addMenuItemToBottom(menuMarkup, params);
				$('.row-botton > .spinner').removeClass('is-active');
				$(menu_obj.tab_active).jsGrid('refresh', menu_obj.data);
				$(menu_obj.tab_active).jsGrid('clearFilter');
				$(".mmcp-manager-typeitem-menu button.mmcp-close").trigger('click');
			});
		}
	});
});