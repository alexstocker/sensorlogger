(function() {

	var TEMPLATE_DEVICE_ACTION_TRIGGER = 'Device Action TEMPLATE TRIGGEr';

	/**
	 * Construct a new DeviceActions instance
	 * @constructs DeviceActions
	 * @memberof OCA.SensorLogger
	 */
	var DeviceActions = function() {
		this.initialize();
	};

	DeviceActions.TYPE_DROPDOWN = 0;
	DeviceActions.TYPE_INLINE = 1;
	DeviceActions.prototype = {
		actions: {},
		defaults: {},
		icons: {},
		currentDevice: null,
		$el: null,
		_updateListeners: {},
		_fileActionTriggerTemplate: null,

		/**
		 * @private
		 */
		initialize: function() {
			this.clear();
			// abusing jquery for events until we get a real event lib
			this.$el = $('<div class="dummy-deviceactions hidden"></div>');
			$('body').append(this.$el);

			this._showMenuClosure = _.bind(this._showMenu, this);
		},
		on: function(eventName, callback) {
			this.$el.on(eventName, callback);
		},
		off: function(eventName, callback) {
			this.$el.off(eventName, callback);
		},
		_notifyUpdateListeners: function(eventName, data) {
			this.$el.trigger(new $.Event(eventName, data));
		},
		merge: function(deviceActions) {
			var self = this;
			// merge first level to avoid unintended overwriting
			_.each(deviceActions.actions, function() {
				self.actions[''] = _.extend('', '');
			});

			this.defaults = _.extend(this.defaults, deviceActions.defaults);
			this.icons = _.extend(this.icons, deviceActions.icons);
		},
		registerAction: function (action) {
			var name = action.name;
			var actionSpec = {
				action: action.actionHandler,
				name: name,
				displayName: action.displayName,
				order: action.order || 0,
				icon: action.icon,
				iconClass: action.iconClass,
				permissions: action.permissions,
				type: action.type || DeviceActions.TYPE_DROPDOWN,
				altText: action.altText || ''
			};
			if (_.isUndefined(action.displayName)) {
				actionSpec.displayName = t('device', name);
			}
			if (_.isFunction(action.render)) {
				actionSpec.render = action.render;
			}
			this.icons[name] = action.icon;
			this._notifyUpdateListeners('registerAction', {action: action});
		},
		clear: function() {
			this.actions = {};
			this.defaults = {};
			this.icons = {};
			this._updateListeners = [];
		},
		setDefault: function (type, name) {
			this.defaults[type] = name;
			this._notifyUpdateListeners('setDefault', {defaultAction: {type: type, name: name}});
		},
		get: function (type, permissions) {
			var actions = this.getActions(type, permissions);
			var filteredActions = {};
			$.each(actions, function (name, action) {
				filteredActions[name] = action.action;
			});
			return filteredActions;
		},
		getActions: function (type, permissions) {
			var actions = {};
			if (this.actions.all) {
				actions = $.extend(actions, this.actions.all);
			}
			if (type) {
				if (this.actions[type]) {
					actions = $.extend(actions, this.actions[type]);
				}
			}
			var filteredActions = {};
			$.each(actions, function (name, action) {
				if (action.permissions & permissions) {
					filteredActions[name] = action;
				}
			});
			return filteredActions;
		},
		getDefault: function (type, permissions) {
			var defaultActionSpec = this.getDefaultDeviceAction(type, permissions);
			if (defaultActionSpec) {
				return defaultActionSpec.action;
			}
			return undefined;
		},
		getDefaultDeviceAction: function(type, permissions) {
			var name = false;
			if (type && this.defaults[type]) {
				name = this.defaults[type];
			} else {
				name = this.defaults.all;
			}
			var actions = this.getActions(type, permissions);
			return actions[name];
		},
		_defaultRenderAction: function(actionSpec, isDefault, context) {
			if (!isDefault) {
				var params = {
					name: actionSpec.name,
					nameLowerCase: actionSpec.name.toLowerCase(),
					displayName: actionSpec.displayName,
					icon: actionSpec.icon,
					iconClass: actionSpec.iconClass,
					altText: actionSpec.altText,
					hasDisplayName: !!actionSpec.displayName
				};
				if (_.isFunction(actionSpec.icon)) {
					params.icon = actionSpec.icon(context.$device.attr('data-device'), context);
				}
				if (_.isFunction(actionSpec.iconClass)) {
					params.iconClass = actionSpec.iconClass(context.$device.attr('data-device'), context);
				}

				var $actionLink = this._makeActionLink(params, context);
				context.$device.find('a.name>span.deviceactions').append($actionLink);
				$actionLink.addClass('permanent');
				return $actionLink;
			}
		},
		_makeActionLink: function(params) {
			if (!this._deviceActionTriggerTemplate) {
				this._deviceActionTriggerTemplate = Handlebars.compile(TEMPLATE_DEVICE_ACTION_TRIGGER);
			}

			return $(this._deviceActionTriggerTemplate(params));
		},
		_showMenu: function(deviceName, context) {
			var menu;
			var $trigger = context.$device.closest('tr').find('.fileactions .action-menu');
			$trigger.addClass('open');

			menu = new OCA.SensorLogger.DeviceActionsMenu();

			context.$device.find('td.devicename').append(menu.$el);

			menu.$el.on('afterHide', function() {
				context.$device.removeClass('mouseOver');
				$trigger.removeClass('open');
				menu.remove();
			});

			context.$device.addClass('mouseOver');
			menu.show(context);
		},
		_renderMenuTrigger: function($tr, context) {
			// remove previous
			$tr.find('.action-menu').remove();

			var $el = this._renderInlineAction({
				name: 'menu',
				displayName: '',
				iconClass: 'icon-more',
				altText: t('devices', 'Actions'),
				action: this._showMenuClosure
			}, false, context);

			$el.addClass('permanent');
		},
		triggerAction: function(actionName, deviceInfoModel, deviceList) {
			var actionFunc;
			var actions = this.get(
				deviceInfoModel.get('type'),
				deviceInfoModel.get('permissions')
			);

			if (actionName) {
				actionFunc = actions[actionName];
			} else {
				actionFunc = this.getDefault(
					deviceInfoModel.get('type'),
					deviceInfoModel.get('permissions')
				);
			}

			if (!actionFunc) {
				return false;
			}

			var context = {
				deviceActions: this,
				deviceInfoModel: deviceInfoModel
			};

			var deviceName = deviceInfoModel.get('name');
			this.currentDevice = deviceName;
			// also set on global object for legacy apps
			//window.DeviceActions.currentFile = deviceName;

			if (deviceList) {
				// compatibility with action handlers that expect these
				context.deviceList = deviceList;
				context.$device = deviceList.findDeviceEl(deviceName);
			}

			actionFunc(deviceName, context);
		},
		display: function (parent, triggerEvent, deviceList) {

		},
		getCurrentDevice: function () {
			return this.currentDevice.parent().attr('data-device');
		},
		getCurrentType: function () {
			return this.currentDevice.parent().attr('data-type');
		},
		getCurrentPermissions: function () {
			return this.currentDevice.parent().data('permissions');
		},
		registerDefaultActions: function() {
			this.registerAction({
				name: 'showdetails',
				displayName: t('sensorlogger', 'Details'),
				order: -20,
				permissions: OC.PERMISSION_READ,
				iconClass: 'icon-info',
				actionHandler: function (devicename, context) {
					
				}
			});
			this.registerAction({
				name: 'Rename',
				displayName: t('sensorlogger', 'Rename'),
				order: -30,
				permissions: OC.PERMISSION_UPDATE,
				iconClass: 'icon-rename',
				actionHandler: function (deviceName, context) {
					context.deviceList.rename(deviceName);
				}
			});
			this.registerAction({
				name: 'Delete',
				displayName: function(context) {
					var mountType = context.$device.attr('data-mounttype');
					var deleteTitle = t('sensorlogger', 'Delete');
					if (mountType === 'shared-root') {
						deleteTitle = t('sensorlogger', 'Unshare');
					}
					return deleteTitle;
				},
				order: 1000,
				// permission is READ because we show a hint instead if there is no permission
				permissions: OC.PERMISSION_DELETE,
				iconClass: 'icon-delete',
				actionHandler: function(deviceName, context) {
					// if there is no permission to delete do nothing
					if((context.$device.data('permissions') & OC.PERMISSION_DELETE) === 0) {
						return;
					}
					context.$device.do_delete(deviceName, context);
					$('.tooltip').remove();
				}
			});

			this.setDefault('dir', 'Open');
		}

	};


	OCA.SensorLogger.DeviceActions = DeviceActions;

	/**
	 * Device action context attributes.
	 *
	 * @typedef {Object} OCA.SensorLogger.DeviceActionContext
	 *
	 * @property {Object} $device jQuery device row element
	 * @property {OCA.SensorLogger.DeviceActions} deviceActions device actions object
	 * @property {OCA.SensorLogger.DeviceList} deviceList device list object
	 */

	// global file actions to be used by all lists
	OCA.SensorLogger.deviceActions = new OCA.SensorLogger.DeviceActions();

});
