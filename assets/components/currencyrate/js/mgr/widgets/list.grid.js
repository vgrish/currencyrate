currencyrate.grid.List = function(config) {
    config = config || {};
    if (!config.id) {
        config.id = 'currencyrate-grid-list';
    }

    this.dd = function(grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function(dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: currencyrate.config.connector_url,
                        params: {
                            action: config.action || 'mgr/valute/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function(r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function(r) {
                                    dd.el.unmask();
                                },
                                scope: grid
                            }
                        }
                    });
                }
            }
        });
    };

    Ext.applyIf(config, {
        url: currencyrate.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/valute/getlist'
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function(rec, ri, p) {
                return !rec.data.active ? 'currencyrate-row-disabled' : '';
            }
        },
        save_action: 'mgr/valute/updatefromgrid',
        autosave: true,
        save_callback: this.updateRow,

        paging: true,
        remoteSort: true,
        autoHeight: true,

        ddGroup: 'dd',
        enableDragDrop: true,
        listeners: {
            render: {
                fn: this.dd,
                scope: this
            }
        }

    });
    currencyrate.grid.List.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function() {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(currencyrate.grid.List, MODx.grid.Grid, {
    windows: {},

    getMenu: function(grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = currencyrate.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },
    getFields: function(config) {
        return ['id', 'charcode', 'name', 'value', 'nominal', 'rate', 'valuerate', 'active'];
    },
    getColumns: function(config) {
        return [{
            header: _('cr_valute_id'),
            dataIndex: 'id',
            sortable: true,
            width: 50
        }, {
            header: _('cr_valute_charcode'),
            dataIndex: 'charcode',
            sortable: true,
            width: 100,
        }, {
            header: _('cr_valute_name'),
            dataIndex: 'name',
            sortable: false,
            width: 250,
        }, {
            header: _('cr_valute_value'),
            dataIndex: 'value',
            sortable: true,
            width: 150,
        }, {
            header: _('cr_valute_nominal'),
            dataIndex: 'nominal',
            sortable: true,
            width: 100,
        }, {
            header: _('cr_valute_rate'),
            dataIndex: 'rate',
            sortable: true,
            width: 100,
            editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('cr_valute_valuerate'),
            dataIndex: 'valuerate',
            sortable: true,
            width: 100,
            decimalPrecision: 4
        }, {
            header: _('cr_valute_active'),
            dataIndex: 'active',
            sortable: true,
            width: 100,
            editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }];
    },
    getTopBar: function(config) {
        return [{
            text: '<i class="' + (MODx.modx23 ? 'icon icon-refresh' : 'fa fa-refresh') + '"></i> ' + _('cr_valute_index_create'),
            //handler: this.indexCreate(),
            handler: function() {
                this.indexCreate();
            },
            scope: this
        }, '-', {
            text: '<i class="' + (MODx.modx23 ? 'icon icon-trash-o' : 'fa fa-trash-o') + '"></i> ' + _('cr_valute_index_clear'),
            handler: function() {
                this.indexClear();
            },
            scope: this
        }];
    },
    indexCreate: function() {
        var el = this.getEl();
        el.mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/index/create'
            },
            listeners: {
                success: {
                    fn: function() {
                        this.refresh();
                        el.unmask();
                    },
                    scope: this
                }
            }
        })
    },
    indexClear: function(btn, e) {
        MODx.msg.confirm({
            title: _('cr_index_remove_all'),
            text: _('cr_index_remove_all_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/index/clear'
            },
            listeners: {
                success: {
                    fn: function(r) {
                        this.refresh();
                    },
                    scope: this
                }
            }
        });
    },
    _getSelectedIds: function() {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }
        return ids;
    },
    updateRow: function(response) {
        var row = response.object;
        var items = this.store.data.items;

        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.id == row.id) {
                item.data = row;
            }
        }
    }

});
Ext.reg('currencyrate-grid-list', currencyrate.grid.List);
