(function($) {
    FWP.hooks.addAction('facetwp/refresh/color', function($this, facet_name) {
        var selected_values = [];
        $this.find('.facetwp-color.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        FWP.facets[facet_name] = selected_values;
    });

    FWP.hooks.addFilter('facetwp/selections/color', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            choices.push({
                value: val,
                label: val
            });
        });
        return choices;
    });

    FWP.hooks.addAction('facetwp/ready', function() {
        $(document).on('click touchstart', '.facetwp-facet .facetwp-color:not(.disabled)', function(e) {
            if (true === e.handled) {
                return false;
            }
            e.handled = true;
            $(this).toggleClass('checked');
            var $facet = $(this).closest('.facetwp-facet');
            FWP.autoload();
        });
    });

    $(document).on('facetwp-loaded', function() {
        $('.facetwp-color').each(function() {
            $(this).css('background-color', $(this).attr('data-color'));
        });
    });
})(jQuery);
