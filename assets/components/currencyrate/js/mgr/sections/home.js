currencyrate.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'currencyrate-panel-home', renderTo: 'currencyrate-panel-home-div'
		}]
	});
	currencyrate.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(currencyrate.page.Home, MODx.Component);
Ext.reg('currencyrate-page-home', currencyrate.page.Home);