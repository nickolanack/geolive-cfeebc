var dateToPercent = function(time) {
    return Math.round((time / span) * 100.0);
}


var min = (new Date(range[0])).toISOString().split('T')[0].substring(0, 10);
var max = (new Date(span + range[0])).toISOString().split('T')[0].substring(0, 10);

filterManager.query(
    AttributeFilter.InsersectFilter('markerAttributes', [{
        field: 'sessionDate',
        comparator: 'greatorThanOrEqualTo',
        value: min,
        format: 'date'
    }, {
        field: 'sessionDate',
        comparator: 'lessThanOrEqualTo',
        value: max,
        format: 'date'
    }]), {
        json: {
            format: ['id', 'attribute.*'],
            show: ['matches'],
            group: []
        }
    },
    function(result) {
        var events = [];
        Object.each(result.results.matches, function(match) {

            var startTime = (new Date(match.sessionDate)).getTime();

            var startOffset = startTime - range[0];
            var startPercent = dateToPercent(startOffset);
            var startPercent = startPercent % 100;
            if (startPercent < 0) {
                var startPercent = startPercent + 100;
            }

            var months = {
                '01': 'Jan',
                '02': 'Feb',
                '03': 'Mar',
                '04': 'Apr',
                '05': 'May',
                '06': 'Jun',
                '07': 'Jul',
                '08': 'Aug',
                '09': 'Sep',
                '10': 'Oct',
                '11': 'Nov',
                '12': 'Dec'

            }



            var suffix = function(n) {
                if (n === 1 || n == 21 || n == 31) {
                    return 'st';
                }
                if (n === 2 || n == 22) {
                    return 'nd';
                }
                if (n === 3 || n == 23) {
                    return 'rd';
                }

                return 'th';
            }

            events.push({
                start: (function() {

                    var d = match.sessionDate.split('-');
                    return months[d[1]] + ' ' + parseInt(d[2]) + suffix(d[2]);


                })(),
                percent: startPercent,
                label: 'event',
                onclick: function() {
                    GeoliveSearch.SearchAndOpenMapItem(application, match.id, match.lid);

                },
                popover: function(p) {

                    var marker = application.getLayerManager().filterMarkerById(match.id);
                    if (marker) {
                        p.setText(marker.getTitle());
                    }

                    //going to search for items name, and then updated the display text.

                }
            });
        });


        var todayTime = (new Date()).getTime();
        var todayOffset = todayTime - range[0];
        var todayPercent = dateToPercent(todayOffset);



        //event class: a, b, c, and d are used to alter the height and label directions using css

        events.sort(function(a, b) {
            return a.percent - b.percent;
        });



        events.push({
            start: 'today',
            percent: todayPercent,
            label: 'today',
            onclick: false,
            popover: false,
            'class': 'today'
        });

        var pinDecorator = new AttributeTimelineSliderPinDecorator(container, {
            events: events
        });

    }

);