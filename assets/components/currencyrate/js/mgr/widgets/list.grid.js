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
        save_callback: this._updateRow,

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

    /* Clear selection on grid refresh */
    this.store.on('load', function() {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(currencyrate.grid.List, MODx.grid.Grid, {

    windows: {},

    getFields: function(config) {
        return ['id', 'numcode', 'charcode', 'name', 'value', 'nominal', 'rate', 'valuerate', 'active', 'actions'];
    },

    getTopBar: function(config) {
        var tbar = [];

        tbar.push({
            text: '<i class="fa fa-cogs"></i> ',
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('currencyrate_create'),
                cls: 'currencyrate-cogs',
                handler: this.create,
                scope: this
            },'-',{
                text: '<i class="fa fa-refresh"></i> ' + _('currencyrate_index_create'),
                cls: 'currencyrate-cogs',
                handler: this.indexCreate,
                scope: this
            }, {
                text: '<i class="fa fa-trash-o"></i> ' + _('currencyrate_index_clear'),
                cls: 'currencyrate-cogs',
                handler: this.indexClear,
                scope: this
            }]
        });

        if (1 == MODx.config.currencyrate_button_prices_update) {
            tbar.push({
                text: '<i class="fa fa-refresh"></i> ' + _('currencyrate_prices_update'),
                handler: this.updatePrices,
                scope: this
            });
        }

        tbar.push('->',{
            xtype: 'currencyrate-combo-active',
            width: 210,
            custm: true,
            clear: true,
            addall: true,
            value: 1,
            listeners: {
                select: {
                    fn: this._filterByCombo,
                    scope: this
                },
                afterrender: {
                    fn: this._filterByCombo,
                    scope: this
                }
            }
        });

        return tbar;
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = currencyrate.utils.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
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
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    getColumns: function (config) {
        var columns = [/*this.exp, this.sm*/];
        var add = {
            id: {
                width: 15,
                sortable: true
            },
            numcode: {
                width: 20,
                sortable: true,
                editor: {
                    xtype: 'textfield'
                }
            },
            charcode: {
                width: 20,
                sortable: true,
                editor: {
                    xtype: 'textfield'
                }
            },
            name: {
                width: 50,
                sortable: true,
                editor: {
                    xtype: 'textfield'
                }
            },
            value: {
                width: 20,
                sortable: true
            },
            nominal: {
                width: 20,
                sortable: true
            },
            rate: {
                width: 20,
                sortable: true,
                editor: {
                    xtype: 'textfield'
                }
            },
            valuerate: {
                width: 20,
                sortable: true,
                decimalPrecision: 4
            },
            actions: {
                width: 20,
                sortable: false,
                renderer: currencyrate.utils.renderActions,
                id: 'actions'
            }
        };

        for (var field in add) {
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('currencyrate_header_' + field),
                    tooltip: _('currencyrate_tooltip_' + field),
                    dataIndex: field
                });
                columns.push(add[field]);
            }
        }

        return columns;
    },

    setAction: function(method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: currencyrate.config.connector_url,
            params: {
                action: 'mgr/valute/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function(response) {
                        this.refresh();
                        if (response.message != '') {
                            MODx.msg.alert(_('info'),response.message);
                        }
                    },
                    scope: this
                },
                failure: {
                    fn: function(response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    active: function(btn, e) {
        this.setAction('setproperty', 'active', 1);
    },

    inactive: function(btn, e) {
        this.setAction('setproperty', 'active', 0);
    },

    updatePrices: function (btn, e) {
        this.setAction('updateprices', 'false', 0);
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
            title: _('currencyrate_index_remove_all'),
            text: _('currencyrate_index_remove_all_confirm'),
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

    remove: function() {
        Ext.MessageBox.confirm(
            _('currencyrate_action_remove'),
            _('currencyrate_confirm_remove'),
            function(val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    create: function (btn, e) {
        var record = {
            active: 1
        };
        var w = MODx.load({
            xtype: 'currencyrate-valute-window-create',
            class: this.config.class,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    update: function(btn, e, row) {
        var record = typeof(row) != 'undefined' ? row.data : this.menu.record;
        MODx.Ajax.request({
            url: currencyrate.config.connector_url,
            params: {
                action: 'mgr/valute/get',
                id: record.id
            },
            listeners: {
                success: {
                    fn: function(r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'currencyrate-valute-window-create',
                            title: _('currencyrate_action_update'),
                            action: 'mgr/valute/update',
                            record: record,
                            listeners: {
                                success: {
                                    fn: this.refresh,
                                    scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(record);
                        w.show(e.target);
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

    _updateRow: function (response) {
        this.refresh();
    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value;
        this.getBottomToolbar().changePage(1);
    },

});
Ext.reg('currencyrate-grid-list', currencyrate.grid.List);
