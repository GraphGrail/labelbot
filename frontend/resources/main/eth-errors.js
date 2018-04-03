function showEthClientError(message) {
    var container = $('.eth-errors');
    container.find('.m-alert__text').html(message);
    container.show();
    return false;
}