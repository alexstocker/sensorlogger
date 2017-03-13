(function() {
	
	var Navigation = function($el) {
		this.initialize($el);
	};
	
	Navigation.prototype = {
		
		_activeItem: null,
		
		$currentContent: null,
		
		initialize: function($el) {
			this.$el = $el;
			this._activeItem = null;
			this.$currentContent = null;
			this._setupEvents();
		},
		
		_setupEvents: function() {
			this.$el.on('click', 'li a', _.bind(this._onClickItem, this));
		},
		
		getActiveContainer: function() {
			return this.$currentContent;
		},
		
		getActiveItem: function() {
			return this._activeItem;
		},
		
		setActiveItem: function(itemId, options) {
			var oldItemId = this._activeItem;
			if (itemId === this._activeItem) {
				if (!options || !options.silent) {
					this.$el.trigger(
						new $.Event('itemChanged', {itemId: itemId, previousItemId: oldItemId})
					);
				}
				return;
			}
			this.$el.find('li').removeClass('active');
			if (this.$currentContent) {
				this.$currentContent.addClass('hidden');
				this.$currentContent.trigger(jQuery.Event('hide'));
			}
			this._activeItem = itemId;
			this.$el.find('li[data-id=' + itemId + ']').addClass('active');
			this.$currentContent = $('#app-content-' + itemId);
			this.$currentContent.removeClass('hidden');
			if (!options || !options.silent) {
				this.$currentContent.trigger(jQuery.Event('show'));
				this.$el.trigger(
					new $.Event('itemChanged', {itemId: itemId, previousItemId: oldItemId})
				);
			}
		},
		
		itemExists: function(itemId) {
			return this.$el.find('li[data-id=' + itemId + ']').length;
		},
		
		_onClickItem: function(ev) {
			var $target = $(ev.target);
			var itemId = $target.closest('li').attr('data-id');
			this.setActiveItem(itemId);
			ev.preventDefault();
		}
	};

	OCA.SensorLogger.Navigation = Navigation;

})();