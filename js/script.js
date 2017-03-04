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
		var _onClickAction = function(event) {
			var $target = $(event.target);
			if (!$target.hasClass('menuItem')) {
				$target = $target.closest('.menuItem');
			}
			event.preventDefault();
			$target.closest('ul').find('.menuItem.active').removeClass('active');
			$target.addClass('active');
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

		$('body').on('click','.deviceChart',function (e) {
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/deviceChart/'+id);
			$.get(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
				loadChart();
			});
		});

		$('body').on('click','.deviceListData',function (e) {
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/showDeviceData/'+id);
			$.post(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
			});
		});

		$('body').on('click','.icon-close',function(e) {
			var sidebar = $('#app-sidebar');
			sidebar.hide();
		});

		$('body').on('click','.deviceEdit',function(e) {
			var sidebar = $('#app-sidebar');
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/showDeviceDetails/'+id);
			$.post(url).success(function (response) {
				console.log(response);
				sidebar.find('.title').empty();
				sidebar.find('.title').append(response.name);

				var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
				bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');
				sidebar.find('.bodyDetails').remove();

				var group = bodyDetailsContainer.clone().append('Group: '+response.group);
				var groupParent = bodyDetailsContainer.clone().append('Parent group: '+response.groupParent);
				var uuid = bodyDetailsContainer.clone().append('UUID: '+response.uuid);
				var type = bodyDetailsContainer.clone().append('Type: '+response.type);

				sidebar.find('.body').append(group);
				sidebar.find('.body').append(groupParent);
				sidebar.find('.body').append(uuid);
				sidebar.find('.body').append(type);
				//sidebar.find('.body').append(bodyDetailsContainer.append(groupParent));
				//$('#app-content-wrapper').empty().append(response);
				//var sidebar = '<diapp-sidebar'
				//if(!sidebar.is(':visible')) {
					sidebar.toggle();
				//}

			});
		});
		
	});

	var loadChart = function() {
		var id = $('div#chart').data('id');
		var url = OC.generateUrl('/apps/sensorlogger/chartData/' + id);
		$.getJSON(url,"json").success(function(data) {
			//console.log(data);
			var line1 = [];
			var line2 = [];
			var content = data;
			$.each(content, function (index, item) {
				line1.push([item.created_at, parseFloat(item.temperature)])
				line2.push([item.created_at, parseFloat(item.humidity)])
			});
			var plot1 = $.jqplot("chart",[line1,line2],{
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
				]
			});
		});
	}

})(jQuery, OC);