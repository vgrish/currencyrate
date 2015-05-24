var currencyrate = function(config) {
    config = config || {};
    currencyrate.superclass.constructor.call(this, config);
};
Ext.extend(currencyrate, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    config: {},
    view: {},
    utils: {}
});
Ext.reg('currencyrate', currencyrate);

currencyrate = new currencyrate();
