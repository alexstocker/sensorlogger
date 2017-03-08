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

	OCA.SensorLogger = OCA.SensorLogger || {};

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

	$(document).ready(function () {
		var sidebar = $('#app-sidebar');

		var _onClickAction = function(event) {
			var $target = $(event.target);
			if (!$target.hasClass('menuItem')) {
				$target = $target.closest('.menuItem');
			}
			event.preventDefault();
			$target.closest('ul').find('.menuItem.active').removeClass('active');
			$target.addClass('active');
			sidebar.hide();
		};

		$('#showList').click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/showList');
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});
		});

		$('#showDashboard').click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/showDashboard');
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});

		});

		$('#deviceList').click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceList');
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});

		});

		$('#deviceTypeList').click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceTypeList');
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});

		});

		$('#deviceGroupList').click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/deviceGroupList');
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});

		});

		$('#dataTypeList').click(function (e) {
			_onClickAction(e);
			var url = OC.generateUrl('/apps/sensorlogger/dataTypeList');
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});

		});

		$('#app-content-wrapper').on('click','.deviceChart',function (e) {
			sidebar.hide();
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/deviceChart/'+id);
			$.get(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
				loadChart();
			});
		});

		$('#app-content-wrapper').on('click','.deviceListData',function (e) {
			sidebar.hide();
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+id);
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});
		});

		$('#app-content-wrapper').on('click','.icon-close',function(e) {
			sidebar.hide();
		});

		$('#app-content-wrapper').on('click','.deviceEdit',function(e) {
			var target = $(e.target);
			if(e.target.nodeName != 'TD') {
				return;
			}

			if(sidebar.is(':visible')) {
				sidebar.hide();
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

	});
	
	var loadChart = function() {
		var plotArea = $('div#chart');
		var id = $(plotArea).data('id');
		var url = OC.generateUrl('/apps/sensorlogger/chartData/' + id);
		$.getJSON(url,"json").success(function(data) {
			var dataLines = [];
			var serieslabel = [];
			var line1 = [];
			var line2 = [];

			if(data.dataTypes && data.logs) {
				if (data.logs[0] && data.logs[0].data.length > 0) {
					var lines = data.logs[0].data;
					$.each(data.dataTypes, function (index, item) {
						serieslabel.push(['label', item.description])
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
						clonedPlotArea.attr('id', 'chart-' + i).appendTo('#app-content-wrapper')
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

			var drawableLines = [];
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
				var plot1 = $.jqplot("chart",drawableLines,{
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
							showMarker:false
							//markerOptions: {size: 2, style: "x"}
						},{
							yaxis: 'y2axis',
							showMarker:false,
							tickInterval: 1,
							//markerOptions: {style:"circle"}
						},
						{yaxis: 'yaxis'},
						{yaxis: 'y2axis'}
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
						location: 's	',
						placement: 'inside',
						fontSize: '11px',
						fontFamily: ["Lucida Grande","Lucida Sans Unicode","Arial","Verdana","sans-serif"],
						rendererOptions: {
							seriesToggle: 'normal'
						}
					}
				});
			} catch (err) {
				$(plotArea).html(err);
			}
		});
	}

})(jQuery, OC);