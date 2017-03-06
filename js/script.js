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

		var editDetailOld = function(elem) {
			var editable = $(elem).find('.editable');
			var elemData= editable.data();
			$(editable).each( function () {
				$(this).editable('click',function(e) {
					console.log(e.value);
					if(e.value.length != 0) {
						var url = OC.generateUrl('/apps/sensorlogger/updateDevice/'+elemData.id);
						$.post(url, {'field':elemData.field,'value':e.value} );
					}
				});
			});
		};

		var editDetail = function(elem) {
			console.log(elem);

		};

		$('body').on('click','.deviceEdit',function(e) {
			var sidebar = $('#app-sidebar');

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

				sidebar.find('.title').empty();
				//sidebar.find('.title span.handler').append(response.name);
				//sidebar.find('.title span.handler').attr('data-field','name').attr('data-id',id);

				var updateUrl = OC.generateUrl('/apps/sensorlogger/updateDevice/'+id);

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

				sidebar.find('.title').append(title);

				var groupSource = [];

				for (group in response.groups) {
					groupSource.push({
						value : response.groups[group].id,
						text: response.groups[group].device_group_name}
					);
				}

				var typeSource = [];

				for (type in response.types) {
					typeSource.push({
						value : response.types[type].id,
						text: response.types[type].device_type_name}
					);

				}

				var groupSelect = $('<a/>',{
					'id':'group_id',
					'href': '#',
					'data-type': 'select',
					'data-field': 'group',
					'data-pk': id,
					'data-url': updateUrl,
					'data-title': response.deviceGroupName
				}).editable({
					value: response.deviceDetails.group,
					source: groupSource
				});

				var groupParentSelect = $('<a/>',{
					'id':'group_parent_id',
					'href': '#',
					'data-type': 'select',
					'data-field': 'group',
					'data-pk': id,
					'data-url': updateUrl,
					'data-title': response.deviceGroupParentName
				}).editable({
					value: response.deviceDetails.groupParent,
					source: groupSource
				});

				var typeSelect = $('<a/>',{
					'id':'type_id',
					'href': '#',
					'data-type': 'select',
					'data-field': 'type',
					'data-pk': id,
					'data-url': updateUrl,
					'data-title': response.deviceTypeName
				}).editable({
					value: response.deviceDetails.type,
					source: typeSource
				});

				//<a href="#" id="status" data-type="select" data-pk="1" data-url="/post" data-title="Select status"></a>

				var bodyDetailsContainer = sidebar.find('.tpl_bodyDetails').clone();
				bodyDetailsContainer.removeClass('tpl_bodyDetails').addClass('bodyDetails');
				sidebar.find('.bodyDetails').remove();

				var uuid = bodyDetailsContainer.clone().append('UUID: '+response.deviceDetails.uuid);
				//var group = bodyDetailsContainer.clone().append('Group: '+response.deviceGroupName);
				var group = bodyDetailsContainer.clone().append(groupSelect);
				//var groupParent = bodyDetailsContainer.clone().append('Parent group: '+response.deviceDetails.deviceGroupParentName);
				var groupParent = bodyDetailsContainer.clone().append(groupParentSelect);
				//var type = bodyDetailsContainer.clone().append('Type: '+response.deviceDetails.deviceTypeName);
				var type = bodyDetailsContainer.clone().append(typeSelect);

				sidebar.find('.body').append(uuid);
				sidebar.find('.body').append(group);
				sidebar.find('.body').append(groupParent);
				sidebar.find('.body').append(type);

				editDetail(sidebar);
				sidebar.show();
			});
		});

	});

	$(document).ready(function() {
		//toggle `popup` / `inline` mode
		$.fn.editable.defaults.mode = 'inline';

		//make username editable
		$('#username').editable();

		//make status editable
		$('#status').editable({
			type: 'select',
			title: 'Select status',
			placement: 'right',
			value: 2,
			source: [
				{value: 1, text: 'status 1'},
				{value: 2, text: 'status 2'},
				{value: 3, text: 'status 3'}
			]
			/*
			 //uncomment these lines to send data on server
			 ,pk: 1
			 ,url: '/post'
			 */
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