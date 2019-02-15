(function ($,SensorLogger) {

    SensorLogger = {
        $sidebar : $('#app-sidebar'),
        $buttons : $('.app-sensorlogger > .button')
    }


    SensorLogger.SideBar = function(settings)  {
        if ( settings.action === 'toggle') {
            SensorLogger.$sidebar.toggle();
        }
        if ( action === 'hide') {
            SensorLogger.sidebar.hide();
        }
        if ( action === 'show') {
            SensorLogger.sidebar.show();
        }
    }
    
    SensorLogger.Buttons = function() {
        SensorLogger.buttons.each(function(index,element){
            /* assign to te elmenet what to do on click */
            let $element = $(element);
            $element.on('click',function () {
                if($element.hasClass('.someclass'))  {
                    /* what should be done */
                }
            })
        })
    }

    SensorLogger.initialize = function() {
        /* define what to initialize */
        SensorLogger.Buttons();
        SensorLogger.SideBar();
    };

    SensorLogger.initialize();

})($,SensorLogger);

