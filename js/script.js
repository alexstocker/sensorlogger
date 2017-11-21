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
		var showList = $('#showList');
		var showDashboard = $('#showDashboard');
		var deviceList = $('#deviceList');
		var deviceTypeList = $('#deviceTypeList');
		var deviceGroupList = $('#deviceGroupList');
		var dataTypeList = $('#dataTypeList');
		var appContentWrapper = $('#app-content-wrapper');
		var notification = $("#notification");

		var _onClickAction = function(event) {
			var $target = $(event.target);
			if (!$target.hasClass('menuItem')) {
				$target = $target.closest('.menuItem');
			}
			event.preventDefault();
			$target.closest('ul').find('.menuItem.active').removeClass('active');
			$target.addClass('active');
			sidebar.hide();
			saveBtn.hide();
		};

		showList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/showList');
			$.post(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});
		});

		showDashboard.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/showDashboard');
			$.post(url).success(function (response) {
				appContentWrapper.empty().append(response);
				dashboardWidgets(e);
			});
		});

		deviceList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceList');
			$.post(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});

		deviceTypeList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceTypeList');
			$.post(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});

		deviceGroupList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceGroupList');
			$.post(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});

		dataTypeList.click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/dataTypeList');
			$.post(url).success(function (response) {
				appContentWrapper.empty().append(response);
			});

		});


		appContentWrapper.on('click','.deviceChart',function (e) {
			sidebar.hide();
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
			sidebar.hide();
			saveBtn.hide();
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+id);
			$.post(url).success(function (response) {
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

		appContentWrapper.on('click','a.device-delete',function (e) {
			var id = $(e.target).data('id');
			var container = $(e.target).closest('tr');
			var url = OC.generateUrl('/apps/sensorlogger/deleteDevice/'+id);
			$.post(url).success(function (response) {
				if(response.success) {
					container.remove();
					OC.Notification.showTemporary(t('sensorlogger', 'Device deleted'));
				}
			});
		});


		sidebar.on('click','.icon-close',function(e) {
			sidebar.hide();
			saveBtn.hide();
		});

		$(document.body).on('click','.actions',function(e) {
			sidebarWidgets();
			sidebar.show();
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
							text: response.widgetTypes[key],
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

				var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
				bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

				sidebar.find('.bodyDetails').remove();

				var widgetType = bodyDetailsContainer.clone().append(widgetTypeSelect);
				var device = bodyDetailsContainer.clone().append(deviceSelect);

				sidebarBody.append(widgetType);
				sidebarBody.append(device);

				sidebarTitle.empty().append('Dashboard widget');

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
			//console.log($(".column"));

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
			});
		};

		appContentWrapper.on('click','.deviceEdit',function(e) {
			var target = $(e.target);
			if(e.target.nodeName != 'TD') {
				return;
			}

			if(sidebar.is(':visible')) {
				sidebar.hide();
				saveBtn.hide();
				return;
			}

			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/showDeviceDetails/'+id);
			$.post(url).success(function (response) {
				$.fn.editable.defaults.mode = 'inline';

				var sidebarBody = sidebar.find('.body');
				var sidebarTitle = sidebar.find('.title')

				var updateUrl = OC.generateUrl('/apps/sensorlogger/updateDevice/'+id);
				var createDeviceTypeUrl = OC.generateUrl('/apps/sensorlogger/createDeviceType');
				var createDeviceGroupUrl = OC.generateUrl('/apps/sensorlogger/createDeviceGroup');

				var title = $('<a/>',{
					'id':'name',
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
						value : response.groups[group].id,
						text: response.groups[group].device_group_name,
						id : response.groups[group].id
					}
					);
				}

				var typeSource = [];
				for (type in response.types) {
					typeSource.push({
						value: response.types[type].id,
						text: response.types[type].device_type_name,
						id : response.types[type].id
					}
					);

				}

				var groupSelect = $('<a/>',{
					'id':'group_id',
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
						initSelection: function(element, callback) {
							callback({ 'id': response.deviceDetails.group, 'text': response.deviceDetails.deviceGroupName })
						},
						createSearchChoice: function(term, data) {
							if ($(data).filter(
									function() {
										return this.text.localeCompare(term)===0;
									}).length===0) {
								return {id:'create_'+term, text:term};
							}
						}
					}
				});

				var groupParentSelect = $('<a/>',{
					'id':'group_parent_id',
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
						initSelection: function(element, callback) {
							callback({ 'id': response.deviceDetails.groupParent, 'text': response.deviceDetails.deviceGroupParentName })
						},
						createSearchChoice: function(term, data) {
							if ($(data).filter(
									function() {
										return this.text.localeCompare(term)===0;
									}).length===0) {
								return {id:'create_'+term, text:term};
							}
						}
					}
				});

				var typeSelect = $('<a/>',{
					'id':'type_id',
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
						initSelection: function(element, callback) {
							callback({ 'id': response.deviceDetails.type, 'text': response.deviceDetails.deviceTypeName })
						},
						createSearchChoice: function(term, data) {
							if ($(data).filter(
									function() {
										return this.text.localeCompare(term)===0;
									}).length===0) {
								return {id:'create_'+term, text:term};
							}
						}
					}
				});

				var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
				bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');

				sidebar.find('.bodyDetails').remove();

				var uuid = bodyDetailsContainer.clone().append('UUID: '+response.deviceDetails.uuid);
				var group = bodyDetailsContainer.clone().append(groupSelect);
				var groupParent = bodyDetailsContainer.clone().append(groupParentSelect);
				var type = bodyDetailsContainer.clone().append(typeSelect);

				sidebarTitle.empty().append(title);
				sidebarBody.append(uuid);
				sidebarBody.append(group);
				sidebarBody.append(groupParent);
				sidebarBody.append(type);

				$(type).on('select2-selecting',function(e){
					var string = e.object.id,
						substring = "create_";
					if(string.includes(substring)) {
						$.post(createDeviceTypeUrl, {'device_id':id,'device_type_name':e.object.text} )
							.success(function (response) {
								$(e.target).val(response.deviceTypeId);
								typeSource.push({
									value: response.deviceTypeId,
									text: e.object.text,
									id : response.deviceTypeId
								})
							});
					}
				});

				$(groupParent).on('select2-selecting',function(e){
					var string = e.object.id,
						substring = "create_";
					if(string.includes(substring)) {
						$.post(createDeviceGroupUrl, {'device_id':id,'device_group_name':e.object.text} )
							.success(function (response) {
								$(e.target).val(response.deviceGroupId);
								groupSource.push({
									value: response.deviceGroupId,
									text: e.object.text,
									id : response.deviceGroupId
								})
							});
					}
				});

				$(group).on('select2-selecting',function(e){
					var string = e.object.id,
						substring = "create_";
					if(string.includes(substring)) {
						$.post(createDeviceGroupUrl, {'device_id':id,'device_group_name':e.object.text} )
							.success(function (response) {
								$(e.target).val(response.deviceGroupId);
								groupSource.push({
									value: response.deviceGroupId,
									text: e.object.text,
									id : response.deviceGroupId
								})
							});
					}
				});
				sidebar.show();
			});
		});
		dashboardWidgets();
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
					//console.log(serieslabel);
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



	function doUpdate() {
		var t = 10000;
		var n = 20;

		if(drawableLines[0].length > n-1){
			drawableLines[0].shift();
		}

		var y = getRandomArbitrary(10,39);
		var y1 = getRandomInt(1,100);
		var x = new Date();

	//	console.log(x.toLocaleTimeString());

		drawableLines[0].push([x.toLocaleTimeString(),y]);
		drawableLines[1].push([x.toLocaleTimeString(),y1]);
		if (plotRealTimeChart) {
			$(plotArea).empty();
		}

		//console.log(plotChart);

		//plotChart.series[0].data = data;
		//il problema è che adesso i valori su y delle ticks non sono più statici
		//e cambiano ad ogni aggiornamento, quindi cambia la logica sottostante
		// devo intervenire sui valori all'interno di options.
		//options.axes.xaxis.min = data[0][0];
		//options.axes.xaxis.max = data[data.length-1][0];
		//plotChart = $.jqplot (plotArea, [data]);
		//console.log(drawableLines);
		plotRealTimeChart(plotArea,drawableLines);
		setTimeout(doUpdate, t);
	}

})(jQuery, OC);