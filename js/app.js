(function () {

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

        _onPopState: function (params) {
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
        buttons: [],

        //saveBtn: $('#save-btn'),
        wipeoutBtn: $('#wipeout-btn'),
        showList: $('#showList'),
        showDashboard: $('#showDashboard'),
        deviceList: $('#deviceList'),
        deviceTypeList: $('#deviceTypeList'),
        deviceGroupList: $('#deviceGroupList'),
        dataTypeList: $('#dataTypeList'),
        appContentWrapper: $('#app-content-wrapper'),

        saveBtn : $('<a/>', {
            'id': 'save-btn',
            'class': 'icon-save',
            'style': 'display:none',
        }).html('<span class="icon icon-save" ></span>'),

        closeBtn : $('<a/>', {
            'id': 'close-btn',
            'class': 'icon-close handler close-all'
        }).html('<span class="icon icon-close" ></span>'),

        sidebarInfoBodyElement : $('<div/>', {
            'class': 'bodyElement'
        }),

        sidebarInfoBody : $('<div/>', {
            'class': 'sidebarInfoBody'
        }),

        sidebarContentWrapper : $('<div/>', {
            'class': 'content-wrapper'
        }),

        sidebarInfoContainer : $('<div/>', {
            'class': 'sidebarInfoContainer'
        }),

        sidebar : $('<div/>', {
        'id': 'app-sidebar'
        }),

        side : function() { $('div#app-sidebar') },

        sidebarInfoTitle : $('<div/>', {
            'class': 'sidebarInfoTitle title'
        }),

        sidebarFooter : $('<div/>', {
            'class': 'footer'
        }),

        Sidebar: function (elementId) {

            if(typeof elementId === 'undefined') {
                //var $sidebar = $('div#app-sidebar');
                //OCA.SensorLogger.sidebar = $sidebar;
            }


            //this.saveBtn = $saveBtn;

            /*
            var $wipeoutBtn = $('<a/>', {
                'id': 'wipeout-btn',
                'class': 'icon-wipeout',
                'style': 'display:none',
                'text': '<span class="icon icon-wipeout" ></span>'
            }).html('<span class="icon icon-wipeout" ></span>');
            */

            /*
            var $deleteBtn = $('<a/>', {
                'id': 'delete-btn',
                'class': 'icon-delete handler',
                'style': 'display:none',
                'text': '<span class="icon icon-delete" ></span>'
            });
            */

            /*
            var $closeBtn = $('<a/>', {
                'id': 'close-btn',
                'class': 'icon-close handler close-all'
            }).html('<span class="icon icon-close" ></span>');

            if (typeof elementId === 'undefined') {
                elementId = 'showDashboard';
            }
            */

            /*
            var $sidebarContentWrapper = $('<div/>', {
                'class': 'content-wrapper'
            });

            var $sidebarInfoContainer = $('<div/>', {
                'class': 'sidebarInfoContainer'
            });

            var $sidebar = $('#app-sidebar');

            var $sidebarInfoTitle = $('<div/>', {
                'class': 'sidebarInfoTitle title'
            });

            var $sidebarInfoBody = $('<div/>', {
                'class': 'sidebarInfoBody'
            });

            var $sidebarInfoBodyElement = $('<div/>', {
                'class': 'bodyElement'
            });

            var $sidebarInfoTags = $('<div/>', {
                'class': 'sidebarInfoTags'
            });

            var $tabHeaders = $('<ul/>', {
                'class': 'tabHeaders'
            });

            var $tabLiShareView = $('<li>', {
                'data-tabid': 'shareTabView'
            });

            var $tabLiActivityView = $('<li>', {
                'data-tabid': 'activityTabView'
            });

            var $tabLiNotificationsView = $('<li>', {
                'data-tabid': 'notificationsTabView'
            });

            var $tabsContainer = $('<div/>', {
                'class': 'tabsContainer'
            });

            var $tabShareView = $('<div/>', {
                'class': '',
                'text': 'ShareView Placeholder'
            });

            var $tabActivityView = $('<div/>', {
                'class': '',
                'text': 'ActivityView Placeholder'
            });

            var $tabNotificationView = $('<div/>', {
                'class': '',
                'text': 'ActivityView Placeholder'
            });

            var $footer = $('<div/>', {
                'class': 'footer'
            });

            */

            var url;
            var $appContentWrapper = $('#app-content-wrapper');

            var $sidebar = $('#app-sidebar');
            OCA.SensorLogger.sidebar = $sidebar;

            //if (elementId === 'showDashboard') {

                //$appContentWrapper.on('click', '.widgets.create', OCA.SensorLogger.WidgetSidebar('create'));
                //$appContentWrapper.on('click', '.widgets.edit', OCA.SensorLogger.WidgetSidebar('edit'));
                //$appContentWrapper.on('click', '.widgets.delete', OCA.SensorLogger.WidgetSidebar('delete'));
                $appContentWrapper.on('click', '.widgets.create', function (e) {

                    $('#app-sidebar .footer').remove();
                    $('.bodyElement').remove();

                    //$('div#sidebar').empty();
                    $('.sidebarInfoContainer').remove();
                    //$sidebarInfoTitle.empty();
                    //OCA.SensorLogger.sidebar.empty();


                    OCA.SensorLogger.WidgetSidebar('create');


                    //OCA.SensorLogger.sidebar.show();
                });
                $appContentWrapper.on('click', '.widgets.edit', function (e) {
                    OCA.SensorLogger.WidgetSidebar('edit');
                });
                $appContentWrapper.on('click', '.widgets.delete', function (e) {
                    OCA.SensorLogger.WidgetSidebar('delete');
                });
            //}

            if (elementId === 'deviceList') {

                $appContentWrapper.on('click', '.deviceEdit', function (e) {
                    $('#app-sidebar .footer').remove();
                    $('.bodyElement').remove();
                    $('div#sidebar .content-wrapper').remove();
                    $('.sidebarInfoTitle.title').remove();
                    $sidebarInfoTitle.empty();
                    $sidebarContentWrapper.remove();
                    $footer.remove();


                    var id = parseInt($(this).data('id'), 10);
                    var url = OC.generateUrl('/apps/sensorlogger/showDeviceDetails/' + id);

                    $.post(url).success(function (response) {

                        $.fn.editable.defaults.mode = 'inline';

                        //var sidebarBody = sidebar.find('.body');
                        //var sidebarTitle = sidebar.find('.title')
                        var updateUrl = OC.generateUrl('/apps/sensorlogger/updateDevice/' + id);
                        var createDeviceTypeUrl = OC.generateUrl('/apps/sensorlogger/createDeviceType');
                        var createDeviceGroupUrl = OC.generateUrl('/apps/sensorlogger/createDeviceGroup');

                        var title = $('<a/>', {
                            'title':t('sensorlogger', 'Device Name'),
                            'id': 'name',
                            'href': '#',
                            'data-type': 'text',
                            'data-field': 'name',
                            'data-pk': id,
                            'data-url': updateUrl,
                            'data-title': 'response.name',
                            'text': response.deviceDetails.name
                        }).editable();


                        var groupSource = [];
                        for (group in response.groups) {
                            groupSource.push({
                                    value: response.groups[group].id,
                                    text: response.groups[group].device_group_name,
                                    id: response.groups[group].id
                                }
                            );
                        }

                        var typeSource = [];
                        for (type in response.types) {
                            typeSource.push({
                                    value: response.types[type].id,
                                    text: response.types[type].device_type_name,
                                    id: response.types[type].id
                                }
                            );

                        }

                        var groupSelect = $sidebarInfoBodyElement.clone().append($('<a/>', {
                            'title': t('sensorlogger', 'Device Group'),
                            'id': 'group_id',
                            'href': '#',
                            'data-type': 'select2',
                            'data-field': 'group',
                            'data-pk': id,
                            'data-url': updateUrl,
                            'data-title': response.deviceGroupName
                        }).editable({
                            value: response.deviceDetails.group,
                            source: groupSource,
                            select2: {
                                multiple: false,
                                data: groupSource,
                                dropdownAutoWidth: true,
                                initSelection: function (element, callback) {
                                    callback({
                                        'id': response.deviceDetails.group,
                                        'text': response.deviceDetails.deviceGroupName
                                    })
                                },
                                createSearchChoice: function (term, data) {
                                    if ($(data).filter(
                                            function () {
                                                return this.text.localeCompare(term) === 0;
                                            }).length === 0) {
                                        return {id: 'create_' + term, text: term};
                                    }
                                }
                            }
                        }));

                        var groupParentSelect = $sidebarInfoBodyElement.clone().append($('<a/>', {
                            'title':t('sensorlogger', 'Device Parent Group'),
                            'id': 'group_parent_id',
                            'href': '#',
                            'data-type': 'select2',
                            'data-field': 'group',
                            'data-pk': id,
                            'data-url': updateUrl,
                            'data-title': response.deviceGroupParentName
                        }).editable({
                            value: response.deviceDetails.groupParent,
                            source: groupSource,
                            select2: {
                                multiple: false,
                                data: groupSource,
                                dropdownAutoWidth: true,
                                initSelection: function (element, callback) {
                                    callback({
                                        'id': response.deviceDetails.groupParent,
                                        'text': response.deviceDetails.deviceGroupParentName
                                    })
                                },
                                createSearchChoice: function (term, data) {
                                    if ($(data).filter(
                                            function () {
                                                return this.text.localeCompare(term) === 0;
                                            }).length === 0) {
                                        return {id: 'create_' + term, text: term};
                                    }
                                }
                            }
                        }));

                        var typeSelect = $sidebarInfoBodyElement.clone().append($('<a/>', {
                            'title':t('sensorlogger', 'Device Type'),
                            'id': 'type_id',
                            'href': '#',
                            'data-type': 'select2',
                            'data-field': 'type',
                            'data-pk': id,
                            'data-url': updateUrl,
                            'data-title': response.deviceTypeName
                        }).editable({
                            value: response.deviceDetails.type,
                            source: typeSource,
                            select2: {
                                multiple: false,
                                data: typeSource,
                                dropdownAutoWidth: true,
                                initSelection: function (element, callback) {
                                    callback({
                                        'id': response.deviceDetails.type,
                                        'text': response.deviceDetails.deviceTypeName
                                    })
                                },
                                createSearchChoice: function (term, data) {
                                    if ($(data).filter(
                                            function () {
                                                return this.text.localeCompare(term) === 0;
                                            }).length === 0) {
                                        return {id: 'create_' + term, text: term};
                                    }
                                }
                            }
                        }));

                        /*
                        var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
                        bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

                        sidebar.find('.bodyDetails').remove();

                        var uuid = bodyDetailsContainer.clone().append('UUID: ' + response.deviceDetails.uuid);
                        var group = bodyDetailsContainer.clone().append(groupSelect);
                        var groupParent = bodyDetailsContainer.clone().append(groupParentSelect);
                        var type = bodyDetailsContainer.clone().append(typeSelect);

                        sidebarTitle.empty().append(title);
                        sidebarBody.append(uuid);
                        sidebarBody.append(group);
                        sidebarBody.append(groupParent);
                        sidebarBody.append(type);

                        $(type).on('select2-selecting', function (e) {
                            var string = e.object.id,
                                substring = "create_";
                            if (string.includes(substring)) {
                                $.post(createDeviceTypeUrl, {'device_id': id, 'device_type_name': e.object.text})
                                    .success(function (response) {
                                        $(e.target).val(response.deviceTypeId);
                                        typeSource.push({
                                            value: response.deviceTypeId,
                                            text: e.object.text,
                                            id: response.deviceTypeId
                                        })
                                    });
                            }
                        });

                        $(groupParent).on('select2-selecting', function (e) {
                            var string = e.object.id,
                                substring = "create_";
                            if (string.includes(substring)) {
                                $.post(createDeviceGroupUrl, {'device_id': id, 'device_group_name': e.object.text})
                                    .success(function (response) {
                                        $(e.target).val(response.deviceGroupId);
                                        groupSource.push({
                                            value: response.deviceGroupId,
                                            text: e.object.text,
                                            id: response.deviceGroupId
                                        })
                                    });
                            }
                        });

                        $(group).on('select2-selecting', function (e) {
                            var string = e.object.id,
                                substring = "create_";
                            if (string.includes(substring)) {
                                $.post(createDeviceGroupUrl, {'device_id': id, 'device_group_name': e.object.text})
                                    .success(function (response) {
                                        $(e.target).val(response.deviceGroupId);
                                        groupSource.push({
                                            value: response.deviceGroupId,
                                            text: e.object.text,
                                            id: response.deviceGroupId
                                        })
                                    });
                            }
                        });

                        */
                        //$sidebarInfoBodyElement.clone().append($wipeoutBtn.html())
                        //$footer.append($wipeoutBtn);
                        $wipeoutBtn.appendTo($footer);
                        $closeBtn.appendTo($sidebar);
                        $closeBtn.on('click',function () {
                            $sidebar.hide();
                        });

                        //console.log($footer);

                        $(typeSelect).appendTo($sidebarInfoBody);
                        $(groupSelect).appendTo($sidebarInfoBody);
                        $(groupParentSelect).appendTo($sidebarInfoBody);

                        title.appendTo($sidebarInfoTitle);;
                        $sidebarInfoTitle.appendTo($sidebarInfoContainer);
                        $sidebarInfoBody.appendTo($sidebarInfoContainer);
                        $sidebarInfoContainer.appendTo($sidebarContentWrapper);
                        $footer.appendTo($sidebarContentWrapper);
                        $sidebarContentWrapper.appendTo($sidebar);


                        //$($sidebarInfoTitle.appendTo($sidebarContentWrapper)).appendTo($sidebar);
                        $wipeoutBtn.show();
                        $sidebar.show();
                    });



                });


                //OCA.SensorLogger.App.appContentWrapper.find('tr').on('click','.deviceEdit',function(e) {

                //
                //});

            }


        },
        DeviceList: function () {

        },
        Navigation: function (element) {
            $listElements = $(element).find('li');
            $.each($listElements, function (idx, listElement) {
                //OCA.SensorLogger.Sidebar($(this).attr('id'));
                //$(listElement).on('click', function () {
                    //OCA.SensorLogger.Sidebar($(this).attr('id'));
                //})
                //OCA.SensorLogger.Sidebar($(this).attr('id'));
            });
        },
        DeviceActions: function () {

        },
        Widgets: function (container) {
            var widgets = container.find('.dashboard-widget');
            $.map(widgets, function (widget, idx) {
                new OCA.SensorLogger.Widget($(widget));
            });
        },
        Widget: function (widgetContainer) {
            var startBtn = $('<button/>', {
                'text': 'Start'
            });

            var timeOutDd = $('<select/>', {
                'class': 'realtimechart-timeout',
            });
            timeOutDd
                .append('<option value="1">1 sec.</option>')
                .append('<option value="5" selected>5 sec.</option>')
                .append('<option value="10">10 sec.</option>')
                .append('<option value="30">30 sec.</option>')
                .append('<option value="60">60 sec.</option>');

            var dataTypeDd = $('<select/>', {
                'class': 'realtimechart-datatype'
            });

            var liveIndicator = $('<div/>', {
                "class": "pulse"
            });

            //console.log(widgetContainer.data('widget-type'));
            if (widgetContainer.data('widget-type') === 'chart') {

            } else if (widgetContainer.data('widget-type') === 'list') {

            } else if (widgetContainer.data('widget-type') === 'last') {

            } else if (widgetContainer.data('widget-type') === 'realTimeLast') {
                var realTimeContainer = widgetContainer.find('.realTimeLast');
                if (!$('#realTimeLast-' + widgetContainer.data('id'))
                        .hasOwnProperty('length')) {

                    var realTimeLastId = 'realTimeLast-' + widgetContainer.data('id');
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

                                                if (parseInt(dataTypeId) === parseInt(logDataTypeId)) {
                                                    var dataElem = dataElement
                                                        .clone()
                                                        .text(logData[logKey].value + ' ' + dataTypes[typeKey].short);

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
                                        var humidity = dataElement.clone().text(response[0].humidity + ' % r.F.');
                                        var temperature = dataElement.clone().text(response[0].temperature + ' °C');

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

                if (!$('#realTimePlotArea-' + widgetContainer.data('id'))
                        .hasOwnProperty('length')) {

                    var t = 5000;
                    var serie;

                    var plotAreaId = 'realTimePlotArea-' + widgetContainer.data('id');
                    var plotArea = $('<div/>', {
                        'id': plotAreaId
                    });

                    startBtn.click(function () {
                        t = timeOutDd[0].options[timeOutDd[0].selectedIndex].value * 1000;
                        serie = dataTypeDd[0].options[dataTypeDd[0].selectedIndex].value;
                        doUpdate(plotAreaId);
                        $(this).hide();
                        $('#realTimeChartTimeOut-' + widgetContainer.data('id')).hide();
                        $('#realTimeChartDataType-' + widgetContainer.data('id')).hide();
                    });

                    timeOutDd.attr('id', 'realTimeChartTimeOut-' + widgetContainer.data('id'));
                    dataTypeDd.attr('id', 'realTimeChartDataType-' + widgetContainer.data('id'));

                    chartContainer.append(plotArea);
                    chartContainer.append(timeOutDd);
                    chartContainer.append(startBtn);

                    var n = 20;
                    var x = (new Date()).getTime(); // current time

                    var data = [];
                    for (i = 0; i < n; i++) {
                        data.push([x - (n - 1 - i) * t, 0]);
                    }

                    var options = {
                        axes: {
                            xaxis: {
                                numberTicks: 4,
                                renderer: $.jqplot.DateAxisRenderer,
                                tickOptions: {formatString: '%H:%M:%S'},
                                min: data[0][0],
                                //max : data[19][0]
                                max: data[data.length - 1][0]
                            },
                            yaxis: {
                                min: -10, max: 40, numberTicks: 6,
                                tickOptions: {formatString: '%.1f'}
                            }
                        },
                        seriesDefaults: {
                            rendererOptions: {smooth: true}
                        },
                        highlighter: {
                            show: true,
                            sizeAdjust: 7.5
                        },
                        legend: {
                            renderer: $.jqplot.EnhancedLegendRenderer,
                            show: true,
                            showLabels: true,
                            location: 'n',
                            placement: 'inside',
                            fontSize: '11px',
                            fontFamily: ["Lucida Grande", "Lucida Sans Unicode", "Arial", "Verdana", "sans-serif"],
                            rendererOptions: {
                                seriesToggle: 'normal'
                            }
                        }
                    };

                    $.ajax({
                        url: 'lastLog/' + widgetContainer.data('id'),
                        data: {'limit': n},
                        type: 'GET',
                        async: 'false',
                        success: function (response) {
                            if (response) {
                                if (response.dataTypes) {
                                    for (var dataType in response.dataTypes) {
                                        dataTypeDd
                                            .append('<option value="' + response.dataTypes[dataType].id + '">' + response.dataTypes[dataType].description + '</option>');
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

                    var doUpdate = function () {

                        if (data.length > n - 1) {
                            data.shift();
                        }

                        $.ajax({
                            url: 'lastLog/' + widgetContainer.data('id'),
                            data: {'limit': 1},
                            type: 'GET',
                            success: function (response) {
                                if (response) {
                                    var time, x, y;
                                    if (response.dataTypes) {
                                        time = response.logs[0].createdAt.split(/[- :]/);
                                        x = new Date(time[0], time[1] - 1, time[2], time[3], time[4], time[5]).getTime();
                                        //y = response.logs[0].data[serie];

                                        var logData = response.logs[0].data;

                                        for (var key in logData) {
                                            if (logData[key].dataTypeId == serie) {
                                                y = logData[key].value;
                                            }
                                        }

                                        for (var dataType in response.dataTypes) {
                                            if (response.dataTypes[dataType].id == serie) {
                                                options.series = [{
                                                    label: response.dataTypes[dataType].description + ' [' + response.dataTypes[dataType].short + ']'
                                                }];
                                            }
                                        }

                                        if (y < options.axes.yaxis.min) {
                                            options.axes.yaxis.min = y;
                                        }
                                        if (y > options.axes.yaxis.max) {
                                            options.axes.yaxis.max = y;
                                        }

                                        data.push([x, y]);
                                        if (plot1) {
                                            plot1.destroy();
                                        }

                                    } else {
                                        time = response[0].createdAt.split(/[- :]/);
                                        x = new Date(time[0], time[1] - 1, time[2], time[3], time[4], time[5]).getTime();
                                        y = response[0][serie];

                                        if (y < options.axes.yaxis.min) {
                                            options.axes.yaxis.min = y;
                                        }
                                        if (y > options.axes.yaxis.max) {
                                            options.axes.yaxis.max = y;
                                        }

                                        options.series = [{
                                            label: serie
                                        }];

                                        data.push([x, y]);
                                        if (plot1) {
                                            plot1.destroy();
                                        }
                                    }

                                    plot1.series[0].data = data;
                                    options.axes.xaxis.min = data[0][0];

                                    if (data[0][0] > data[data.length - 1][0]) {
                                        options.axes.xaxis.min = x - (n - 1) * t;
                                    }

                                    options.axes.xaxis.max = data[data.length - 1][0];
                                    plot1 = $.jqplot(plotAreaId, [data], options);
                                    setTimeout(doUpdate, t);
                                }
                            }
                        });
                    }
                }
            } else if (widgetContainer.data('widget-type') === 'max_values_24h') {
                realTimeContainer = widgetContainer.find('.max_values_24h');
                if (!$('#max_values_24h-' + widgetContainer.data('id'))
                        .hasOwnProperty('length')) {

                    realTimeLastId = 'max_values_24h-' + widgetContainer.data('id');
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
                            url: 'maxLog/' + widgetContainer.data('id') + '/24',
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

                                                if (parseInt(dataTypeId) === parseInt(logDataTypeId)) {
                                                    var dataElem = dataElement
                                                        .clone()
                                                        .text(logData[logKey].value + ' ' + dataTypes[typeKey].short);

                                                    var dataTypeElem = dataTypeElement
                                                        .clone()
                                                        .text(dataTypes[typeKey].description);

                                                    dataElem.appendTo(realTimeLast);
                                                    dataTypeElem.appendTo(realTimeLast);
                                                }

                                            }

                                        }

                                    } else if (response.humidity && response.temperature) {
                                        //console.log(response);
                                        realTimeLast.empty();
                                        //var date = dataElement.clone().text(response[0].createdAt);
                                        var humidity = dataElement.clone().text(response.humidity + ' % r.F.');
                                        var temperature = dataElement.clone().text(response.temperature + ' °C');

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
        WidgetSidebar: function(action) {

            if ( action === 'create' ) {
                var saveWidget = OC.generateUrl('/apps/sensorlogger/saveWidget');
                OCA.SensorLogger.saveBtn.click(function() {
                    $('.editable').editable('submit', {
                        url: saveWidget,
                        ajaxOptions: {
                            dataType: 'json' //assuming json response
                        },
                        success: function(data, config) {
                            if(data && data.id) {

                                $(this).editable('option', 'pk', data.id);
                                //$($sidebar).hide();
                                //OC.Notification.showTemporary(t('sensorlogger', 'Dashboard widget saved'));
                                //console.log(config);
                                //console.log(data);
                                //console.log(OCA.SensorLogger.showDashboard)
                                //$('a.menuItem .active').trigger('click');
                                location.reload();

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
                        },
                        complete: function() {

                        }
                    });
                });

                var widgetDataUrl = OC.generateUrl('/apps/sensorlogger/widgetTypeList');
                $.get(widgetDataUrl).success(function (response) {
                    OCA.SensorLogger.saveBtn.show();
                    $.fn.editable.defaults.mode = 'inline';
                    //var sidebarBody = $sidebarInfoBody;
                    //var sidebarTitle = $sidebarInfoTitle;

                    var widgetTypeSource = [];
                    if(response.hasOwnProperty('widgetTypes')) {
                        for (var key in response.widgetTypes) {
                            widgetTypeSource.push({
                                    value : key,
                                    text: response.widgetTypes[key].displayName,
                                    id : key
                                }
                            );
                        }
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

                    var widgetTypeSelect = OCA.SensorLogger.sidebarInfoBodyElement.clone().append($('<a/>',{
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
                    }));

                    var deviceSelect = OCA.SensorLogger.sidebarInfoBodyElement.clone().append($('<a/>',{
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
                    }));

                    //var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
                    //bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

                    //sidebar.find('.bodyDetails').remove();

                    //var widgetType = bodyDetailsContainer.clone().append(widgetTypeSelect);
                    //var device = bodyDetailsContainer.clone().append(deviceSelect);

                    //$sidebarInfoBodyElement.append(widgetTypeSelect);
                    //$sidebarInfoBodyElement.append(deviceSelect);
                    //$sidebarInfoTitle.append()
                    //wipeOutBtn.hide();

                    //sidebarTitle.empty().append('Dashboard widget');



                    $(widgetTypeSelect).appendTo(OCA.SensorLogger.sidebarInfoBody);
                    //console.log(OCA.SensorLogger.sidebarInfoBody);
                    $(deviceSelect).appendTo(OCA.SensorLogger.sidebarInfoBody);

                    OCA.SensorLogger.saveBtn.appendTo(OCA.SensorLogger.sidebarFooter);
                    OCA.SensorLogger.closeBtn.appendTo(OCA.SensorLogger.sidebarFooter);
                    OCA.SensorLogger.closeBtn.on('click',function () {
                        //OCA.SensorLogger.sidebar.hide();
                    });

                    var title = $('<a/>',{
                        'id':'name',
                        'href': '#',
                        'data-type': 'text',
                        'data-field': 'name',
                        'data-pk': 'widget_name',
                        'data-value': '',
                        //'data-url': updateUrl,
                        'data-title': 'response.name',
                        'text': 'Add Dashboard Widget Title'
                    }).editable();


                    title.appendTo(OCA.SensorLogger.sidebarInfoTitle);

                    OCA.SensorLogger.sidebarInfoTitle.appendTo(OCA.SensorLogger.sidebarInfoContainer);
                    OCA.SensorLogger.sidebarInfoBody.appendTo(OCA.SensorLogger.sidebarInfoContainer);
                    OCA.SensorLogger.sidebarInfoContainer.appendTo(OCA.SensorLogger.sidebarContentWrapper);
                    OCA.SensorLogger.sidebarFooter.appendTo(OCA.SensorLogger.sidebarContentWrapper);
                    OCA.SensorLogger.sidebarContentWrapper.appendTo(OCA.SensorLogger.sidebar);

                    // OCA.SensorLogger.sidebar.append(title);
                    //$(title).appendTo(OCA.SensorLogger.sidebar);
                    //OCA.SensorLogger.sidebar.appendTo('body');


                })
            }

            //OCA.SensorLogger.sidebar.show();
        }
    };

    OCA.SensorLogger.App = {

        navigation: null,
        devices: null,
        widgets: null,
        //sidebar : $('#app-sidebar'),
        saveBtn: $('#save-btn'),
        showList: $('#showList'),
        showDashboard: $('#showDashboard'),
        deviceList: $('#deviceList'),
        deviceTypeList: $('#deviceTypeList'),
        deviceGroupList: $('#deviceGroupList'),
        dataTypeList: $('#dataTypeList'),
        appContentWrapper: $('#app-content-wrapper'),

        initialize: function () {
            this.navigation = OCA.SensorLogger.Navigation($('#app-navigation'));
            this.widgets = new OCA.SensorLogger.Widgets($('#widget-wrapper'));

            if ($('#deviceNotFound').val() === "1") {
                OC.Notification.showTemporary(t('sensorlogger', 'Device could not be found'));
            }

            var urlParams = OC.Util.History.parseUrlQuery();
            var deviceActions = new OCA.SensorLogger.DeviceActions();

            this.devices = OCA.SensorLogger.Devices;

            this.sidebar = OCA.SensorLogger.Sidebar();

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
        }

    }

})();

$(document).ready(function () {
    _.defer(function () {
        OCA.SensorLogger.App.initialize();
    });
});