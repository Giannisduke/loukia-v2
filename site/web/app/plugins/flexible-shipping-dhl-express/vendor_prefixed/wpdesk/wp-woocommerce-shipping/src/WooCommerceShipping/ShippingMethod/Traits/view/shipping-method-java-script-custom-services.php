<script type="text/javascript">

    jQuery(document).ready(function () {

        var custom_services_checkbox_class = 'wpdesk_wc_shipping_custom_service_checkbox';
        var custom_services_class = 'wpdesk_wc_shipping_custom_services';
        var custom_service_name_class = 'wpdesk_wc_shipping_custom_services_service_name';

        jQuery('.' + custom_services_checkbox_class).change(function () {
            if (jQuery(this).is(':checked') && jQuery(this).is(':visible')) {
                jQuery('.' + custom_services_class).closest('tr').show();
                jQuery('.' + custom_service_name_class).prop('required', true);
            } else {
                jQuery('.' + custom_services_class).closest('tr').hide();
                jQuery('.' + custom_service_name_class).prop('required', false);
            }
        });
        if (jQuery('.' + custom_services_checkbox_class).length) {
            jQuery('.' + custom_services_checkbox_class).change();
        }

        function api_status_update(status_field) {
            var shipping_service_id = jQuery(status_field).data('shipping_service_id');
            var ajax_url = jQuery(status_field).data('ajax_url');
            var ajax_data = {
                action: 'wpdesk_wc_shipping_api_status_' + shipping_service_id,
                shipping_service_id: shipping_service_id,
                security: jQuery(status_field).data('nonce'),
            };
            jQuery.ajax({
                url: ajax_url,
                data: ajax_data,
                method: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    jQuery(status_field).html(data.status);
                    jQuery(status_field).removeClass('wpdesk_wc_shipping_api_status_ok');
                    jQuery(status_field).removeClass('wpdesk_wc_shipping_api_status_error');
                    jQuery(status_field).addClass(data.class_name);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    jQuery(status_field).html(thrownError);
                },
                complete: function () {
                }
            });
        }

        jQuery(".wpdesk_wc_shipping_api_status").each(function () {
            api_status_update(this);
        });

    });

</script>

