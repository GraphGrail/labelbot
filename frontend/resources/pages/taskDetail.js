var TaskDetailTable = function() {
    var obj = this;
    var rendered = false;
    var source = $(".js-workers-source");

    this.updateSource = function (data) {
        var _result = {};
        $.each(data, function (key, item) {
            _result[item.id || key] = item;
        });
        source.val(JSON.stringify(_result));
    };

    this.getFormattedData = function () {
        var data = JSON.parse(source.val());
        var formatted = [];

        $.each(data, function (key, item) {
            item.id = key;
            formatted.push(item);
        });

        return formatted;
    };

    this.reloadTable = function () {
        rendered = false;
        $('.m_datatable').mDatatable('destroy');
        this.renderTable();
    };

    this.renderTable = function () {
        if (rendered) {
            return;
        }
        var datatable = $('.m_datatable').mDatatable({
            data: {
                type: 'local',
                source: this.getFormattedData(),
                pageSize: 20
            },

            // layout definition
            layout: {
                scroll: false,
                footer: false
            },

            // column sorting
            sortable: true,

            pagination: true,

            toolbar: {
                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        // page size select
                        pageSizeSelect: [20, 50, 100, 200, 500],
                    },
                },
            },

            search: {
                input: $('#generalSearch'),
            },

            // columns definition
            columns: [
                {
                    field: 'id',
                    title: 'Worker Ethereum address <i class="flaticon-info" ' +
                    'data-trigger1="focus" ' +
                    'data-toggle="m-popover" ' +
                    'data-placement="top" ' +
                    'data-content="Ethereum addresses of worker who worked on the task."></i>',
                    sortable: false, // disable sort for this column
                    width: 350,
                    selector: false,
                    textAlign: 'left',
                },
                {
                    field: 'totalItems',
                    title: 'Total works <i class="flaticon-info" ' +
                    'data-trigger1="focus" ' +
                    'data-toggle="m-popover" ' +
                    'data-placement="top" ' +
                    'data-content="Total number of works made each worker."></i>',
                    // sortable: 'asc', // default sort
                    filterable: false, // disable or enable filtering
                    width: 120,
                    // basic templating support for column rendering,
                    // template: '{{OrderID}} - {{ShipCountry}}',
                    textAlign: 'center',
                }, {
                    field: 'approvedItems',
                    title: 'Approved <i class="flaticon-info" ' +
                    'data-trigger1="focus" ' +
                    'data-toggle="m-popover" ' +
                    'data-placement="top" ' +
                    'data-content="Number of approved works worker made."></i>',
                    sortable: false,
                    width: 100,
                    selector: false,
                    textAlign: 'center',
                },
                {
                    field: 'declinedItems',
                    title: 'Declined <i class="flaticon-info" ' +
                    'data-trigger1="focus" ' +
                    'data-toggle="m-popover" ' +
                    'data-placement="top" ' +
                    'data-content="Number of declined works worker made."></i>',
                    sortable: false,
                    width: 100,
                    selector: false,
                    textAlign: 'center',
                }, {
                    field: 'current',
                    title: 'Current work <i class="flaticon-info" ' +
                    'data-trigger1="focus" ' +
                    'data-toggle="m-popover" ' +
                    'data-placement="top" ' +
                    'data-content="Completeness of the worker\'s current work."></i>',
                    sortable: false,
                    width: 120,
                    selector: false,
                    textAlign: 'center',
                },
            ],
        });

        rendered = true;
    };

    var initTable = function() {
        obj.renderTable();
    };

    return {
        init: function() {
            initTable();
        }
    };
}();

jQuery(document).ready(function() {
    TaskDetailTable.init();
});