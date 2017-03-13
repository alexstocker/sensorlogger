(function() {

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

	OCA.SensorLogger.App = {

		navigation: null,
		devices: null,

		initialize: function() {
			this.navigation = new OCA.SensorLogger.Navigation($('#app-navigation'));

			if ($('#deviceNotFound').val() === "1") {
				OC.Notification.showTemporary(t('sensorlogger', 'Device could not be found'));
			}

			var urlParams = OC.Util.History.parseUrlQuery();
			var deviceActions = new OCA.SensorLogger.DeviceActions();
			deviceActions.registerDefaultActions();
			//deviceActions.merge(window.DeviceActions);
			deviceActions.merge(OCA.SensorLogger.deviceActions);

			this.devices = OCA.SensorLogger.Devices;

			this.deviceList = new OCA.SensorLogger.DeviceList(
				$('#app-content-devices'), {
					scrollContainer: $('#app-content-wrapper'),
					dragOptions: dragOptions,
					folderDropOptions: folderDropOptions,
					deviceActions: deviceActions,
					allowLegacyActions: true,
					scrollTo: urlParams.scrollto,
					devicesClient: OC.SensorLogger.getClient(),
					sorting: {
						mode: $('#defaultDeviceSorting').val(),
						direction: $('#defaultDeviceSortingDirection').val()
					}
				}
			);
			this.devices.initialize();
		}

	}

})();

$(document).ready(function() {
	_.defer(function() {
		OCA.SensorLogger.App.initialize();
	});
});