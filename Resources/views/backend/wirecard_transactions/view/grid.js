Ext.define('Shopware.apps.WirecardTransactions.view.Grid', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.wirecardee-transactions-grid',
    region: 'center',

    viewConfig: {
        enableTextSelection: true
    },

    snippets: {
        OrderNumber: '{s name="OrderNumber" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        PaymentUniqueId: '{s name="PaymentUniqueId" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        Type: '{s name="Type" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        TransactionId: '{s name="TransactionId" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        ParentTransactionId: '{s name="ParentTransactionId" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        ProviderTransactionId: '{s name="ProviderTransactionId" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        TransactionType: '{s name="TransactionType" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        TransactionState: '{s name="TransactionState" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        PaymentMethod: '{s name="PaymentMethod" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        Amount: '{s name="Amount" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        Currency: '{s name="Currency" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        OrderCanceledErrorTitle: '{s name="OrderCanceledErrorTitle" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}',
        OrderCanceledErrorText: '{s name="OrderCanceledErrorText" namespace="backend/wirecard_elastic_engine/transactions_window"}{/s}'
    },

    configure: function () {
        var me = this;

        return {
            columns: me.getColumns(),
            rowEditing: false,
            deleteButton: false,
            deleteColumn: false,
            editButton: false,
            editColumn: false,
            addButton: false
        };
    },

    /**
     * @returns { Object }
     */
    getColumns: function () {
        var me = this;

        return {
            orderNumber: {
                header: me.snippets.OrderNumber,
                draggable: false,
                renderer: me.orderNumberRenderer
            },
            paymentUniqueId: {
                header: me.snippets.PaymentUniqueId,
                draggable: false
            },
            type: {
                header: me.snippets.Type,
                draggable: false
            },
            transactionId: {
                header: me.snippets.TransactionId,
                draggable: false
            },
            parentTransactionId: {
                header: me.snippets.ParentTransactionId,
                draggable: false
            },
            providerTransactionId: {
                header: me.snippets.ProviderTransactionId,
                draggable: false
            },
            transactionType: {
                header: me.snippets.TransactionType,
                draggable: false
            },
            paymentMethod: {
                header: me.snippets.PaymentMethod,
                draggable: false,
                renderer: me.paymentMethodRenderer
            },
            amount: {
                header: me.snippets.Amount,
                draggable: false
            },
            currency: {
                header: me.snippets.Currency,
                draggable: false
            }
        };
    },

    /**
     * @returns { Array }
     */
    createActionColumnItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push({
            iconCls: 'sprite-magnifier-medium',
            tooltip: 'Open transaction details',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                var historyWindow = Ext.create('Shopware.apps.WirecardExtendOrder.view.HistoryWindow', { record: record });
                historyWindow.show();
            }
        });

        items.push({
            iconCls: 'sprite-pencil',
            tooltip: 'Open order details',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                if (!record.get('orderId') || record.get('orderStatus') < 0) {
                    Shopware.Notification.createStickyGrowlMessage({
                        title: me.snippets.OrderCanceledErrorTitle,
                        text: me.snippets.OrderCanceledErrorText,
                        width: 440,
                        log: false
                    });
                    return;
                }
                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.Order',
                    action: 'detail',
                    params: {
                        orderId: record.get('orderId')
                    }
                });
            }
        });

        return items;
    },

    /**
     * @param { String } value
     * @param { Object } style
     * @param { Object } row
     * @returns { String }
     */
    orderNumberRenderer: function (value, style, row) {
        if (value === Ext.undefined) {
            return value;
        }
        if (!row.data.orderId) {
            return '(no order)';
        }
        if (!row.data.orderId || row.data.orderStatus < 0) {
            return value + ' (canceled)';
        }
        return value;
    },

    /**
     * @param { String } value
     * @param { Object } style
     * @param { Object } row
     * @returns { String }
     */
    paymentMethodRenderer: function (value, style, row) {
        if (row.data.orderPaymentMethod) {
            return row.data.orderPaymentMethod;
        }
        if (value) {
            return value;
        }
        return '';
    },

    /**
     * @param { String } value
     * @returns { String }
     */
    dateColumnRenderer: function (value) {
        if (value === Ext.undefined) {
            return value;
        }

        return Ext.util.Format.date(value, 'm.d.Y H:i');
    }
});