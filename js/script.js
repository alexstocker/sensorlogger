/**
 * ownCloud - sensorlogger
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author ELExG <elexgspot@gmail.com>
 * @copyright ELExG 2017
 */

(function ($, OC) {

	$(function () {
		var sidebar = $('#app-sidebar');
		var saveBtn = $('#save-btn');
		var wipeOutBtn = $('#wipeout-btn');
		var showList = $('#showList');
		var showDashboard = $('#showDashboard');
		var deviceList = $('#deviceList');
		var deviceTypeList = $('#deviceTypeList');
		var deviceGroupList = $('#deviceGroupList');
		var dataTypeList = $('#dataTypeList');
		var appContentWrapper = $('#app-content-wrapper');
		var chartBtns = $('.deviceChart');
		var deviceDetailsRequest = null;

		var setSidebarVisible = function(visible) {
			sidebar.attr('aria-hidden', visible ? 'false' : 'true');
			sidebar.toggle(visible);
		};

		var setNativeTooltip = function(element) {
			var $element = $(element);
			var title = $element.attr('data-original-title') ||
				$element.find('[data-original-title]').attr('data-original-title');
			if (title) {
				$element.attr('title', title);
			}
		};

		var _onClickAction = function(event) {
			var $target = $(event.target);
			if (!$target.hasClass('menuItem')) {
				$target = $target.closest('.menuItem');
			}
			event.preventDefault();
			$target.closest('ul').find('li.active, .menuItem.active').removeClass('active');
			$target.addClass('active');
			$target.closest('li').addClass('active');
			setSidebarVisible(false);
			saveBtn.hide();
		};

		showList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/showList');
			$.get(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});
		});

		showDashboard.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/showDashboard');
			$.get(url).success(function (response) {
				appContentWrapper.empty().append(response);
				dashboardWidgets(e);
			});
		});

		deviceList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceList');
			$.get(url).success(function (response) {
				var $response = $(response).clone();
				$.each($response
					.find('a.device-datalist, ' +
						'.deviceactions > a.action-share, ' +
						'.deviceactions > a.action-menu'),function(idx,element){
					setNativeTooltip(element);
				});
				appContentWrapper.empty().append($response);
			});
		});

		deviceTypeList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceTypeList');
			$.get(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});

		deviceGroupList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceGroupList');
			$.get(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});

		dataTypeList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/dataTypeList');
			$.get(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});

		appContentWrapper.on('click','.deviceChart',function (e) {
			setSidebarVisible(false);
			saveBtn.hide();
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/deviceChart/'+id);
			$.get(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
				var dataUrl = OC.generateUrl('/apps/sensorlogger/chartData/' + id);
				loadChart($('div#chart'),$('div#chart').data('id'),dataUrl);

			});
		});

		appContentWrapper.on('click','.deviceListData',function (e) {
			setSidebarVisible(false);
			saveBtn.hide();
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+id);
			$.get(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});
		});

		appContentWrapper.on('click','a.widget-delete',function (e) {
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

		appContentWrapper.on('click','a.datatype-delete',function (e) {
			var id = $(e.target).data('id');
			var container = $(e.target).closest('tr');
			var url = OC.generateUrl('/apps/sensorlogger/deleteDataType/'+id);
			$.post(url).success(function (response) {
				if(response.success) {
					container.remove();
					OC.Notification.showTemporary(t('sensorlogger', 'Data Type deleted'));
				} else {
					OC.Notification.showTemporary(t('sensorlogger', 'Data Type NOT deleted'));
				}
			});
		});

		appContentWrapper.on('click','a.devicegroup-delete',function (e) {
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

		appContentWrapper.on('click','a.devicetype-delete',function (e) {
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

		appContentWrapper.on('click','a.device-delete',function (e) {
			var id = $(e.target).data('id');
			var container = $(e.target).closest('tr');
			var url = OC.generateUrl('/apps/sensorlogger/deleteDevice/'+id);
			$.post(url).success(function (response) {
				if(response.success) {
					container.remove();
					OC.Notification.showTemporary(t('sensorlogger', 'Device deleted'));
				} else {
                    OC.Notification.showTemporary(t('sensorlogger', 'Device NOT deleted'));
				}
			});
		});

		appContentWrapper.on('click','a.log-delete',function (e) {
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


		sidebar.on('click','.icon-close',function(e) {
			setSidebarVisible(false);
			sidebar.removeAttr('data-device-id');
			if (deviceDetailsRequest) {
				deviceDetailsRequest.abort();
				deviceDetailsRequest = null;
			}
			saveBtn.hide();
			wipeOutBtn.hide();
		});

		$(document.body).on('click','.actions',function(e) {
			sidebarWidgets();
			setSidebarVisible(true);
		});

		var sidebarWidgets = function (e) {
			var saveWidget = OC.generateUrl('/apps/sensorlogger/saveWidget');

			saveBtn.click(function() {
				$('.editable').editable('submit', {
					url: saveWidget,
					ajaxOptions: {
						dataType: 'json' //assuming json response
					},
					success: function(data, config) {
						if(data && data.id) {
							$(this).editable('option', 'pk', data.id);
							$(sidebar).hide();
							OC.Notification.showTemporary(t('sensorlogger', 'Dashboard widget saved'));
							showDashboard.trigger('click');

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
				saveBtn.show();
				$.fn.editable.defaults.mode = 'inline';
				var sidebarBody = sidebar.find('.body');
				var sidebarTitle = sidebar.find('.title');

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

				var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
				bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

				sidebar.find('.bodyDetails').remove();

				var widgetType = bodyDetailsContainer.clone()
					.append(widgetTypeLabel)
					.append(widgetTypeContentSpan);
				var device = bodyDetailsContainer.clone()
					.append(deviceLabel)
					.append(deviceContentSpan);

				sidebarBody.append(widgetType);
				sidebarBody.append(device);
                wipeOutBtn.hide();

				sidebarTitle.empty().append('Dashboard Widget');

			})
		};

		var dashboardWidgets = function (e) {
			$('a.delete').click(
				function(){
					var sel = '';
					if(sel) {
					}
				}
			);

			$('.column').sortable({
				connectWith: '.column',
				handle: 'h2',
				cursor: 'move',
				placeholder: 'placeholder',
				forcePlaceholderSize: true,
				opacity: 0.4,
				stop: function(event, ui)
				{
					$(ui.item).find('h2').click();
					var sortorder='';

					$('.column').each(function(){
						var itemorder=$(this).sortable('toArray');
						var columnId=$(this).attr('id');
						sortorder+=columnId+'='+itemorder.toString()+'&';
					});
					sortorder = sortorder.substring(0, sortorder.length - 1)
				}
			}).disableSelection();

			$(".column").each(function(){
				var type = $(this).children().data('widget-type');

				if(type === "chart") {
					var id = $(this).children().data('id');
					var dataUrl = OC.generateUrl('/apps/sensorlogger/chartData/' + id);
					var chartContainer = $(this).children().find('.chartcontainer');
					var data = {"limit":1280};
					plotArea = chartContainer;
					loadChart(chartContainer,id,dataUrl,data);
				}
				if(type === "realTimeChart") {
                    new OCA.SensorLogger.Widgets($('#widget-wrapper'));
				}
                if(type === "realTimeLast") {
                    new OCA.SensorLogger.Widgets($('#widget-wrapper'));
                }
			});
		};

		appContentWrapper.on('click', '.deviceEdit', function(e) {
			if ($(e.target).closest('a, button').length) {
				return;
			}

			var id = $(this).closest('tr').data('id');
			if (sidebar.is(':visible') && String(sidebar.data('device-id')) === String(id)) {
				setSidebarVisible(false);
				sidebar.removeAttr('data-device-id');
				if (deviceDetailsRequest) {
					deviceDetailsRequest.abort();
					deviceDetailsRequest = null;
				}
				saveBtn.hide();
				wipeOutBtn.hide();
				return;
			}

			openDeviceSidebar(id);
		});

		appContentWrapper.on('click', '.deviceactions > a.action-share, .deviceactions > a.action-menu', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).closest('tr').trigger('click');
		});

		var openDeviceSidebar = function(id) {
			$(".confirm-container").remove();

			sidebar.attr('data-device-id', id);
			sidebar.find('.bodyDetails').remove();
			sidebar.find('.title').text(t('sensorlogger', 'Loading device details'));
			sidebar.find('.body').attr('aria-busy', 'true');
			setSidebarVisible(true);

            var wipeOutUrl = OC.generateUrl('/apps/sensorlogger/wipeOutDevice');

            var wipeOutDevice = $('#wipeout-btn').show();
			wipeOutDevice.attr('title', t('sensorlogger', 'Wipe out device'));

            var wipeOutDeviceConfirm = $('<button/>',{
                "text": t('sensorlogger', 'Yes')
            });

            var wipeOutDeviceCancel = $('<button/>',{
                "text": t('sensorlogger', 'No')
            });

            wipeOutDevice.off('click.sensorlogger').on('click.sensorlogger',function(e){
				e.preventDefault();
                $(".confirm-container").remove();
            	var confirmContainer = $('<div/>',{
            		"class": "confirm-container",
					"text": t('sensorlogger', 'Are you sure?')
				}).append(wipeOutDeviceConfirm).append(wipeOutDeviceCancel);
                wipeOutDevice.hide().parent().append(confirmContainer);

                var wipeOutCall = function(id){
                	var deviceTr = $('table#sensorDevicesTable tr[data-id='+id+']');
                    var spinner = $('<div class="icon icon-loading">');
                    confirmContainer.parent().append(spinner);
                    //console.log(confirmContainer);
                    confirmContainer.remove();
                    $.post( wipeOutUrl, {'device_id':id} )
                        .success(function (response) {
                        	if(response.success) {
                                deviceTr.remove();
                                setSidebarVisible(false);
                                spinner.remove();
                                $(".confirm-container").remove();
                                OC.Notification.showTemporary(t('sensorlogger', 'Wiped out your device completely!'));
							} else {
                                wipeOutDevice.show();
                                $(".confirm-container").remove();
                                spinner.remove();
                                OC.Notification.showTemporary(t('sensorlogger', 'Could not wipe out your device!'));
							}
                        })
						.error(function(response) {
                            wipeOutDevice.show();
                            $(".confirm-container").remove();
                            spinner.remove();
                            OC.Notification.showTemporary(t('sensorlogger', 'Could not wipe out your device!'));
						});
                };


                wipeOutDeviceCancel.on('click', function(e) {
                    confirmContainer.remove();
                    wipeOutDevice.show();
				});
                wipeOutDeviceConfirm.on('click', function(e) {
                    wipeOutCall(id);
				});

			});

			var url = OC.generateUrl('/apps/sensorlogger/showDeviceDetails/'+id);
			if (deviceDetailsRequest) {
				deviceDetailsRequest.abort();
			}
			deviceDetailsRequest = $.get(url).done(function (response) {
				if (String(sidebar.data('device-id')) !== String(id)) {
					return;
				}

				var sidebarBody = sidebar.find('.body');
				var sidebarTitle = sidebar.find('.title');
				var updateUrl = OC.generateUrl('/apps/sensorlogger/updateDevice/'+id);
				var createDeviceTypeUrl = OC.generateUrl('/apps/sensorlogger/createDeviceType');
				var createDeviceGroupUrl = OC.generateUrl('/apps/sensorlogger/createDeviceGroup');
				var device = response.deviceDetails;
				if (!device) {
					sidebarTitle.text(t('sensorlogger', 'Could not load device details'));
					sidebarBody.attr('aria-busy', 'false');
					return;
				}

				var groupSource = [];
				for (var group in (response.groups || [])) {
					groupSource.push({
						value: response.groups[group].id,
						text: response.groups[group].device_group_name
					});
				}

				var typeSource = [];
				for (var type in (response.types || [])) {
					typeSource.push({
						value: response.types[type].id,
						text: response.types[type].device_type_name
					});
				}

				var updateDeviceField = function(field, value, onSuccess, onFailure) {
					$.post(updateUrl, {name: field, value: value})
						.done(function(result) {
							if (!result || !result.success) {
								OC.Notification.showTemporary(t('sensorlogger', 'Could not update device'));
								if (onFailure) {
									onFailure();
								}
								return;
							}
							if (onSuccess) {
								onSuccess(result);
							}
						})
						.fail(function() {
							OC.Notification.showTemporary(t('sensorlogger', 'Could not update device'));
							if (onFailure) {
								onFailure();
							}
						});
				};

				var addDetail = function(labelText, fieldName, value, options) {
					var row = $('<div/>', {'class': 'bodyDetails'});
					var label = $('<label/>', {text: labelText});
					var control;
					if (options) {
						control = $('<select/>', {'aria-label': labelText});
						control.append($('<option/>', {
							value: '0',
							text: t('sensorlogger', 'None')
						}));
						$.each(options, function(index, option) {
							control.append($('<option/>', {
								value: option.value,
								text: option.text
							}));
						});
						control.val(value === null || value === undefined ? '0' : String(value));
						var savedValue = control.val();
						control.on('change', function() {
							var selectedValue = $(this).val();
							updateDeviceField(fieldName, selectedValue, function() {
								savedValue = selectedValue;
							}, function() {
								control.val(savedValue);
							});
						});
					} else {
						control = $('<input/>', {
							type: 'text',
							'aria-label': labelText,
							value: value || ''
						});
						var save = $('<button/>', {
							type: 'button',
							text: t('sensorlogger', 'Save')
						}).on('click', function() {
							var newName = control.val().trim();
							if (!newName) {
								control.trigger('focus');
								return;
							}
							updateDeviceField(fieldName, newName, function() {
								sidebarTitle.text(newName);
								$('table#sensorDevicesTable tr[data-id="' + id + '"] td').eq(3).text(newName);
							});
						});
						row.append(label, control, save);
					}
					if (options) {
						row.append(label, control);
					}
					sidebarBody.append(row);
					return control;
				};

				sidebarTitle.text(device.name);
				sidebarBody.empty();
				addDetail(t('sensorlogger', 'Device name'), 'name', device.name);
				var uuidRow = $('<div/>', {'class': 'bodyDetails'});
				uuidRow.append(
					$('<label/>', {text: t('sensorlogger', 'Device Unique Id')}),
					$('<span/>', {'class': 'unique-id', text: device.uuid || ''})
				);
				sidebarBody.append(uuidRow);
				var groupSelect = addDetail(t('sensorlogger', 'Device Group'), 'group_id', device.group, groupSource);
				var groupParentSelect = addDetail(t('sensorlogger', 'Device Parent Group'), 'group_parent_id', device.groupParent, groupSource);
				var typeSelect = addDetail(t('sensorlogger', 'Device Type'), 'type_id', device.type, typeSource);
				sidebarBody.attr('aria-busy', 'false');

				var createControl = function(label, url, parameterName, select, relatedSelects, fieldName) {
					var input = $('<input/>', {
						type: 'text',
						'class': 'device-option-create',
						'aria-label': label
					});
					var button = $('<button/>', {
						type: 'button',
						text: t('sensorlogger', 'Create')
					});
					button.on('click', function() {
						var value = input.val();
						if (!value || !value.trim()) {
							input.trigger('focus');
							return;
						}

						var data = {'device_id': id};
						data[parameterName] = value.trim();
						$.post(url, data).done(function(result) {
							var optionId = result.deviceTypeId || result.deviceGroupId;
							if (!optionId) {
								OC.Notification.showTemporary(t('sensorlogger', 'Could not create option'));
								return;
							}
							$.each(relatedSelects, function(index, relatedSelect) {
								var exists = relatedSelect.find('option').filter(function() {
									return String(this.value) === String(optionId);
								}).length > 0;
								if (!exists) {
									relatedSelect.append($('<option/>', {
										value: optionId,
										text: value.trim()
									}));
								}
							});
							select.val(String(optionId)).trigger('change');
							input.val('');
						}).fail(function() {
							OC.Notification.showTemporary(t('sensorlogger', 'Could not create option'));
						});
					});
					$('<div/>', {'class': 'device-option-create-control'})
						.append(input, button)
						.appendTo(sidebarBody);
				};

				createControl(t('sensorlogger', 'Create device type'), createDeviceTypeUrl, 'device_type_name', typeSelect, [typeSelect], 'type_id');
				createControl(t('sensorlogger', 'Create device group'), createDeviceGroupUrl, 'device_group_name', groupSelect, [groupSelect, groupParentSelect], 'group_id');
				createControl(t('sensorlogger', 'Create parent device group'), createDeviceGroupUrl, 'device_group_name', groupParentSelect, [groupSelect, groupParentSelect], 'group_parent_id');
			}).fail(function (request, status) {
				if (status === 'abort') {
					return;
				}
				sidebar.find('.body').attr('aria-busy', 'false');
				sidebar.find('.title').text(t('sensorlogger', 'Could not load device details'));
				OC.Notification.showTemporary(t('sensorlogger', 'Could not load device details'));
			});
		};
		dashboardWidgets();
		var standaloneChart = $('#chart');
		if (standaloneChart.length) {
			var deviceId = standaloneChart.data('id');
			var dataUrl = OC.generateUrl('/apps/sensorlogger/chartData/' + deviceId);
			loadChart(standaloneChart, deviceId, dataUrl);
		}
	});
	var drawableLines;
	var plotArea;

	var loadChart = function(plotArea,id,url,data) {
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
				plotRealTimeChart(plotArea,drawableLines,serieslabel);

				$("a#toggle_realtime").on('click',function(){
					plotArea = $(this).attr('data-plotarea');
					doUpdate();
				});
			} catch (err) {
				$(plotArea).html(err);
			}
		});
	};

	function getRandomArbitrary(min, max) {
		return Math.random() * (max - min) + min;
	}

	function getRandomInt(min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	var data = drawableLines;

	var plotChart = function(plotArea,drawableLines) {
		$.jqplot($(plotArea).attr('id'),drawableLines,{
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
			highlighter: {
				show: true,
				sizeAdjust: 7.5
			},
			series: [
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
			],
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
	};

	var plotRealTimeChart = function(plotArea,drawableLines,labels) {
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
	};

    /**
	 * @deprecated
     */
	function doUpdate() {
		var t = 10000;
		var n = 20;

		if(drawableLines[0].length > n-1){
			drawableLines[0].shift();
		}

		var y = getRandomArbitrary(10,39);
		var y1 = getRandomInt(1,100);
		var x = new Date();

		drawableLines[0].push([x.toLocaleTimeString(),y]);
		drawableLines[1].push([x.toLocaleTimeString(),y1]);
		if (plotRealTimeChart) {
			$(plotArea).empty();
		}
		plotRealTimeChart(plotArea,drawableLines);
		setTimeout(doUpdate, t);
	}

})(jQuery, OC);