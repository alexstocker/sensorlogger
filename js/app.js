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

    	widgets: [],

        DeviceList: function () {

        },
        Navigation: function (element) {

        },
        DeviceActions: function () {

        },
		Widgets: function(container) {
        	var widgets = container.find('.dashboard-widget');
        	$.map(widgets, function (widget,idx) {
                new OCA.SensorLogger.Widget($(widget));
			});
		},
		Widget: function (widgetContainer) {
    		if(widgetContainer.data('widget-type') === 'chart') {

			} else if (widgetContainer.data('widget-type') === 'list') {

			} else if (widgetContainer.data('widget-type') === 'last') {

            } else if (widgetContainer.data('widget-type') === 'realTimeLast') {

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
                    //console.log(widgetContainer.data('widget-type') );
                    var startBtn = $('<button/>', {
                        'text': 'Start'
                    });
                    startBtn.click(function(){
                        t = timeOutDd[0].options[timeOutDd[0].selectedIndex].value * 1000;
                        serie = dataTypeDd[0].options[dataTypeDd[0].selectedIndex].value;
                        doUpdate(plotAreaId);
                        $(this).hide();
                        $('#realTimeChartTimeOut-'+widgetContainer.data('id')).hide();
                        $('#realTimeChartDataType-'+widgetContainer.data('id')).hide();
                    });

                    var timeOutDd = $('<select/>', {
                        'id': 'realTimeChartTimeOut-'+widgetContainer.data('id'),
                        'class':'realtimechart-timeout',
                    });
                    timeOutDd
                        .append('<option value="1">1 sec.</option>')
                        .append('<option value="5" selected>5 sec.</option>')
                        .append('<option value="10">10 sec.</option>')
                        .append('<option value="30">30 sec.</option>')
                        .append('<option value="60">60 sec.</option>');

                    var dataTypeDd = $('<select/>', {
                        'id': 'realTimeChartDataType-'+widgetContainer.data('id'),
                        'class':'realtimechart-datatype'
                    });

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
                            //console.log(response);
                            if(response) {
                                if(response.dataTypes) {
                                    for(var dataType in response.dataTypes) {
                                        //console.log(response.dataTypes[dataType].id);
                                        dataTypeDd
                                            .append('<option value="'+response.dataTypes[dataType].id+'">'+response.dataTypes[dataType].description+'</option>');
                                    }
                                } else {
                                    dataTypeDd
                                        .append('<option value="temperature">Temperature</option>')
                                        .append('<option value="humidity">Humidity</option>');
                                }
                                dataTypeDd.insertAfter(plotArea);
                                /*
                                for (var log in response) {
                                    console.log(response[log]);

                                    var time = response[log].createdAt.split(/[- :]/);
                                    var x = new Date(time[0], time[1]-1, time[2], time[3], time[4], time[5]).getTime();

                                    var y = response[log].temperature;

                                    if(y < options.axes.yaxis.min) {
                                        options.axes.yaxis.min = y;
                                    }
                                    if(y > options.axes.yaxis.max) {
                                        options.axes.yaxis.max = y;
                                    }

                                    data.push([x,y])
                                }
                                */
                                //if (plot1) {
                                //    plot1.destroy();
                                //}
                                //plot1.series[0].data = data;
                                //options.axes.xaxis.min = data[0][0];
                                //options.axes.xaxis.max = data[data.length-1][0];
                                //plot1 = $.jqplot (plotAreaId, [data],options);
                                //setTimeout(doUpdate, t);

                                /*
                                var time = response[0].createdAt.split(/[- :]/);
                                var x = new Date(time[0], time[1]-1, time[2], time[3], time[4], time[5]).getTime();

                                var y = response[0].temperature;

                                if(y < options.axes.yaxis.min) {
                                    options.axes.yaxis.min = y;
                                }
                                if(y > options.axes.yaxis.max) {
                                    options.axes.yaxis.max = y;
                                }
                                */

                                //var x = (new Date()).getTime();
                                //console.log(date);

                                /*
                                data.push([x,y]);
                                if (plot1) {
                                    plot1.destroy();
                                }
                                plot1.series[0].data = data;
                                options.axes.xaxis.min = data[0][0];
                                options.axes.xaxis.max = data[data.length-1][0];
                                plot1 = $.jqplot (plotAreaId, [data],options);
                                setTimeout(doUpdate, t);
                                */
                            }
                        }
                    });




                    var plot1 = $.jqplot(plotAreaId, [data], options);
                    plot1.series[0].data = data;

                    var doUpdate = function() {

                        /*
                        var selected = timeOutDd.children('option').filter(function() {
                            return $(this).val(); //To select Blue
                        }).prop('selected', true);
                        */

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

                                        //console.log(x+' '+y);

                                        //x = response.createdAt;
                                        //y = response.temperature;
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
                                    options.axes.xaxis.max = data[data.length-1][0];
                                    plot1 = $.jqplot (plotAreaId, [data],options);
                                    setTimeout(doUpdate, t);
                                }
                            }
                        });
                    }
                }



            } else {
                console.log('EHLO');
			}
			//return widgetContainer;
        }
	};

	OCA.SensorLogger.App = {

		navigation: null,
		devices: null,
		widgets: null,
        sidebar : $('#app-sidebar'),
        saveBtn : $('#save-btn'),
        showList : $('#showList'),
        showDashboard : $('#showDashboard'),
        deviceList : $('#deviceList'),
        deviceTypeList : $('#deviceTypeList'),
        deviceGroupList : $('#deviceGroupList'),
        dataTypeList : $('#dataTypeList'),
        appContentWrapper : $('#app-content-wrapper'),

		initialize: function() {
			this.navigation = new OCA.SensorLogger.Navigation($('#app-navigation'));
			this.widgets = new OCA.SensorLogger.Widgets($('#widget-wrapper'));

			if ($('#deviceNotFound').val() === "1") {
				OC.Notification.showTemporary(t('sensorlogger', 'Device could not be found'));
			}

			var urlParams = OC.Util.History.parseUrlQuery();
			var deviceActions = new OCA.SensorLogger.DeviceActions();
			//deviceActions.registerDefaultActions();
			//deviceActions.merge(window.DeviceActions);
			//deviceActions.merge(OCA.SensorLogger.deviceActions);

			this.devices = OCA.SensorLogger.Devices;

			this.deviceList = new OCA.SensorLogger.DeviceList(
				$('#app-content-devices'), {
					scrollContainer: $('#app-content-wrapper'),
					//dragOptions: dragOptions,
					//folderDropOptions: folderDropOptions,
					deviceActions: deviceActions,
					allowLegacyActions: true,
					scrollTo: urlParams.scrollto,
					//devicesClient: OC.SensorLogger.getClient(),
					sorting: {
						mode: $('#defaultDeviceSorting').val(),
						direction: $('#defaultDeviceSortingDirection').val()
					}
				}
			);
			//this.devices.initialize();
		}

	}

})();

$(document).ready(function() {
	_.defer(function() {
		OCA.SensorLogger.App.initialize();
	});
});