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
			console.log(this);
			var id = $(this).data('id');
			var url = OC.generateUrl('/apps/sensorlogger/deviceChart/'+id);
			$.get(url).success(function (response) {
				$('#app-content-wrapper').empty().append(response);
				loadChart();
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
			var content = $.parseJSON(data);
			$.each(content, function (index, item) {
				line1.push([item.created_at, parseFloat(item.temperature)])
				line2.push([item.created_at, parseFloat(item.humidity)])
			});
			console.log(line1);
			var plot1 = $.jqplot("chart",[line1,line2],{
				//title: 'GRAPT TITLE',
				axes: {
					xaxis:{
						//label:"x axis",
						renderer:$.jqplot.DateAxisRenderer,
						tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
						tickOptions:{formatString:'%Y-%m-%d %n %H:%M:%S', angle: -45}
					}
				},
				series: [
					{
						markerOptions: {size: 7, style: "x"}
					},{
						markerOptions: {style:"circle"}
					}
				]
			});
		});
	}

})(jQuery, OC);