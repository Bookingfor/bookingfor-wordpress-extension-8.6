jQuery(document).ready(function() {
	console.log("jQuery ready");
	jQuery(document).ajaxSuccess(function(e, xhr, settings) {
		var widget_id_base = 'bookingfor_booking_search';
		if(typeof settings.data === "string" && settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=bookingfor_booking_search') != -1) {
			var widgetid = bfi_getParameterByName('widget-id', settings.data);
			var cForm = jQuery('input[value="' + widgetid + '"]').parents("form");
			bfi_adminInit(cForm); 
		}
		if(typeof settings.data === "string" && settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=bookingfor_carousel') != -1) {
			var widgetid = bfi_getParameterByName('widget-id', settings.data);
			var cForm = jQuery('input[value="' + widgetid + '"]').parents("form");
			bfi_adminselect2Init(cForm);
		}

	});

	jQuery(document).on('click','.bfiadvance-cb',function(){
		bfi_CheckAdvance(jQuery(this));
	});

	jQuery(".bfi-select2").each(function() {
		console.log(".bfi-select2 each");
		currForm = jQuery(jQuery(this)).closest("form");
		bfi_adminselect2Init(currForm);
	});

	
	jQuery.widget.bridge('bfiTabsDetails', jQuery.ui.tabs);
	jQuery("#bfiadminsetting").bfiTabsDetails();

	if (typeof elementor !== "undefined")
	{
		elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
			//codice per elementor
			setTimeout(function(){
				jQuery(".bfi-select2").each(function() {
					currForm = jQuery(jQuery(this)).closest("form");
					bfi_adminselect2Init(currForm);
				});
			},3000);
		} );
	}
});

function bfi_CheckAdvance(obj){
		currForm = jQuery(obj).closest("form");
		currForm.find(".bfiadvance").hide();
		if (jQuery(obj).is(":checked"))
		{
			currForm.find(".bfiadvance").show();
		}
}


function bfi_adminselect2Init(currForm){
	console.log("bfi_adminselect2Init");
		if(currForm!= null && jQuery(currForm).length){
			jQuery(currForm).find(".select2").select2();
			jQuery(currForm).find(".select2full").select2({ width: '100%' });
		}else{
			jQuery(".select2").not('[name*="__i__"]').select2();
			jQuery(".select2full").not('[name*="__i__"]').select2({ width: '100%' });
		}
}


function bfi_adminInit(currForm){
	console.log("bfi_adminInit");
	bfi_adminselect2Init(currForm);
	bfi_CheckAdvance(jQuery(currForm).find('.bfiadvance-cb').first());
}

function bfi_getParameterByName(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}