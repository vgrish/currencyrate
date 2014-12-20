currencyrate.window.CreateItem = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'currencyrate-item-window-create';
	}
	Ext.applyIf(config, {
		title: _('currencyrate_item_create'),
		width: 550,
		autoHeight: true,
		url: currencyrate.config.connector_url,
		action: 'mgr/item/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	currencyrate.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(currencyrate.window.CreateItem, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('currencyrate_item_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
		}, {
			xtype: 'textarea',
			fieldLabel: _('currencyrate_item_description'),
			name: 'description',
			id: config.id + '-description',
			height: 150,
			anchor: '99%'
		}, {
			xtype: 'xcheckbox',
			boxLabel: _('currencyrate_item_active'),
			name: 'active',
			id: config.id + '-active',
			checked: true,
		}];
	}

});
Ext.reg('currencyrate-item-window-create', currencyrate.window.CreateItem);


currencyrate.window.UpdateItem = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'currencyrate-item-window-update';
	}
	Ext.applyIf(config, {
		title: _('currencyrate_item_update'),
		width: 550,
		autoHeight: true,
		url: currencyrate.config.connector_url,
		action: 'mgr/item/update',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	currencyrate.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(currencyrate.window.UpdateItem, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		}, {
			xtype: 'textfield',
			fieldLabel: _('currencyrate_item_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
		}, {
			xtype: 'textarea',
			fieldLabel: _('currencyrate_item_description'),
			name: 'description',
			id: config.id + '-description',
			anchor: '99%',
			height: 150,
		}, {
			xtype: 'xcheckbox',
			boxLabel: _('currencyrate_item_active'),
			name: 'active',
			id: config.id + '-active',
		}];
	}

});
Ext.reg('currencyrate-item-window-update', currencyrate.window.UpdateItem);