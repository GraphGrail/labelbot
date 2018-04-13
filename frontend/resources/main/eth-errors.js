function showEthClientError(message) {
    var container = $('.eth-errors');
    container.find('.m-alert__text').html(message);
    container.show();
    return false;
}

function showEthCreditAlert(taskId, contractAddress) {
    let container = $('.js-credit-invitation');

    if (taskId) {
        let link = container.find('.credit-action a');
        let route = 'task/' + taskId + '/get-credit?address=' + contractAddress;

        link.attr('href', route);
    }
    container.show();
}

function notifyCheckEthClient() {
    swal({
        position: 'top-right',
        title: '<span class="text-success"><i class="fa fa-check"></i> Check your Ethereum client</span>',
        showConfirmButton: false,
        timer: 2500,
    });
}