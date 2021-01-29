<script type="text/javascript">
    jQuery(document).ready(function(){
        let $custom_origin = jQuery('.custom_origin');

        $custom_origin.change(function(){
            let $custom_origin_fields = jQuery( '.custom_origin_field' );
            let $is_custom_origin_enabled = jQuery(this).is(':checked');
            $custom_origin_fields.closest('tr').toggle($is_custom_origin_enabled);
            $custom_origin_fields.attr('required',$is_custom_origin_enabled);
        });

        if ( $custom_origin.length ) {
            $custom_origin.change();
            jQuery('.custom_origin_country').select2();
        }
    });
</script>

