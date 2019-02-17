(function ($,SensorLogger) {

    SensorLogger = {
        $sidebar : $('#app-sidebar'),
        $buttons : $('.app-sensorlogger > .button')
    };

    SensorLogger.sidebar.widget = {
        elements: ['widget_type','device'],
        actions : ['save','close'],
        tabs: []
    };

    SensorLogger.sidebar.device = {
        elements: ['name','unique_id','device_group','device_type'],
        actions: ['wipeout', 'close'],
        tabs: ['details','share','notifications']
    };
    SensorLogger.sidebar.device.shareTab = {
        elements: ['share_with'],
        actions: ['share','revoke']
    };


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
            /* assign to the element what to do on click */
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

