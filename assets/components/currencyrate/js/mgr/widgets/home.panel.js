currencyrate.panel.Home = function (config) {
	config = config || {};
    var last_date = (currencyrate.config.last_date !== '') ? '<p>' +  _('cr_last_date_msg') + currencyrate.config.last_date + '</p>' : '';
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
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
				title: _('cr_list'),
				layout: 'anchor',
				items: [{
					html: _('cr_intro_msg') + last_date,
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
