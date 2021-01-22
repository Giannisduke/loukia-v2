jQuery(document).ready(function($) {

    var now = new Date();

    $( '.mtm-countdown-timer-design' ).each(function( index ) {

        var date_id      = $(this).attr('id');
        var date_id      = date_id+ ' .mtm-clock'
        var date_conf    = $.parseJSON( $(this).parent('.mtm-countdown-wrp').find('.mtm-date-conf').text());
        var diff         = new Date(date_conf.mtm_date);
        var reg          = getdifference(diff);

        var $example = $("#"+date_id),
            $ceHours = $example.find('.ce-days'),
            $ceHours = $example.find('.ce-hours'),
            $ceMinutes = $example.find('.ce-minutes'),
            $ceSeconds = $example.find('.ce-seconds');

        $("#"+date_id).countEverest({
            day: now.getDate()+(reg.days),
            month: now.getMonth()+1,
            year: now.getFullYear()+0,
            hour: now.getHours()+(reg.hours),
            minute: now.getMinutes()+(reg.minutes),
            second: now.getSeconds()+(reg.seconds),
            daysLabel: (date_conf.days_text)       != '' ? date_conf.days_text    : 'Days',
            hoursLabel: (date_conf.hours_text)     != '' ? date_conf.hours_text   : 'Hours',
            minutesLabel: (date_conf.minutes_text) != '' ? date_conf.minutes_text : 'Minutes',
            secondsLabel: (date_conf.seconds_text) != '' ? date_conf.seconds_text : 'Seconds',
            onChange: function() {
                countEverestAnimate( $("#"+date_id).find('.ce-digits span') );
            }
        });

        function countEverestAnimate($el) {
            $el.each( function(index) {
                var $this = $(this),
                    fieldText = $this.text(),
                    fieldData = $this.attr('data-value'),
                    fieldOld = $this.attr('data-old');

                if (typeof fieldOld === 'undefined') {
                    $this.attr('data-old', fieldText);
                }

                if (fieldText != fieldData) {
                    
                    $this
                        .attr('data-value', fieldText)
                        .attr('data-old', fieldData)
                        .addClass('ce-animate');

                    window.setTimeout(function() {
                        $this
                            .removeClass('ce-animate')
                            .attr('data-old', fieldText);
                    }, 300);
                }
            });
        }
    });

});

// Function to get difference between two dates
function getdifference(t){
    
    material                  = [];
    material['days']          = 0;
    material['hours']         = 0;
    material['minutes']       = 0;
    material['seconds']       = 0;
    material['total_seconds'] = 0;
    
    var now = new Date();
    
    if(t > now){
        
        // get total seconds between the times
        var delta = Math.abs(t - now) / 1000;

        // calculate (and subtract) whole days
        var days = Math.floor(delta / 86400);
        delta -= days * 86400;
        material['days']= days;

        // calculate (and subtract) whole hours
        var hours = Math.floor(delta / 3600) % 24;
        delta -= hours * 3600;
        material['hours']= hours;

        // calculate (and subtract) whole minutes
        var minutes = Math.floor(delta / 60) % 60;
        delta -= minutes * 60;
        material['minutes']= minutes;

        // what's left is seconds
        var seconds = delta % 60;
        material['seconds']= seconds;

        var total_seconds = (t.getTime() - now.getTime()) / 1000;
        material['total_seconds'] = total_seconds;

        return material;
    }
    
    

    return material;
}