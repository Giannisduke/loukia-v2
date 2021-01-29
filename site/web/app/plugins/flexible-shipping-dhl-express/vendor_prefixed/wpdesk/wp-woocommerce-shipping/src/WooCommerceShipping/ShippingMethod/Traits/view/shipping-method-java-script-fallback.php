<?php

namespace DhlVendor;

/**
 * @var string $settings_prefix
 */
?><script type="text/javascript">

    jQuery(document).ready(function(){

        let $fallback_field = jQuery('#<?php 
echo $settings_prefix;
?>_fallback');

        $fallback_field.change(function() {
            let $ups_fallback_cost = jQuery('#<?php 
echo $settings_prefix;
?>_fallback_cost');
            if ( jQuery(this).is(':checked') && jQuery(this).is(':visible') ) {
                $ups_fallback_cost.closest('tr').show();
                $ups_fallback_cost.attr('required',true);
            }
            else {
                $ups_fallback_cost.closest('tr').hide();
                $ups_fallback_cost.attr('required',false);
            }
        });

        if ( $fallback_field.length ) {
            $fallback_field.change();
        }

    });

</script>
<?php 
