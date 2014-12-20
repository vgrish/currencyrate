currencyrate.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'currencyrate-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('currencyrate') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
				title: _('currencyrate_items'),
				layout: 'anchor',
				items: [{
					html: _('currencyrate_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'currencyrate-grid-list',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	currencyrate.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(currencyrate.panel.Home, MODx.Panel);
Ext.reg('currencyrate-panel-home', currencyrate.panel.Home);
