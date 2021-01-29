function fs_select2() {
	let elements = jQuery( '.fs_select2' );
	if ( elements.length ) {
		if (jQuery.fn.selectWoo) {
			elements.selectWoo();
		} else {
			elements.select2();
		}
	}
}

jQuery(document).ready(function(){
    if ( jQuery('#flexible_shipping_labels_url').length ) {
        window.location.href = jQuery('#flexible_shipping_labels_url').attr('href');
    }

    if ( jQuery('a.shipping_manifest_download').length == 1 ) {
        window.location.href = jQuery('a.shipping_manifest_download').attr('href');
    }

    if ( typeof window.history.pushState == 'function' ) {
        var url = document.location.href;
        var url2 = document.location.href;
        url = fs_removeParam('bulk_flexible_shipping_labels', url);
        url = fs_removeParam('bulk_flexible_shipping_send', url);
        url = fs_removeParam('bulk_flexible_shipping_manifests', url);
        url = fs_removeParam('bulk_flexible_shipping_no_labels_created', url);
        url = fs_trimChar(url,'?');
        if ( url != url2 ) {
            window.history.pushState({}, "", url);
        }
    }

	/* Connect Global Notice */
	var nav = jQuery( '.fs-connect__vertical-nav-container' ),
		contentContainer = jQuery( '.fs-connect__content-container' );
		nextFeatureButtons = jQuery( '.fs-banner__button-container .next-feature' ),

	nav.on( 'click', '.vertical-menu__feature-item:not( .vertical-menu__feature-item-is-selected )', function() {
		transitionSlideToIndex( jQuery( this ).index() );
	} );

	nextFeatureButtons.on( 'click', function( e ) {
		e.preventDefault();

		var slideIndex = jQuery( this )
			.closest( '.fs-connect__slide' )
			.index();

		transitionSlideToIndex( slideIndex + 1 );
	} );

	function transitionSlideToIndex( index ) {
		// Remove classes from previously selected menu item and content
		nav
			.find( '.vertical-menu__feature-item-is-selected' )
			.removeClass( 'vertical-menu__feature-item-is-selected' );

		contentContainer
			.find( '.fs__slide-is-active' )
			.removeClass( 'fs__slide-is-active' );

		// Add classes to selected menu item and content
		nav
			.children()
			.eq( index )
			.addClass( 'vertical-menu__feature-item-is-selected' );

		contentContainer
			.children()
			.eq( index )
			.addClass( 'fs__slide-is-active' );
	}
});

function fs_removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

function fs_trimChar(string, charToRemove) {
    while(string.charAt(0)==charToRemove) {
        string = string.substring(1);
    }

    while(string.charAt(string.length-1)==charToRemove) {
        string = string.substring(0,string.length-1);
    }

    return string;
}


// Order functions

function fs_id( element ) {
    return jQuery(element).closest('.flexible_shipping_shipment').attr('data-id');
}

function fs_data_set_val( data, name, val ) {
    if ( typeof name == 'undefined' ) {
        return data;
    }
    if ( name.indexOf("[") == -1 ) {
        data[name] = val;
    }
    else {
        var names = name.split("[");
        var data2 = data;
        var data3 = data;
        var name2 = '';
        jQuery.each(names,function(index,name) {
            name2 = name.replace("]","");
            if ( typeof data2[name2] == 'undefined' ) {
                data2[name2] = {};
            }
            data3 = data2;
            data2 = data2[name2];
        });
        data3[name2] = val;
    }
    return data;
}

