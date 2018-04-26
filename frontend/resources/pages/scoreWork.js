const ScoreWorkTable = function() {
    let processed = {};
    let needSort = true;
    let worksToScore = 0;
    let rendered = false;
    const source = $(".js-workers-source");
    const result = $(".js-workers");
    const obj = this;
    const declineModal = $('#delete_score_work_modal');

    this.updateResult = function (data) {
        var _result = {};
        $.each(data, function (key, item) {
            _result[item.id || key] = {
                approvedItems: item.approvedItems,
                declinedItems: item.declinedItems
            };
        });
        result.val(JSON.stringify(_result));
        worksToScore--;
        console.log(worksToScore);
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
        $.each(formatted, function (_, element) {
            if (element.totalItems != (element.approvedItems + element.declinedItems)) {
                worksToScore++;
                return;
            }
            processed[element.id] = true;
        });

        return formatted;
    };

    this.reloadTable = function () {
        rendered = false;
        $('.m_datatable').mDatatable('destroy');
        this.renderTable();
        if (!worksToScore) {
            $('.js-btn-score-work').attr('disabled', false);
        }
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

    this.preview = function (id) {
        var data = JSON.parse(source.val());
        var row = data[id];
        if (!row) {
            return;
        }
        var url = $('.workers-preview-url-' + id).val();
        if (!url) {
            return;
        }

        var modal = $('#preview_modal');

        $.ajax({
            url: url,
            dataType: 'json',
            success: function (data) {
                if (!data.list) {
                    return;
                }
                var body = modal.find('.modal-body');
                body.html('');
                $.each(data.list, function (_, item) {
                    obj.createPreviewElement(item).appendTo(body);
                });
            }
        });
    };

    this.createPreviewElement = function (data) {
        var section = $('<div class="m-section"/>');
        var container = $('<div class="m-section__content"/>');

        var text = $('<strong class="">');
        var label = $('<div class="">');

        var path = '';
        if (data.label) {
            path = data.label.join(' -> ');
        }
        label.html(path);
        text.html(data.text);

        container
            .append(text)
            .append('<br>')
            .append(label)
            .appendTo(section)
        ;
        return section;
    };

    this.renderTable = function () {
        if (rendered) {
            return;
        }
        var datatable = $('.m_datatable').mDatatable({
            data: {
                type: 'local',
                source: this.getFormattedData(),
                pageSize: 50
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
                    field: 'Actions',
                    width: 120,
                    title: 'Current work <i class="flaticon-info" ' +
                    'data-trigger1="focus" ' +
                    'data-toggle="m-popover" ' +
                    'data-placement="top" ' +
                    'data-content="Last completed work that needs to be approved or declined."></i>',
                    sortable: false,
                    overflow: 'visible',
                    template: function (row, index, datatable) {
                        if (processed[row.id]) {
                            return '';
                        }

                        return '\
                        <a href="javascript:void(0);" \
                            class="js-worker-preview m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" \
                            title="Preview"\
                            data-id="' + row.id + '"\
                            data-toggle="modal"\
                            data-target="#preview_modal"\
                        >\
                            <i class="la la-picture-o"></i>\
                        </a>\
						<a href="javascript:void(0);" \
						    class="js-worker-approve m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill"\
						    title="Approve"\
						    data-id="' + row.id + '"\
						    data-field="approvedItems"\
                        >\
							<i class="la la-check-circle-o"></i>\
						</a>\
						<a href="javascript:void(0);" \
						    class="js-worker-decline m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" \
						    title="Decline"\
						    data-id="' + row.id + '"\
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

        $('.js-worker-preview').click(function () {
            obj.preview($(this).data('id'));
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

$(document).ready(function() {
    ScoreWorkTable.init();
});