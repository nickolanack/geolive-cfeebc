var dateToPercent=function(time){
    return Math.round((time/span)*100.0);
}


var eventsBar=container.appendChild(new Element('div', {'class':'events-bar'}));




var min=(new Date(range[0])).toISOString().split('T')[0].substring(0,10);
var max=(new Date(span+range[0])).toISOString().split('T')[0].substring(0,10);

filterManager.query(
	AttributeFilter.InsersectFilter('markerAttributes',
		[
            {
                field:'sessionDate',
                comparator:'greatorThanOrEqualTo',
                value:min,
                format:'date'
            },
            {
                field:'sessionDate',
                comparator:'lessThanOrEqualTo',
                value:max,
                format:'date'
            }
        ]
	),
    {
	    json:{
		    format:['id', 'attribute.*'],
		    show:['matches'],
		    group:[]
	    }
    },
    function(result){
        var events=[];
        Object.each(result.results.matches,function(match){

        	var startTime=(new Date(match.sessionDate)).getTime();

            var startOffset=startTime-range[0];
            var startPercent=dateToPercent(startOffset);
            var startPercent=startPercent%100;
            if(startPercent<0){
                var startPercent=startPercent+100;
            }


            events.push({
                start:match.sessionDate,
                percent:startPercent,
                label:'event',
                onclick:function(){
                    GeoliveSearch.SearchAndOpenMapItem(application, match.id, match.lid);

                },
                popover:function(p){

                        var marker=application.getLayerManager().filterMarkerById(match.id);
                        if(marker){
                            p.setText(marker.getTitle());
                        }

                	//going to search for items name, and then updated the display text.

                }
            });
        });


        var todayTime=(new Date()).getTime();
        var todayOffset=todayTime-range[0];
        var todayPercent=dateToPercent(todayOffset);




        //event class: a, b, c, and d are used to alter the height and label directions using css

        events.sort(function(a, b){
    	   return a.percent-b.percent;
        });




        events.push({
            start:'today',
            percent:todayPercent,
            label:'today',
            onclick:false,
            popover:false,
            'class':'today'
        });

        Array.each(events, function(event){

        	var pinOpts={
                    'class':'e-'+event.start+' '+(event['class']||'a'),
                    styles:{
                        left:event.percent+'%',
                    }
                };

            if(event.start!==false){
                pinOpts['data-label']=event.start;
            }
            var pin =eventsBar.appendChild(new Element('div', pinOpts));
            if(event.onclick){
                pin.addEvent('click', event.onclick);
            }
            if(event.popover){
                pin.addEvent('mouseover:once',function(){

                	var popover=new UIPopover(pin, {
                        anchor:UIPopover.AnchorTo(['top']),
                        title:'',
                        description:event.label//,
                        //hideDelay:500,
                        //margin:50
                    });

                event.popover(popover);
                popover.show();

                });
            }
            event.pin=pin;
        });

        for(var i=1;i<events.length-1;i++){

    	    if(events[i].start===events[i-1].start){
      	      events[i].pin.addClass('dplct');
            }
        }

       //skip first and last

        for(var i=0;i<events.length-1;i++){

    	    if(events[i].percent<events[events.length-1].percent){

      	      events[i].pin.addClass('past');

            }
       }

       var stagger=function(){
           for(var i=1;i<events.length-1;i++){

        	    if(events[i].percent-events[i-1].percent<10){


        	      var classes=['a', 'b', 'c', 'd'];
      	    	  for(var j=0;j<classes.length;j++){
      	    	     if(events[i-1].pin.hasClass(classes[j])){
      	    	    	events[i].pin.addClass(classes[(j%(classes.length-1))+1]); //adds: b, c, d, b, c, d ...
      	    	    	events[i].pin.removeClass('a');
      	    	    	break;
          	    	 }
                  }

                }
           }

       };
       stagger();

    }

);