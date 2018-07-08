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
			$(self).megaMenuCreatorPro({menu_item_id: menu_item_id, menu_item_depth: menu_depth, menu_id: menu_id, menu_item_title: title});
			return false;
		});
	});

	$(".mmcp-manager-typeitem-menu button.mmcp-close").on('click', function(event){
		console.log('Close sidebar >>>>>>>>>>>>>>>>>>>>>');
		$(".mmcp-manager-typeitem-menu").trigger("sidebar:close");
	});
	$('.block-menustruct .block-button-addmenu > button').on('click', function(event){
		$(".mmcp-manager-typeitem-menus").css('display','block');
		$('[id=asideOverlay]').show();
		$(".mmcp-manager-typeitem-menu").trigger("sidebar:open")
		.on('sidebar:opened', function(event){
			//$('.mmcp-manager-typeitem-menu .load_content').show();
		})
		.on('sidebar:closed', function(event){
			console.log('Close sidebar >>>>>>>>>>>>>>>>>>>>>');
			//$('.mmcp-manager-typeitem-menu .sidebar-content').empty();
			$('[id=asideOverlay]').hide();
		});
	});
});