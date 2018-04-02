var ScoreWorkTable = function() {
    var processed = {};
    var needSort = true;
    var source = $(".js-workers-source");
    var result = $(".js-workers");
    var obj = this;
    var rendered = false;
    var declineModal = $('#delete_score_work_modal');

    this.updateResult = function (data) {
        var _result = {};
        $.each(data, function (key, item) {
            _result[item.id || key] = {
                approvedItems: item.approvedItems,
                declinedItems: item.declinedItems
            };
        });
        result.val(JSON.stringify(_result));
    };

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
        if (needSort) {
            formatted.sort(function (a, b) {
                var isA = a.totalItems - a.approvedItems - a.declinedItems;
                var isB = b.totalItems - b.approvedItems - b.declinedItems;
                return isA < isB;
            });
            needSort = false;

            this.updateSource(formatted);
        }

        return formatted;
    };

    this.reloadTable = function () {
        rendered = false;
        $('.m_datatable').mDatatable('destroy');
        this.renderTable();
    };

    this.updateRow = function (id, type) {
        var data = JSON.parse(source.val());
        var row = data[id];
        if (!row || ! type) {
            return;
        }

        row[type] += 1;
        processed[id] = true;

        this.updateSource(data);
        this.updateResult(data);

        this.reloadTable();
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
                    title: 'ID',
                    sortable: false, // disable sort for this column
                    width: 320,
                    selector: false,
                    textAlign: 'center',
                },
                {
                    field: 'totalItems',
                    title: 'Total',
                    // sortable: 'asc', // default sort
                    filterable: false, // disable or enable filtering
                    width: 60,
                    // basic templating support for column rendering,
                    // template: '{{OrderID}} - {{ShipCountry}}',
                }, {
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
                }, {
                    field: 'Actions',
                    width: 110,
                    title: 'Actions',
                    sortable: false,
                    overflow: 'visible',
                    template: function (row, index, datatable) {
                        if (processed[row.id]) {
                            return '';
                        }

                        return '\
                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Preview">\
                            <i class="la la-picture-o"></i>\
                        </a>\
						<a href="javascript:void(0);" \
						    class="js-worker-approve m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill"\
						    title="Approve"\
						    data-id=' + row.id + '\
						    data-field="approvedItems"\
                        >\
							<i class="la la-check-circle-o"></i>\
						</a>\
						<a href="javascript:void(0);" \
						    class="js-worker-decline m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" \
						    title="Decline"\
						    data-id=' + row.id + '\
						    data-field="declinedItems"\
						    data-toggle="modal" data-target="#delete_score_work_modal"\
                        >\
							<i class="la la-ban"></i>\
						</a>\
					';
                    },
                }],
        });

        $('.js-worker-approve').click(function () {
            obj.updateRow($(this).data('id'), $(this).data('field'));
        });

        $('.js-worker-decline').click(function () {
            var id = $(this).data('id');
            declineModal.find('.confirm-decline-link').data('id', id);
            declineModal.find('.confirm-decline-link').data('field', $(this).data('field'));
        });

        $('.confirm-decline-link').click(function (e) {
            e.preventDefault();
            $(this).addClass('m-loader m-loader--light m-loader--right');
            obj.updateRow($(this).data('id'), $(this).data('field'));

            $(this).removeClass('m-loader m-loader--light m-loader--right');
            $(this).data('id', '');
            $(this).data('field', '');
            declineModal.modal('hide');

        });

        $('.break-decline-link').click(function () {
            $(this).data('id', '');
            $(this).data('field', '');
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
    ScoreWorkTable.init();

});