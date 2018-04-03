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
                pageSize: 10
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
                        pageSizeSelect: [10, 20, 30, 50, 100],
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
                    title: 'Worker',
                    sortable: false, // disable sort for this column
                    width: 320,
                    selector: false,
                    textAlign: 'center',
                },
                {
                    field: 'totalItems',
                    title: 'Total',
                    filterable: false, // disable or enable filtering
                    width: 60,
                },
                {
                    field: 'approvedItems',
                    title: 'Approved',
                    sortable: false,
                    width: 60,
                    selector: false,
                    textAlign: 'center',
                },
                {
                    field: 'declinedItems',
                    title: 'Declined',
                    sortable: false,
                    width: 60,
                    selector: false,
                    textAlign: 'center',
                },
                {
                    field: 'current',
                    title: 'Current',
                    sortable: false,
                    width: 60,
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