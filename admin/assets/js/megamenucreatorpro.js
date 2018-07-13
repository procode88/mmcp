/**
 * Mega Menu Creator Pro jQuery Plugin
 */
(function($) {
	"use strict";
	$.fn.megaMenuCreatorPro = function(options) {
		var plugin = $('<div />');
		var settings = $.extend({
			menu_item_id: null,
			menu_item_depth: 0,
			menu_id: null,
			menu_item_title: ''
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
		})
		.on('sidebar:closed', function(event){
			$('.mmcp-sidebar.mmcp-menuwidget-layout .sidebar-content').empty();
			$('[id=asideOverlay]').hide();
		});
		$(".mmcp-sidebar.mmcp-menuwidget-layout button.mmcp-close").on('click', function(event){
			$(".mmcp-sidebar.mmcp-menuwidget-layout").trigger("sidebar:close");
		});
	}
}(jQuery));

/**
 * libs megamenucreatorpro
 */
jQuery(function($){
	"use strict";
	$(".mmcp-menuwidget-layout").sidebar({side: 'left'});
	$(".mmcp-manager-typeitem-menu").sidebar({side: 'left'});
	$('.block-menustruct [class*=button-edit]').each(function(){
		$(this).on('click', function(){
			var self = this, title = $(self).data('title'), menu_id = $("input#menu").val(), menuitem = $(self).closest('li.menu-item');
			var menu_item_id = parseInt(menuitem.attr("id").match(/[0-9]+/)[0], 10);
			var menu_depth = menuitem.attr('class').match(/\menu-item-depth-(\d+)\b/)[1];
			$(".mmcp-menuwidget-layouts").css('display','block');
			$('[id=asideOverlay]').show();
			$('.mmcp-menuwidget-layouts a[href="#mmcplayout"]').tab('show'); 
			$(self).megaMenuCreatorPro({menu_item_id: menu_item_id, menu_item_depth: menu_depth, menu_id: menu_id, menu_item_title: title});
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


	$('.mmcp-menuwidget-layouts a[data-toggle="tab"]').on('show.bs.tab', function(event){
		console.log('tab is active', $(this).attr('href'));
	}).on('shown.bs.tab', function(evet){
		console.log('tab is actived', $(this).attr('href'));
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

	$('.mmcp-manager-typeitem-menus a[data-toggle="tab"]').on('show.bs.tab', function(event){

	}).on('shown.bs.tab', function(evet){
		var tab_name = $(this).attr('href').split(/#mmcp/)[1];
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
										 	&& (!filter.author || client.author.indexOf(filter.author) > -1);
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
										console.log('success >>>', _data);
									},
									error: function() {
										console.log('error >>>');
										d.resolve([]);
									}
								}).done(function(response) {
									var _data = JSON.parse(response.data);
									console.log('done >>>', _data);
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
						{ name: "author", title:"Author", type: "text", width: 100 },
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
										 	&& (!filter.author || client.author == filter.author);
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
										console.log('success >>>', _data);
									},
									error: function() {
										console.log('error >>>');
										d.resolve([]);
									}
								}).done(function(response) {
									var _data = JSON.parse(response.data);
									console.log('done >>>', _data);
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
						{ name: "author", title:"Author", type: "select", css: 'mmcp_select', items: mmcp_users.udata, valueField: "id", textField: "name", width: 100 },
						{ type: "control", deleteButton: false, editButton: false, visible: true, css: "mmcp-hide" }                
	                ]
				});
			}
		}
	});
	$('button[class*="addtomenu"]').on('click', function(){
		var tap_name = $('ul[id=typeItemTab] li > a.active').attr('href').split('#mmcp')[1], data = [];
		switch(tap_name) {
			case 'Pages':
				data = $('#mmcpPages').jsGrid('data');
				break;
			case 'Posts':
				data = $('#mmcpPosts').jsGrid('data');
				break;
			case 'Categories':
				break;
			case 'Tags':
				break;
			case 'CustomLink':
				break;
		}
	});
});