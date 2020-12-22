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
        url: {
            updateDevice: '/apps/sensorlogger/updateDevice/',
            createdDeviceGroup: '/apps/sensorlogger/createDeviceGroup'
        },
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
                    delete: null,
                    wipeout: null,
                }
            },
        },
        actionButtons: {
    	    addWidget: null,
        },
        Sidebar: null,
        Content: null,
        DetailTabs: function(type,sidebar) {
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
    	    var options = {};
            var wipeOutUrl = OC.generateUrl('/apps/sensorlogger/wipeOutDevice');
            var deviceChartUrl = OC.generateUrl('/apps/sensorlogger/deviceChart/'+deviceId);
            var deviceDataUrl = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+deviceId);
            var deviceDetailsUrl = OC.generateUrl('/apps/sensorlogger/showDeviceDetails/'+deviceId);
            $(element).on('click','a', function(e) {
                var action = $(e.target).closest('a').data('action');
                if(action === 'Menu') {
                    options = {
                        id: deviceId,
                        url: deviceDetailsUrl
                    }
                    OCA.SensorLogger.SidebarBuilder('deviceDetails', options);
                }

                if(action === 'Share') {
                    OCA.SensorLogger.SidebarBuilder('deviceShare', options);
                }
                if(action === 'Chart') {
                    var url = OC.generateUrl('/apps/sensorlogger/deviceChart/'+deviceId);
                    $.get(url).success(function (response) {
                        $('#app-content-wrapper').empty().append(response);
                        var dataUrl = OC.generateUrl('/apps/sensorlogger/chartData/' + deviceId);
                        OCA.SensorLogger.loadChart($('div#chart'),$('div#chart').data('id'),dataUrl);
                    });
                }
                if(action === 'DataList') {
                    var url = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+deviceId);
                    $.post(url).success(function (response) {
                        var $response = $(response);
                        var $tbody = $response.find('tbody#logList');
                        OCA.SensorLogger.LogList($tbody);
                        OCA.SensorLogger.Content.empty().append($response);
                    });
                }
            });
        },
		Widgets: function(container) {
        	var widgets = container.find('.dashboard-widget');
        	$.each(widgets, function (idx,widget) {
                return OCA.SensorLogger.Widget($(widget));
			});
		},
        GridElements: function(widgetContainer) {
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

                $.post(updateUrl,data).success(function (response) {
                    if(response.success) {
                    }
                });
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
            var noDataMsg = '<div class="center">' +
                '<div class="icon-info"></div>' +
                '<span class="center">No data</span>' +
                '<p>Read' +
                '<a href="https://github.com/alexstocker/sensorlogger/wiki" ' +
                'title="SensorLogger Wiki" target="_blank">SensorLogger Wiki</a>' +
                '</p> </div>';

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

    		if(widgetType === 'chart') {
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

                                        if (response.logs.length <= 0)  {


                                            realTimeLast.append(noDataMsg);
                                            return false;
                                        }
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
    		    /** TODO: Remove max_values_24h */
                var removeNotice = '<div class="center">' +
                    '<div class="icon-info"></div>' +
                    '<span class="center">24h Max Value Widget @deprecated and will be removed soon. Disabled for performance reasons</span> ' +
                    '<p>Read ' +
                    '<a href="https://github.com/alexstocker/sensorlogger/issues/40" ' +
                    'title="SensorLogger Issue #40 - Data Aggregation and downsampling" target="_blank">SensorLogger Issue #40 - Data Aggregation and downsampling</a>' +
                    '</p> </div>';


                realTimeContainer = widgetContainer.find('.max_values_24h');
                if(!$('#max_values_24h-'+widgetContainer.data('id')).hasOwnProperty('length')) {

                    realTimeLastId = 'max_values_24h-'+widgetContainer.data('id');
                    realTimeLast = $('<div/>', {
                        'id': realTimeLastId
                    });

                    dataElement = $('<h3/>');

                    realTimeContainer.append(removeNotice);
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
                OCA.SensorLogger.sidebar.footerContainer.actions.saveBtn.show();
                $.fn.editable.defaults.mode = 'inline';
                var sidebarBody = OCA.SensorLogger.Sidebar.find('.body');
                var sidebarTitle = OCA.SensorLogger.Sidebar.find('.title');

                var widgetTypeSource = [];
                for (var key in response.widgetTypes) {
                    var disabled = false;
                    var displayName = response.widgetTypes[key].displayName;
                    if ( key === 'max_values_24h') {
                        disabled = true;
                        displayName = response.widgetTypes[key].displayName + ' @deprecated'
                    }
                    widgetTypeSource.push({
                            disabled: disabled,
                            value : key,
                            text: displayName,
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
                    showbuttons: false,
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
                    showbuttons: false,
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
                sidebarTitle.empty().append('Dashboard Widget');

            })
        },
        SidebarBuilder: function (type, options) {
            OCA.SensorLogger.App.Sidebar.destroy();
    	    if(type === 'widgets') {
    	        OCA.SensorLogger.SidebarWidgets(OCA.SensorLogger.Sidebar)
            }
            if ( type === 'deviceDetails' ) {
                OCA.SensorLogger.sidebar.footerContainer.actions.wipeout = $('a#wipeout-btn');
                $.post(options.url).success(function (response) {
                  OCA.SensorLogger.App.DeviceDetails(response);
                });
                OCA.SensorLogger.App.DeviceWipeout(options);
            }
            if ( type === 'deviceShare' ) {
                /** TODO: Feature to share device needs to be implemented **/
                OCA.SensorLogger.App.DeviceShare();
            }

            OCA.SensorLogger.Sidebar.find('a#close-btn').on('click', function() {
                OCA.SensorLogger.Sidebar.hide();
                OCA.SensorLogger.App.Sidebar.destroy();
            })
            OCA.SensorLogger.Sidebar.show();
        },
        LogList: function(tbody) {
            var logRows = tbody.find('tr');
            $.each(logRows,function(idx,row){
                return OCA.SensorLogger.LogActions($(row).data('id'),row);
            });
        },
        LogActions: function (deviceId, element) {
            $(element).on('click','a.log-delete',function (e) {
                var id = $(e.target).data('id');
                var container = $(e.target).closest('tr');
                var url = OC.generateUrl('/apps/sensorlogger/deleteLog/'+id);
                $.post(url).success(function (response) {
                    if(response.success) {
                        container.remove();
                        OC.Notification.showTemporary(t('sensorlogger', 'Record deleted'));
                    } else {
                        OC.Notification.showTemporary(t('sensorlogger', 'Record not deleted. Please try again.'));
                    }
                });
            });
        },
        DeviceTypeList: function (tbody) {
            var typeRows = tbody.find('tr');
            $.each(typeRows,function(idx,row){
                return OCA.SensorLogger.DeviceTypeActions($(row).data('id'),row);
            });
        },
        DeviceTypeActions: function (deviceTypeId, element) {
            $(element).on('click','a.devicetype-delete',function (e) {
                var id = $(e.target).data('id');
                var container = $(e.target).closest('tr');
                var url = OC.generateUrl('/apps/sensorlogger/deleteDeviceType/'+id);
                $.post(url).success(function (response) {
                    if(response.success) {
                        container.remove();
                        OC.Notification.showTemporary(t('sensorlogger', 'Device Type deleted'));
                    } else {
                        OC.Notification.showTemporary(t('sensorlogger', 'Device Type NOT deleted'));
                    }
                });
            });
        },
        DeviceGroupList: function (tbody) {
            var groupRows = tbody.find('tr');
            $.each(groupRows,function(idx,row){
                return OCA.SensorLogger.DeviceGroupActions($(row).data('id'),row);
            });
        },
        DeviceGroupActions: function (deviceGroupId, element) {
            $(element).on('click','a.devicegroup-delete',function (e) {
                var id = $(e.target).data('id');
                var container = $(e.target).closest('tr');
                var url = OC.generateUrl('/apps/sensorlogger/deleteDeviceGroup/'+id);
                $.post(url).success(function (response) {
                    if(response.success) {
                        container.remove();
                        OC.Notification.showTemporary(t('sensorlogger', 'Device Group deleted'));
                    } else {
                        OC.Notification.showTemporary(t('sensorlogger', 'Device Group NOT deleted'));
                    }
                });
            });
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
        deviceTypeList: null,
        logList: null,
        deviceTypeList : $('#deviceTypeList'),
        deviceGroupList : $('#deviceGroupList'),
        dataTypeList : $('#dataTypeList'),
        contentWrapper : null,
        Sidebar: {
		    destroy: function() {
                    $('.sidbarInfoView > .title').empty();
                    $('.sidbarInfoView > .body > .bodyDetails').remove();
            }
        },
        applyEditable: function (response, options) {
            $.fn.editable.defaults.mode = 'inline';

            return $('<a/>',{
                'id': options.id,
                'href': '#',
                'data-type': options.type,
                'data-field': options.field,
                'data-pk': response.deviceDetails.id,
                'data-url': options.url,
                'data-title': options.title,
                'text': options.text
            }).editable();
        },
        DeviceTitle: function (response) {
		    var options = {
		        id: 'name',
                type: 'text',
                field: 'group',
                title: response.name,
		        url: OC.generateUrl(OCA.SensorLogger.url.updateDevice + response.deviceDetails.id),
                text: response.deviceDetails.name
            }
            return this.applyEditable(response, options);
        },
        DeviceUuid: function (response) {
            var bodyDetailsContainer = OCA.SensorLogger.Sidebar.find('.tpl_bodyDetails').clone();
            bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

            var uuidLabel = $('<label/>', {
                'class':'unique-id'
            }).text('Device Unique Id');
            var uuidContentSpan = $('<span/>', {
                'class':'unique-id-content'
            }).text(response.deviceDetails.uuid);

            var uuid = bodyDetailsContainer
                .clone()
                .append(uuidLabel)
                .append(uuidContentSpan);
            return uuid;
        },
        deviceGroupSource: function(response) {
            let groupSource = [];
            for (group in response.groups) {
                groupSource.push({
                        value : response.groups[group].id,
                        text: response.groups[group].device_group_name,
                        id : response.groups[group].id
                    }
                );
            }
            return groupSource;
        },
        DeviceGroupSelect: function(response) {
            var updateUrl = OC.generateUrl(OCA.SensorLogger.url.updateDevice + response.deviceDetails.id);
            let bodyDetailsContainer = OCA.SensorLogger.Sidebar.find('.tpl_bodyDetails').clone();
            bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

            let groupSource = [];
            for (group in response.groups) {
                groupSource.push({
                        value : response.groups[group].id,
                        text: response.groups[group].device_group_name,
                        id : response.groups[group].id
                    }
                );
            }

            var groupSelect = $('<a/>',{
                'id':'group_id',
                'href': '#',
                'data-type': 'select2',
                'data-field': 'group',
                'data-pk': response.deviceDetails.id,
                'data-url': updateUrl,
                'data-title': response.deviceDetails.deviceGroupName,
                'text': response.deviceDetails.deviceGroupName
            }).editable({
                value: response.deviceDetails.group,
                source: groupSource,
                showbuttons: false,
                select2: {
                    multiple: false,
                    dropdownAutoWidth: true,
                    initSelection: function(element, callback) {
                        callback({ 'id': response.deviceDetails.group, 'text': response.deviceDetails.deviceGroupName })
                    },
                    createSearchChoice: function(term, data) {
                        if ($(data).filter(
                            function() {
                                return this.text.localeCompare(term)===0;
                            }).length===0) {
                            return {id:'create_'+term, text:'Create '+term, data:term};
                        }
                    }
                }
            });

            var groupLabel = $('<label/>', {
                'class':'group'
            }).text('Device Group');
            var groupContentSpan = $('<span/>', {
                'class':'group-content'
            }).append(groupSelect);

            var group = bodyDetailsContainer
                .clone()
                .append(groupLabel)
                .append(groupContentSpan);

            $(group).on('select2-selecting',function(e){
                var string = e.object.id,
                    substring = "create_";
                    if(string.includes(substring)) {
                        groupSource.push({
                            value: e.object.id,
                            text: e.object.data,
                            id : e.object.id
                        });
                    }
            });
            return group;
        },
        DeviceParentGroupSelect: function(response) {
            var updateUrl = OC.generateUrl(OCA.SensorLogger.url.updateDevice + response.deviceDetails.id);
            var bodyDetailsContainer = OCA.SensorLogger.Sidebar.find('.tpl_bodyDetails').clone();
            bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

            var groupSource = [];
            for (group in response.groups) {
                groupSource.push({
                        value : response.groups[group].id,
                        text: response.groups[group].device_group_name,
                        id : response.groups[group].id
                    }
                );
            }

            var groupParentSelect = $('<a/>',{
                'id':'group_parent_id',
                'href': '#',
                'data-type': 'select2',
                'data-field': 'group',
                'data-pk': response.deviceDetails.id,
                'data-url': updateUrl,
                'data-title': response.deviceGroupParentName
            }).editable({
                value: response.deviceDetails.groupParent,
                source: groupSource,
                showbuttons: false,
                select2: {
                    multiple: false,
                    data: groupSource,
                    dropdownAutoWidth: true,
                    initSelection: function(element, callback) {
                        callback({ 'id': response.deviceDetails.groupParent, 'text': response.deviceDetails.deviceGroupParentName })
                    },
                    createSearchChoice: function(term, data) {
                        if ($(data).filter(
                            function() {
                                return this.text.localeCompare(term)===0;
                            }).length===0) {
                            return {id:'create_'+term, text:'Create '+term, data:term};
                        }
                    }
                }
            });

            var parentGroupLabel = $('<label/>', {
                'class':'parent-group'
            }).text('Device Parent Group');
            var parentGroupContentSpan = $('<span/>', {
                'class':'parent-group-content'
            }).append(groupParentSelect);

            var groupParent = bodyDetailsContainer
                .clone()
                .append(parentGroupLabel)
                .append(parentGroupContentSpan);

            $(groupParent).on('select2-selecting',function(e){
                var string = e.object.id,
                    substring = "create_";
                    if(string.includes(substring)) {
                        groupSource.push({
                            value: e.object.id,
                            text: e.object.data,
                            id : e.object.id
                        });
                    }
            });

            return groupParent;
        },
        DeviceTypeSelect: function(response) {
            var updateUrl = OC.generateUrl(OCA.SensorLogger.url.updateDevice + response.deviceDetails.id);
            let bodyDetailsContainer = OCA.SensorLogger.Sidebar.find('.tpl_bodyDetails').clone();
            bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

            var typeSource = [];
            for (type in response.types) {
                typeSource.push({
                        value: response.types[type].id,
                        text: response.types[type].device_type_name,
                        id : response.types[type].id
                    }
                );

            }

            var typeSelect = $('<a/>',{
                'id':'type_id',
                'href': '#',
                'data-type': 'select2',
                'data-field': 'type',
                'data-pk': response.deviceDetails.id,
                'data-url': updateUrl,
                'data-title': response.deviceTypeName
            }).editable({
                value: response.deviceDetails.type,
                source: typeSource,
                showbuttons: false,
                select2: {
                    multiple: false,
                    data: typeSource,
                    dropdownAutoWidth: true,
                    initSelection: function(element, callback) {
                        callback({ 'id': response.deviceDetails.type, 'text': response.deviceDetails.deviceTypeName })
                    },
                    createSearchChoice: function(term, data) {
                        if ($(data).filter(
                            function() {
                                return this.text.localeCompare(term) === 0;
                            }).length === 0 ) {
                            return {
                                id:'create_'+term, text:'Create '+term, data:term
                            };
                        }
                    }
                }
            });

            var typeLabel = $('<label/>', {
                'class':'type'
            }).text('Device Type');
            var typeContentSpan = $('<span/>', {
                'class':'type-content'
            }).append(typeSelect);

            var type = bodyDetailsContainer
                .clone()
                .append(typeLabel)
                .append(typeContentSpan);

            $(type).on('select2-selecting',function(e){
                var string = e.object.id,
                    substring = "create_";
                    if(string.includes(substring)) {
                        typeSource.push({
                            value: e.object.id,
                            text: e.object.data,
                            id : e.object.id
                        });
                    }
            });
            return type;
        },
        DeviceDetails: function (response) {
            var sidebarBody = OCA.SensorLogger.Sidebar.find('.body');
            var sidebarTitle = OCA.SensorLogger.Sidebar.find('.title')

            sidebarTitle.empty().append(this.DeviceTitle(response));
            sidebarBody.append(this.DeviceUuid(response));
            sidebarBody.append(this.DeviceGroupSelect(response));
            sidebarBody.append(this.DeviceParentGroupSelect(response));
            sidebarBody.append(this.DeviceTypeSelect(response));
        },
        DeviceShare: function () {
		    /** TODO: Feature to share device needs to be implemented **/
            var sidebarBody = OCA.SensorLogger.Sidebar.find('.body');
            var sidebarTitle = OCA.SensorLogger.Sidebar.find('.title');
            var bodyDetailsContainer = OCA.SensorLogger.Sidebar.find('.tpl_bodyDetails').clone();
            bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

            var title = $('<h2/>', {
            }).text('Share Device');

            var notice = $('<h3/>', {
            }).text('not implemented yet');

            var icon = $('<div/>', {
                class: 'icon-info'
            }).css('float','left');

            var info = $('<p/>').text('For details about upcoming features or if you want to help improve SensorLogger, please visit ... ');
            var link = $('<a/>', {
                href: "https://github.com/alexstocker/sensorlogger/issues",
                target: '_blank',
                class: 'bold',
                }).text('SensorLogger Issues');

            link.appendTo(info.prepend(icon));
            sidebarTitle.empty().append(title);
            sidebarBody.append(bodyDetailsContainer.clone().append(notice));
            sidebarBody.append(bodyDetailsContainer.clone().append(info));
        },
        DeviceWipeout: function(options) {
            OCA.SensorLogger.sidebar.footerContainer.actions.wipeout.show();
            OCA.SensorLogger
                .sidebar.footerContainer.actions.wipeout
                .find('span').attr('data-original-title','Wipe out device!').tooltip({placement:'right'});

            var wipeOutUrl = OC.generateUrl('/apps/sensorlogger/wipeOutDevice');
            var wipeOutDeviceConfirm = $('<button/>',{
                "text": t('sensorlogger', 'Yes')
            });
            var wipeOutDeviceCancel = $('<button/>',{
                "text": t('sensorlogger', 'No')
            });

            OCA.SensorLogger.sidebar.footerContainer.actions.wipeout.on('click',function(e){
                $(".confirm-container").remove();
                var confirmContainer = $('<div/>',{
                    "class": "confirm-container",
                    "text": t('sensorlogger', 'Are you sure?')
                }).append(wipeOutDeviceConfirm).append(wipeOutDeviceCancel);
                OCA.SensorLogger.sidebar.footerContainer.actions.wipeout.hide().parent().append(confirmContainer);

                var wipeOutCall = function(id){
                    var deviceTr = $('table#sensorDevicesTable tr[data-id='+id+']');
                    var spinner = $('<div class="icon icon-loading">');
                    confirmContainer.parent().append(spinner);
                    confirmContainer.remove();
                    $.post( wipeOutUrl, {'device_id':id} )
                        .success(function (response) {
                            if(response.success) {
                                deviceTr.remove();
                                OCA.SensorLogger.Sidebar.hide();
                                OCA.SensorLogger.App.Sidebar.destroy();
                                spinner.remove();
                                $(".confirm-container").remove();
                                OC.Notification.showTemporary(t('sensorlogger', 'Wiped out your device completely!'));
                            } else {
                                OCA.SensorLogger.sidebar.footerContainer.actions.wipeout.show();
                                $(".confirm-container").remove();
                                spinner.remove();
                                OC.Notification.showTemporary(t('sensorlogger', 'Could not wipe out your device!'));
                            }
                        })
                        .error(function(response) {
                            OCA.SensorLogger.sidebar.footerContainer.actions.wipeout.show();
                            $(".confirm-container").remove();
                            spinner.remove();
                            OC.Notification.showTemporary(t('sensorlogger', 'Could not wipe out your device!'));
                        });
                };
                wipeOutDeviceCancel.on('click', function(e) {
                    confirmContainer.remove();
                    OCA.SensorLogger.sidebar.footerContainer.actions.wipeout.show();
                });
                wipeOutDeviceConfirm.on('click', function(e) {
                    wipeOutCall(options.id);
                });

            });
        },
		initialize: function() {
            this.sidebar = $('#app-sidebar');
            this.appContentWrapper = $('#app-content-wrapper');
            OCA.SensorLogger.Sidebar = this.sidebar;
            OCA.SensorLogger.Content = this.appContentWrapper;
			this.navigation = new OCA.SensorLogger.Navigation($('#app-navigation'));

            if ( this.navigation.activeType === 'showDashboard' ) {
                this.dashboardGrid = new OCA.SensorLogger.GridElements($('#widget-wrapper'));
            }

            if ( this.navigation.activeType === 'deviceList' ) {
                var tbody = this.appContentWrapper.find($('tbody#deviceList'));
                this.deviceList = new OCA.SensorLogger.DeviceList(tbody);
            }

            if ( this.navigation.activeType === 'showList' ) {
                var tbody = this.appContentWrapper.find($('tbody#logList'));
                this.logList = new OCA.SensorLogger.LogList(tbody);
            }

            if ( this.navigation.activeType === 'deviceTypeList' ) {
                var tbody = this.appContentWrapper.find($('table#sensorDeviceTypesTable tbody'));
                this.deviceTypeList= new OCA.SensorLogger.DeviceTypeList(tbody);
            }

            if ( this.navigation.activeType === 'deviceGroupList' ) {
                var tbody = this.appContentWrapper.find($('table#sensorDeviceGroupsTable tbody'));
                this.deviceGroupList= new OCA.SensorLogger.DeviceGroupList(tbody);
            }
		}
	}

})();

$(document).ready(function() {
	_.defer(function() {
        OCA.SensorLogger.App.initialize();
	});
});
