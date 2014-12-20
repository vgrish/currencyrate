currencyrate.grid.List = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'currencyrate-grid-list';
    }
    Ext.applyIf(config, {
        url: currencyrate.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/valute/getlist'
        },
        listeners: {
/*            rowDblClick: function (grid, rowIndex, e) {
             var row = grid.store.getAt(rowIndex);
             this.updateItem(grid, e, row);
             }*/
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec, ri, p) {
                return !rec.data.active
                    ? 'currencyrate-row-disabled'
                    : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    currencyrate.grid.List.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(currencyrate.grid.List, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = currencyrate.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    /*createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'currencyrate-valute-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues({active: true});
        w.show(e.target);
    },*/

/*    updateItem: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/valute/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'currencyrate-valute-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },*/

    removeItem: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
                ? _('currencyrate_list_remove')
                : _('currencyrate_valute_remove'),
            text: ids.length > 1
                ? _('currencyrate_list_remove_confirm')
                : _('currencyrate_valute_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/valute/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },

    disableItem: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/valute/disable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    enableItem: function (act, btn, e) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/valute/enable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    getFields: function (config) {
        return ['id', 'name', 'description', 'active', 'actions'];
    },

    getColumns: function (config) {
        return [{
            header: _('currencyrate_valute_id'),
            dataIndex: 'id',
            sortable: true,
            width: 70
        }, {
            header: _('currencyrate_valute_name'),
            dataIndex: 'name',
            sortable: true,
            width: 200,
        }, {
            header: _('currencyrate_valute_description'),
            dataIndex: 'description',
            sortable: false,
            width: 250,
        }, {
            header: _('currencyrate_valute_active'),
            dataIndex: 'active',
            renderer: currencyrate.utils.renderBoolean,
            sortable: true,
            width: 100,
        }, {
            header: _('currencyrate_grid_actions'),
            dataIndex: 'actions',
            renderer: currencyrate.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actions'
        }];
    },

    getTopBar: function (config) {
        return [
            {
                text: '<i class="' + (MODx.modx23 ? 'icon icon-refresh' : 'fa fa-refresh') + '"></i> ' + _('cr_valute_index_create'),
                //handler: this.indexCreate(),
                handler: function() {
                    this.indexCreate();
                },
                scope: this
            }
            , '-',
            {
                text: '<i class="' + (MODx.modx23 ? 'icon icon-trash-o' : 'fa fa-trash-o') + '"></i> ' + _('cr_valute_index_clear'),
                handler: this.indexClear(),
                scope: this
            }
        ];

    },

    indexCreate: function () {
        var el = this.getEl();

/*
        console.log(el);
        return;
*/

        el.mask(_('loading'),'x-mask-loading');
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/index/create'
            },
            listeners: {
                success: {fn:function(r) {this.refresh();}, scope:this}
            }
        })
    },




    indexClear: function () {

    },



    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('currencyrate-grid-list', currencyrate.grid.List);