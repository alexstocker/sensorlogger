(function() {

    if (!OCA.SensorLogger) {
        /**
         * Namespace for the sensorlogger app
         * @namespace OCA.SensorLogger
         */
        OCA.SensorLogger = {};
    }



	OCA.SensorLogger.Filter = {
		filter: undefined,
		$navigation: $('#app-navigation'),

		_onPopState: function(params) {
			params = _.extend({
				filter: 'all'
			}, params);

			this.setFilter(params.filter);
		},

		setFilter: function (filter) {
			if (filter === this.filter) {
				return;
			}
		}
	};

    OCA.SensorLogger = {

    	widgets: {
    	    gridstack: null,
        },

        sidebar: {
    	    infoContainer: {
    	        title: null,
                body: {}
            },
            tabsContainer: {
    	        tabHeaders: {},
                tabs: {}
            },
            footerContainer: {
    	        actions: {
    	            save: null,
                    close: null,
                    delete: null
                }
            }
        },

        actionButtons: {
    	    addWidget: null,
        },

        Sidebar: null,

        DetailTabs: function(type,sidebar) {
    	    console.log(type);
    	    console.log(sidebar);
    	    if(type === 'deviceList') {

            }
        },

        loadChart: function(plotArea,id,url,data) {
            $.getJSON(url,data,"json").success(function(data) {
                var dataLines = [];
                var serieslabel = [];
                var line1 = [];
                var line2 = [];

                if(data.dataTypes && data.logs) {
                    if (data.logs[0] && data.logs[0].data.length > 0) {
                        var lines = data.logs[0].data;
                        $.each(data.dataTypes, function (index, item) {
                            serieslabel.push(
                                {
                                    'series':item
                                }
                            )
                        });

                        for (var i = 0; i < lines.length; i++) {
                            dataLines[i] = [];
                            $.each(data.logs, function (index, item) {
                                var xaxis = item.createdAt;
                                var ydata = item.data[i];

                                if (ydata && ydata.value) {
                                    ydata = ydata.value;
                                    dataLines[i].push([xaxis, parseFloat(ydata)])
                                }
                            });

                            var clonedPlotArea = plotArea.clone();
                            if(plotArea.parent().hasClass('widget')) {
                                clonedPlotArea.attr('id', 'chart-' + i).appendTo(plotArea.parent());
                            } else {
                                clonedPlotArea.attr('id', 'chart-' + i).appendTo('#app-content-wrapper');
                            }

                        }
                    }
                }

                options = {
                    grid: {
                        backgroundColor: "white",
                    },
                    axesDefaults: {
                        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                    },
                    seriesDefaults: {
                        lineWidth: 2,
                        style: 'square',
                        rendererOptions: { smooth: false }
                    },
                    highlighter: {
                        show: true,
                        sizeAdjust: 7.5
                    }
                };

                if(dataLines.length > 0) {
                    for (var dataLine in dataLines) {
                        $.jqplot("chart-"+dataLine,[dataLines[dataLine]], $.extend(options, {
                                //title: 'GRAPT TITLE',
                                axes: {
                                    xaxis:{
                                        //label:"x axis",
                                        renderer:$.jqplot.DateAxisRenderer,
                                        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                                        tickOptions:{formatString:'%H:%M:%S', angle: -45}
                                    },
                                    yaxis:{
                                        tickOptions:{
                                            formatString:'%.2f'
                                        }
                                    }
                                },
                                series: [{
                                    label: data.dataTypes[dataLine].description+' ['+data.dataTypes[dataLine].short+']'
                                }],
                                legend:{
                                    renderer: $.jqplot.EnhancedLegendRenderer,
                                    show: true,
                                    showLabels: true,
                                    location: 's',
                                    placement: 'inside',
                                    fontSize: '11px',
                                    fontFamily: ["Lucida Grande","Lucida Sans Unicode","Arial","Verdana","sans-serif"],
                                    rendererOptions: {
                                        seriesToggle: 'normal'
                                    }
                                }
                            })
                        )
                    }
                }

                drawableLines = [];
                if(dataLines.length < 1) {
                    $.each(data, function (index, item) {
                        line1.push([item.createdAt, parseFloat(item.temperature)])
                        line2.push([item.createdAt, parseFloat(item.humidity)])
                    });
                    drawableLines.push(line1)
                    drawableLines.push(line2)
                } else {
                    drawableLines = dataLines;
                }


                try {
                    OCA.SensorLogger.plotChart(plotArea,drawableLines,serieslabel);

                    $("a#toggle_realtime").on('click',function(){
                        plotArea = $(this).attr('data-plotarea');
                        doUpdate();
                    });
                } catch (err) {
                    $(plotArea).html(err);
                }
            });
        },

        plotChart: function(plotArea,drawableLines,labels) {
            var series = [];
            if(labels.length > 0) {
                //console.log(labels);
                for (var label in labels) {
                    var serie = {
                        yaxis: 'yaxis',
                        tickInterval: 1,
                        showMarker:true,
                        label: labels[label].series.description+' ['+labels[label].series.type+']'
                    };
                    series.push(serie);
                }


            } else {
                series = [
                    {
                        yaxis: 'yaxis',
                        tickInterval: 0.5,
                        showMarker:false,
                        label: 'Temperature [°C]',
                        color: 'red'
                        //markerOptions: {size: 2, style: "x"}
                    },{
                        yaxis: 'y2axis',
                        showMarker:false,
                        tickInterval: 1,
                        label: 'Humidity [%]',
                        color: 'blue'
                        //markerOptions: {style:"circle"}
                    },
                    {yaxis: 'yaxis'},
                    {yaxis: 'y2axis'},
                ]
            }
            var plot = $.jqplot($(plotArea).attr('id'),drawableLines,{

                height: 240,
                axes: {
                    xaxis:{
                        //label:"x axis",
                        renderer:$.jqplot.DateAxisRenderer,
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                        tickOptions:{formatString:'%H:%M:%S', angle: -10}
                    },
                    yaxis:{
                        tickOptions:{
                            formatString:'%.2f'
                        }
                    }
                },
                highlighter: {
                    show: true,
                    sizeAdjust: 7.5
                },
                series: series,
                cursor: {
                    show: true,
                    tooltipLocation:'sw',
                    zoom:true
                },
                seriesDefaults: {
                    rendererOptions: { smooth: false }
                },
                axesDefaults: {
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                },
                legend:{
                    renderer: $.jqplot.EnhancedLegendRenderer,
                    show: true,
                    showLabels: true,
                    //location: 's	',
                    placement: 'inside',
                    fontSize: '11px',
                    fontFamily: ["Lucida Grande","Lucida Sans Unicode","Arial","Verdana","sans-serif"],
                    rendererOptions: {
                        seriesToggle: 'normal'
                    }
                }
            });
            $('a#zoom_reset').click(function() { plot.resetZoom() });
        },

        DeviceList: function (tbody) {
            /*
    	    tbody.on('click','.deviceEdit, ' +
                '.deviceactions > a.action-share, ' +
                '.deviceactions > a.action-menu', function(e) {
                console.log(e.target);
            });
            */
            var deviceRows = tbody.find('tr');
            $.each(deviceRows,function(idx,row){
               return OCA.SensorLogger.DeviceActions($(row).data('id'),row);
            });
        },
        Navigation: function (appNavigation) {
            this.active = appNavigation.find('a.active');
            this.activeType = this.active.closest('li').prop('id');
            if(this.activeType === 'showDashboard') {
                $('li.actions a.add-widget').on('click',function(e) {
                    OCA.SensorLogger.SidebarBuilder('widgets');
                    OCA.SensorLogger.Sidebar.show();
                });
            }
        },
        DeviceActions: function (deviceId,element) {
            $(element).on('click','a', function(e) {
                //console.log($(e.target).closest('a').data('action'));
                var action = $(e.target).closest('a').data('action');

                if(action === 'Menu' || action === 'Share') {
                    OCA.SensorLogger.SidebarBuilder('deviceDetails');
                    OCA.SensorLogger.Sidebar.show();
                }
                if(action === 'Chart') {

                }
                if(action === 'DataList') {

                }

                var wipeOutUrl = OC.generateUrl('/apps/sensorlogger/wipeOutDevice');
                var deviceChartUrl = OC.generateUrl('/apps/sensorlogger/deviceChart/'+deviceId);
                var deviceDataUrl = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+deviceId);

            });
    	    //console.log('DeviceActions:'+deviceId);
        },
		Widgets: function(container) {
        	var widgets = container.find('.dashboard-widget');
        	$.each(widgets, function (idx,widget) {
                return OCA.SensorLogger.Widget($(widget));
			});
		},
        GridElements: function(widgetContainer) {

    	    //console.log(widgetContainer.find('div[data-widget-type]').data('widget-type'));
            //$('.grid-stack').gridstack({});
            OCA.SensorLogger.widgets.gridstack = widgetContainer.gridstack({
                itemClass: 'column',
                //  disableResize: true
            });

            OCA.SensorLogger.widgets.gridstack.on('change', function(event, items) {
                var updateUrl = OC.generateUrl('/apps/sensorlogger/updateWidgetSettings');

                var data = {};
                for (var idx in items) {
                    var widgetType = $(items[idx].el).children().data('widget-type');
                    var deviceId = $(items[idx].el).children().data('id')

                    //console.log($(items[idx].el).children().data('widget-type'));

                    data[idx] = {
                        key:items[idx].id,
                        x: items[idx].x,
                        y: items[idx].y,
                        h: items[idx].height,
                        w: items[idx].width,
                        widget_type:widgetType,
                        device_id:deviceId
                    }
                }

                //console.log(data);

                $.post(updateUrl,data).success(function (response) {
                    if(response.success) {
                        //container.remove();
                        //OC.Notification.showTemporary(t('sensorlogger', 'Dashboard widget deleted'));
                    }
                });

                /*
                OCA.SensorLogger.sidebar.footerContainer.actions.saveBtn.on('click',function() {

                    $('.editable').editable('submit', {
                        url: saveWidget,
                        ajaxOptions: {
                            dataType: 'json' //assuming json response
                        },
                        success: function(data, config) {
                            if(data && data.id) {
                                $(this).editable('option', 'pk', data.id);
                                OCA.SensorLogger.Sidebar.hide();
                                OC.Notification.showTemporary(t('sensorlogger', 'Dashboard widget saved'));
                                //showDashboard[0].click();
                                location.reload(true);
                            } else if(data && data.errors){
                                OC.Notification.showTemporary(t('sensorlogger', data.errors));
                            }
                        },
                        error: function(errors) {
                            if(errors && errors.responseText) {
                            } else {
                                $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
                            }
                            OC.Notification.showTemporary(t('sensorlogger', 'Some Error occured'));
                        }
                    });
                });
                */
            });

            OCA.SensorLogger.Widgets(widgetContainer);

            widgetContainer.on('click','a.widget-delete',function (e) {
                var id = $(e.target).data('id');
                var container = $(e.target).closest('div.column');
                var url = OC.generateUrl('/apps/sensorlogger/deleteWidget/'+id);
                $.post(url).success(function (response) {
                    if(response.success) {
                        container.remove();
                        OC.Notification.showTemporary(t('sensorlogger', 'Dashboard widget deleted'));
                    }
                });
            });
        },
		Widget: function (widgetContainer) {

            var widgetType = widgetContainer.data('widget-type');
            var dataId = widgetContainer.data('id');
            var startBtn = $('<button/>', {
                'text': 'Start'
            });
            var timeOutDd = $('<select/>', {
                'class':'realtimechart-timeout',
            });
            timeOutDd
                .append('<option value="1">1 sec.</option>')
                .append('<option value="5" selected>5 sec.</option>')
                .append('<option value="10">10 sec.</option>')
                .append('<option value="30">30 sec.</option>')
                .append('<option value="60">60 sec.</option>');

            var dataTypeDd = $('<select/>', {
                'class':'realtimechart-datatype'
            });

            var liveIndicator = $('<div/>', {
                "class":"pulse"
            });

            //console.log(widgetContainer.data('widget-type'));
    		if(widgetType === 'chart') {
    		    //console.log('It is a chart '+dataId);
                var dataUrl = OC.generateUrl('/apps/sensorlogger/chartData/' + dataId);
                var chartContainer = widgetContainer.find('.chartcontainer');
                var data = {"limit":1280};
                plotArea = chartContainer;
                OCA.SensorLogger.loadChart(chartContainer,dataId,dataUrl,data);
			} else if (widgetContainer.data('widget-type') === 'list') {

			} else if (widgetContainer.data('widget-type') === 'last') {

            } else if (widgetContainer.data('widget-type') === 'realTimeLast') {
                var realTimeContainer = widgetContainer.find('.realTimeLast');
                if(!$('#realTimeLast-'+widgetContainer.data('id'))
                        .hasOwnProperty('length')) {

                    var realTimeLastId = 'realTimeLast-'+widgetContainer.data('id');
                    var realTimeLast = $('<div/>', {
                        'id': realTimeLastId
                    });

                    var dataElement = $('<h3/>');

                    realTimeContainer.append(realTimeLast);
                    realTimeContainer.prepend(timeOutDd);
                    realTimeContainer.prev('h2').prepend(liveIndicator);


                    var doAjax = function () {
                        var t = timeOutDd[0].options[timeOutDd[0].selectedIndex].value * 1000;

                        $.ajax({
                            url: 'lastLog/' + widgetContainer.data('id'),
                            data: {'limit': 1},
                            type: 'GET',
                            success: function (response) {
                                if (response) {
                                    if (response.dataTypes) {

                                        realTimeLast.empty();

                                        var logData = response.logs[0].data;
                                        var dataTypes = response.dataTypes;

                                        var dataTypeElement = $('<small/>');

                                        for (var typeKey in dataTypes) {
                                            var dataTypeId = dataTypes[typeKey].id;
                                            for (var logKey in logData) {
                                                var logDataTypeId = logData[logKey].dataTypeId;

                                                if(parseInt(dataTypeId) === parseInt(logDataTypeId)) {
                                                    var dataElem = dataElement
                                                        .clone()
                                                        .text(logData[logKey].value+' '+dataTypes[typeKey].short);

                                                    var dataTypeElem = dataTypeElement
                                                        .clone()
                                                        .text(dataTypes[typeKey].description);

                                                    dataElem.appendTo(realTimeLast);
                                                    dataTypeElem.appendTo(realTimeLast);
                                                }

                                            }

                                        }

                                    } else {

                                        realTimeLast.empty();
                                        var date = dataElement.clone().text(response[0].createdAt);
                                        var humidity = dataElement.clone().text(response[0].humidity+' % r.F.');
                                        var temperature = dataElement.clone().text(response[0].temperature+ ' °C');

                                        temperature.appendTo(realTimeLast);
                                        humidity.appendTo(realTimeLast);
                                        date.appendTo(realTimeLast);

                                    }
                                    setTimeout(doAjax, t);
                                }
                            }
                        });
                    };

                    doAjax();

                }

            } else if (widgetContainer.data('widget-type') === 'realTimeChart') {
                var chartContainer = widgetContainer.find('.chartcontainer');

                if(!$('#realTimePlotArea-'+widgetContainer.data('id'))
                        .hasOwnProperty('length')) {

                    var t = 5000;
                    var serie;

                    var plotAreaId = 'realTimePlotArea-'+widgetContainer.data('id');
                    var plotArea = $('<div/>', {
                        'id': plotAreaId
                    });

                    startBtn.click(function(){
                        t = timeOutDd[0].options[timeOutDd[0].selectedIndex].value * 1000;
                        serie = dataTypeDd[0].options[dataTypeDd[0].selectedIndex].value;
                        doUpdate(plotAreaId);
                        $(this).hide();
                        $('#realTimeChartTimeOut-'+widgetContainer.data('id')).hide();
                        $('#realTimeChartDataType-'+widgetContainer.data('id')).hide();
                    });

                    timeOutDd.attr('id','realTimeChartTimeOut-'+widgetContainer.data('id'));
                    dataTypeDd.attr('id','realTimeChartDataType-'+widgetContainer.data('id'));

                    chartContainer.append(plotArea);
                    chartContainer.append(timeOutDd);
                    chartContainer.append(startBtn);

                    var n = 20;
                    var x = (new Date()).getTime(); // current time

                    var data = [];
                    for(i=0; i<n; i++){
                        data.push([x - (n-1-i)*t,0]);
                    }

                    var options = {
                        axes: {
                            xaxis: {
                                numberTicks: 4,
                                renderer:$.jqplot.DateAxisRenderer,
                                tickOptions:{formatString:'%H:%M:%S'},
                                min : data[0][0],
                                //max : data[19][0]
                                max: data[data.length-1][0]
                            },
                            yaxis: {min:-10, max: 40,numberTicks: 6,
                                tickOptions:{formatString:'%.1f'}
                            }
                        },
                        seriesDefaults: {
                            rendererOptions: { smooth: true}
                        },
                        highlighter: {
                            show: true,
                            sizeAdjust: 7.5
                        },
                        legend:{
                            renderer: $.jqplot.EnhancedLegendRenderer,
                            show: true,
                            showLabels: true,
                            location: 'n',
                            placement: 'inside',
                            fontSize: '11px',
                            fontFamily: ["Lucida Grande","Lucida Sans Unicode","Arial","Verdana","sans-serif"],
                            rendererOptions: {
                                seriesToggle: 'normal'
                            }
                        }
                    };

                    $.ajax({
                        url:'lastLog/'+widgetContainer.data('id'),
                        data:  {'limit': n},
                        type: 'GET',
                        async: 'false',
                        success: function(response) {
                            if(response) {
                                if(response.dataTypes) {
                                    for(var dataType in response.dataTypes) {
                                        dataTypeDd
                                            .append('<option value="'+response.dataTypes[dataType].id+'">'+response.dataTypes[dataType].description+'</option>');
                                    }
                                } else {
                                    dataTypeDd
                                        .append('<option value="temperature">Temperature</option>')
                                        .append('<option value="humidity">Humidity</option>');
                                }
                                dataTypeDd.insertAfter(plotArea);
                            }
                        }
                    });

                    var plot1 = $.jqplot(plotAreaId, [data], options);
                    plot1.series[0].data = data;

                    var doUpdate = function() {

                        if(data.length > n-1){
                            data.shift();
                        }

                        $.ajax({
                            url:'lastLog/'+widgetContainer.data('id'),
                            data:  {'limit': 1},
                            type: 'GET',
                            success: function(response) {
                                if(response) {
                                    var time,x,y;
                                    if(response.dataTypes) {
                                        time = response.logs[0].createdAt.split(/[- :]/);
                                        x = new Date(time[0], time[1]-1, time[2], time[3], time[4], time[5]).getTime();
                                        //y = response.logs[0].data[serie];

                                        var logData = response.logs[0].data;

                                        for (var key in logData) {
                                            if(logData[key].dataTypeId == serie) {
                                                y = logData[key].value;
                                            }
                                        }

                                        for (var dataType in response.dataTypes) {
                                            if(response.dataTypes[dataType].id == serie) {
                                                options.series = [{
                                                    label: response.dataTypes[dataType].description + ' ['+response.dataTypes[dataType].short+']'
                                                }];
                                            }
                                        }

                                        if(y < options.axes.yaxis.min) {
                                            options.axes.yaxis.min = y;
                                        }
                                        if(y > options.axes.yaxis.max) {
                                            options.axes.yaxis.max = y;
                                        }

                                        data.push([x,y]);
                                        if (plot1) {
                                            plot1.destroy();
                                        }

                                    } else {
                                        time = response[0].createdAt.split(/[- :]/);
                                        x = new Date(time[0], time[1]-1, time[2], time[3], time[4], time[5]).getTime();
                                        y = response[0][serie];

                                        if(y < options.axes.yaxis.min) {
                                            options.axes.yaxis.min = y;
                                        }
                                        if(y > options.axes.yaxis.max) {
                                            options.axes.yaxis.max = y;
                                        }

                                        options.series = [{
                                            label: serie
                                        }];

                                        data.push([x,y]);
                                        if (plot1) {
                                            plot1.destroy();
                                        }
                                    }

                                    plot1.series[0].data = data;
                                    options.axes.xaxis.min = data[0][0];

                                    if(data[0][0] > data[data.length-1][0]) {
                                        options.axes.xaxis.min = x - (n-1)*t;
                                    }

                                    options.axes.xaxis.max = data[data.length-1][0];
                                    plot1 = $.jqplot (plotAreaId, [data],options);
                                    setTimeout(doUpdate, t);
                                }
                            }
                        });
                    }
                }
            } else if(widgetContainer.data('widget-type') === 'max_values_24h') {
                realTimeContainer = widgetContainer.find('.max_values_24h');
                if(!$('#max_values_24h-'+widgetContainer.data('id'))
                        .hasOwnProperty('length')) {

                    realTimeLastId = 'max_values_24h-'+widgetContainer.data('id');
                    realTimeLast = $('<div/>', {
                        'id': realTimeLastId
                    });

                    dataElement = $('<h3/>');

                    realTimeContainer.append(realTimeLast);
                    realTimeContainer.prepend(timeOutDd);
                    realTimeContainer.prev('h2').prepend(liveIndicator);


                    doAjax = function () {
                        var t = timeOutDd[0].options[timeOutDd[0].selectedIndex].value * 1000;

                        $.ajax({
                            url: 'maxLog/' + widgetContainer.data('id')+'/24',
                            data: {'limit': 1},
                            type: 'GET',
                            success: function (response) {
                                if (response) {
                                    if (response.dataTypes && response.logs !== null) {
                                        realTimeLast.empty();

                                        var logData = response.logs.data;
                                        var dataTypes = response.dataTypes;

                                        var dataTypeElement = $('<small/>');

                                        for (var typeKey in dataTypes) {
                                            var dataTypeId = dataTypes[typeKey].id;
                                            for (var logKey in logData) {
                                                var logDataTypeId = logData[logKey].dataTypeId;

                                                if(parseInt(dataTypeId) === parseInt(logDataTypeId)) {
                                                    var dataElem = dataElement
                                                        .clone()
                                                        .text(logData[logKey].value+' '+dataTypes[typeKey].short);

                                                    var dataTypeElem = dataTypeElement
                                                        .clone()
                                                        .text(dataTypes[typeKey].description);

                                                    dataElem.appendTo(realTimeLast);
                                                    dataTypeElem.appendTo(realTimeLast);
                                                }

                                            }

                                        }

                                    } else if(response.humidity && response.temperature) {
                                        //console.log(response);
                                        realTimeLast.empty();
                                        //var date = dataElement.clone().text(response[0].createdAt);
                                        var humidity = dataElement.clone().text(response.humidity+' % r.F.');
                                        var temperature = dataElement.clone().text(response.temperature+ ' °C');

                                        temperature.appendTo(realTimeLast);
                                        humidity.appendTo(realTimeLast);
                                        //date.appendTo(realTimeLast);

                                    } else {

                                    }
                                    setTimeout(doAjax, t);
                                }
                            }
                        });
                    };

                    doAjax();

                }
			} else {

            }
        },

        SidebarWidgets: function () {
            OCA.SensorLogger.sidebar.footerContainer.actions.saveBtn = $('a#save-btn');
            var saveWidget = OC.generateUrl('/apps/sensorlogger/saveWidget');
            OCA.SensorLogger.sidebar.footerContainer.actions.saveBtn.on('click',function() {
                var loadingWidget = $('<div/>',{
                	'class':'column'
				}).append('<div class="widget dashboard-widget dragbox"><div class="widget icon-loading"></div></div>');
				OCA.SensorLogger.widgets.gridstack.data('gridstack').addWidget(loadingWidget,0,0,4,4,true);
                $('.editable').editable('submit', {
                        url: saveWidget,
                        ajaxOptions: {
                            dataType: 'json' //assuming json response
                        },
                        data: {x: loadingWidget.data('gsX'),y:loadingWidget.data('gsY')},
                        success: function(data, config) {
                            if(data && data.id) {
                                $(this).editable('option', 'pk', data.id);
                                OCA.SensorLogger.Sidebar.hide();
                                OC.Notification.showTemporary(t('sensorlogger', 'Dashboard widget saved'));
                                location.reload(true);
                            } else if(data && data.errors){
                                OC.Notification.showTemporary(t('sensorlogger', data.errors));
                            }
                        },
                        error: function(errors) {
                            if(errors && errors.responseText) {
                            } else {
                                $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
                            }
                            OC.Notification.showTemporary(t('sensorlogger', 'Some Error occured'));
                        }
                    });
            });

            var widgetDataUrl = OC.generateUrl('/apps/sensorlogger/widgetTypeList');
            $.get(widgetDataUrl).success(function (response) {
                //console.log(OCA.SensorLogger.sidebar.footerContainer.actions.saveBtn);
                OCA.SensorLogger.sidebar.footerContainer.actions.saveBtn.show();
                $.fn.editable.defaults.mode = 'inline';
                var sidebarBody = OCA.SensorLogger.Sidebar.find('.body');
                var sidebarTitle = OCA.SensorLogger.Sidebar.find('.title');

                var widgetTypeSource = [];
                for (var key in response.widgetTypes) {
                    widgetTypeSource.push({
                            value : key,
                            text: response.widgetTypes[key].displayName,
                            id : key
                        }
                    );
                }

                var deviceSource = [];
                for (var key in response.devices) {
                    deviceSource.push({
                            value : response.devices[key].id,
                            text: response.devices[key].name,
                            id : response.devices[key].id,
                        }
                    );
                }

                var widgetTypeSelect = $('<a/>',{
                    'id':'widget_type',
                    'href': '#',
                    'data-type': 'select2',
                    'data-url': '',
                    'data-title': 'Select widget type'
                }).editable({
                    source: widgetTypeSource,
                    select2: {
                        placeholder: 'Select an option',
                        minimumResultsForSearch: Infinity,
                        multiple: false,
                        data: widgetTypeSource,
                        dropdownAutoWidth: true
                    }
                });

                var widgetTypeLabel = $('<label/>', {
                    'class':'widget-type'
                }).text(t('sensorlogger', 'Select Widget Type'));
                var widgetTypeContentSpan = $('<span/>', {
                    'class':'widget-type-content'
                }).append(widgetTypeSelect);

                var deviceSelect = $('<a/>',{
                    'id':'device_id',
                    'href': '#',
                    'data-type': 'select2',
                    'data-url': '',
                    'data-title': 'Select device'
                }).editable({
                    source: deviceSource,
                    select2: {
                        placeholder: 'Select an option',
                        minimumResultsForSearch: Infinity,
                        multiple: false,
                        data: deviceSource,
                        dropdownAutoWidth: true
                    }
                });

                var deviceLabel = $('<label/>', {
                    'class':'device'
                }).text(t('sensorlogger', 'Select Device'));
                var deviceContentSpan = $('<span/>', {
                    'class':'device-content'
                }).append(deviceSelect);

                var bodyDetailsContainer = OCA.SensorLogger.Sidebar.find('.tpl_bodyDetails').clone();
                bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

                OCA.SensorLogger.Sidebar.find('.bodyDetails').remove();

                var widgetType = bodyDetailsContainer.clone()
                    .append(widgetTypeLabel)
                    .append(widgetTypeContentSpan);
                var device = bodyDetailsContainer.clone()
                    .append(deviceLabel)
                    .append(deviceContentSpan);

                sidebarBody.append(widgetType);
                sidebarBody.append(device);
                //wipeOutBtn.hide();

                //console.log(OCA.SensorLogger.Sidebar);

                sidebarTitle.empty().append('Dashboard Widget');

            })
        },

        SidebarBuilder: function (type) {
    	    if(type === 'widgets') {
    	        OCA.SensorLogger.SidebarWidgets(OCA.SensorLogger.Sidebar)
            }
    	    console.log('SidebarBuilder: '+type);
            if ( type === 'deviceList' ) {
                return $('a.action-share').each(function(idx,shareDeviceElement){
                    $(shareDeviceElement).on('click',function(event) {
                        console.log($(event.target));
                    });
                })
            }
            OCA.SensorLogger.Sidebar.find('a#close-btn').on('click', function() {
                OCA.SensorLogger.Sidebar.hide();
            })
        }
	};

	OCA.SensorLogger.App = {

		navigation: null,
		devices: null,
		widgets: null,
        sidebar : null,
        saveBtn : $('#save-btn'),
        showList : $('#showList'),
        showDashboard : $('#showDashboard'),
        dashboardGrid: null,
        deviceList : null,
        deviceTypeList : $('#deviceTypeList'),
        deviceGroupList : $('#deviceGroupList'),
        dataTypeList : $('#dataTypeList'),
        appContentWrapper : null,

		initialize: function() {
            this.sidebar = $('#app-sidebar');
            this.appContentWrapper = $('#app-content-wrapper');
            OCA.SensorLogger.Sidebar = this.sidebar;
			this.navigation = new OCA.SensorLogger.Navigation($('#app-navigation'));

            if ( this.navigation.activeType === 'showDashboard' ) {
                this.dashboardGrid = new OCA.SensorLogger.GridElements($('#widget-wrapper'));
            }

            if ( this.navigation.activeType === 'deviceList' ) {
                var tbody = this.appContentWrapper.find($('tbody#deviceList'));
                this.deviceList = new OCA.SensorLogger.DeviceList(tbody);
            }

			//OCA.SensorLogger.App.sidebar = $('#app-sidebar');

            //var urlParams = OC.Util.History.parseUrlQuery();
            //this.View = this.navigation.activeType;


            //var deviceActions = new OCA.SensorLogger.DeviceActions();
			//console.log(deviceActions);
            //console.log(this.navigation.activeType);
            //this.sidebarTabs = new OCA.SensorLogger.DetailTabs(this.navigation.activeType,OCA.SensorLogger.App.sidebar);


            /*
			if ($('#deviceNotFound').val() === "1") {
				OC.Notification.showTemporary(t('sensorlogger', 'Device could not be found'));
			}

			this.devices = OCA.SensorLogger.Devices;

			this.deviceList = new OCA.SensorLogger.DeviceList(
				$('#app-content-devices'), {
					scrollContainer: $('#app-content-wrapper'),
					deviceActions: deviceActions,
					allowLegacyActions: true,
					scrollTo: urlParams.scrollto,
					sorting: {
						mode: $('#defaultDeviceSorting').val(),
						direction: $('#defaultDeviceSortingDirection').val()
					}
				}
			);
			*/
		}

	}

})();
$(function () {
    //$('.grid-stack').gridstack({});
});
$(document).ready(function() {
	_.defer(function() {
        OCA.SensorLogger.App.initialize();
	});
});