function fs_ajax(button, id, fs_action) {
    jQuery('.button-shipping').attr('disabled', true);
    jQuery(button).parent().find('.spinner').css({visibility: 'visible'});
    var data = {};

    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content input, #flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content select, #flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content textarea').each(function () {
        if (jQuery(this).attr('type') == 'radio') {
            data = fs_data_set_val( data, jQuery(this).attr('name'), jQuery('#flexible_shipping_shipment_' + id + ' input[name=' + jQuery(this).attr('name') + ']:checked').val() );
        }
        else if (jQuery(this).attr('type') == 'checkbox') {
            if (jQuery(this).is(':checked')) {
                data = fs_data_set_val( data, jQuery(this).attr('name'), jQuery(this).val() );
            }
            else {
                data = fs_data_set_val( data, jQuery(this).attr('name'), '' );
            }
        }
        else {
            data = fs_data_set_val( data, jQuery(this).attr('name'), jQuery(this).val() );
        }
    });

    var nonce = jQuery('#flexible_shipping_shipment_nonce_' + id).val();

    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').hide();
    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').removeClass("flexible_shipping_shipment_message_error");

    jQuery.ajax({
        url: fs_admin.ajax_url,
        type: 'POST',
        data: {
            fs_action: fs_action,
            action: 'flexible_shipping',
            nonce: nonce,
            shipment_id: id,
            data: data,
        },
        dataType: 'json',
    }).done(function (response) {
        if (response) {
            if (response == '0') {
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html("Invalid response: 0");
            }
            else if (response.status == 'success') {
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content').html(response.content);
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').hide();
                if ( typeof response.message != 'undefined' ) {
                    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
                    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html(response.message);
                }
            }
            else {
            	if ( typeof response.content !== 'undefined' ) {
					jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content').html(response.content);
				}
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').addClass("flexible_shipping_shipment_message_error");
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html(response.message);
            }
        }
        else {
            jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').addClass("flexible_shipping_shipment_message_error");
            jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
            jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html("Request failed: invalid method?");
        }
    }).always(function () {
        jQuery('.button-shipping').attr('disabled', false);
        jQuery('.shipping-spinner').parent().find('.spinner').css({visibility: 'hidden'});
		fs_select2();
		jQuery('#flexible_shipping_shipment_' + id).trigger( "flexible_shipping_ajax_fs_action_after" );
    }).fail(function (jqXHR, textStatus) {
		jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').addClass("flexible_shipping_shipment_message_error");
        jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
        jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html("Request failed: " + textStatus + " " + jqXHR.status);
    })
}

/* Notice */
jQuery(function($) {
    $( document ).on( 'click', '.flexible-shipping-taxes-notice .notice-dismiss', function () {
        $.ajax( ajaxurl,
            {
                type: 'POST',
                data: {
                    action: 'flexible_shipping_taxes_notice',
                }
            } );
    } );

	$( document ).on( 'click', '#enable-fs-connect-box', function () {
		var fs_connect_checkbox = $('.enable-fs-connect-box');
		var fs_box_state;

		if ( fs_connect_checkbox.prop('checked') ){
			$('.fs-connect-integration-box').slideDown();
			fs_box_state = 1;
		} else{
			$('.fs-connect-integration-box').slideUp();
			fs_box_state = 0;
		}

		$.ajax( ajaxurl,
			{
				type: 'POST',
				data: {
					action: 'update_fs_connect_integration_setting',
					fs_box_state: fs_box_state
				}
			} );
	} );

	$( document ).on( 'click', '#flexible_shipping_rate_plugin .close-fs-rate-notice', function () {
		$( '#flexible_shipping_rate_plugin .notice-dismiss' ).click();
	} );

	$( document ).on( 'click', '#flexible_shipping_rate_plugin .fs-not-good', function () {
		$('#flexible_shipping_rate_plugin p').html( fs_admin.notice_not_good_enought );
	} );

});

/* Free shipping */
jQuery(function($) {
	function fs_toggle_free_shipping_notice() {
		$('#woocommerce_flexible_shipping_method_free_shipping_cart_notice').closest('tr').toggle($('#woocommerce_flexible_shipping_method_free_shipping').val()!=='');
	}

	$('#woocommerce_flexible_shipping_method_free_shipping').on('change',  function(){
		fs_toggle_free_shipping_notice();
	});

	fs_toggle_free_shipping_notice();
});
