
const errorText = {
    'ALREADY_INITIALIZED': 'Oops! Ethereum client not logged in. Log in and reload page',
    'NO_ETHEREUM_CLIENT': 'Oops! Ethereum client was not found. Install one, such as <a href=\"https://metamask.io/\" ' +
        'target=\"_blank\">Metamask</a> and reload page',
    'NO_ACCOUNTS': 'Oops! Ethereum client not logged in. Log in and reload page',
    'WRONG_NETWORK': 'Oops! Ethereum client select wrong network. Change it to \"Rinkeby Test Network\" and reload page',
    'NOT_INITIALIZED': 'Ethereum client was not initialized. Please reload page.',
    'INSUFFICIENT_TOKEN_BALANCE': 'Oops! Not enough tokens.',
    'NOT_INITIALIZED': 'Oops! Ethereum client was not initialized. Please reload page.',
    'TRANSACTION_ALREADY_RUNNING': 'Oops! Transaction already running. Reload page.',
    'CONTRACT_NOT_FOUND': 'Oops! Contract not found.',
    'INSUFFICIENT_ETHER_BALANCE': 'Oops! Not enough ether.',
    'INVALID_CONTRACT_STATE': 'Oops! Invalid contract state.',
    'UNAUTHORIZED': 'Oops! Unauthorized. Check permissions.',
    'TRANSACTION_FAILED': 'Oops! Transaction failed.',
};

const tokenContractAddress = $('.js-token-contract-address').text(); //'0x436e362ac2c1d5f88986b7553395746446922be2';
const expectedNetworkId = $('.js-eth-network-id').text(); //'1337';

let clientAddress;
let clientBalances;
const bigNum = graphGrailEther.BigNumber;
const contractAddress = $('.js-contract-address').val();
const tokensValue = $('.js-tokens-value').val();


// Wallet initialization
graphGrailEther.init(tokenContractAddress, expectedNetworkId)
    .catch(err => {
        console.log(err.code + ': ' + err);
        switch(err.code) {
            case 'ALREADY_INITIALIZED':
                return graphGrailEther.getClientAddress();
            case 'NO_ACCOUNTS':
            case 'NO_ETHEREUM_CLIENT':
            case 'WRONG_NETWORK':
                return showEthClientError(errorText[err.code]);
            default:
                return showEthClientError(err);
        }
    })
    .then(address => {
        if (!address) return;
        clientAddress = address;

        $('.js-address').val(address);
        return graphGrailEther.checkBalances(address);
    })
    .then(balances => {
        if (!balances) return;
        clientBalances = balances;

        showWallet();

        if (lowBalances()) {
            $('.js-credit-invitation').show();
            showEthCreditAlert();
        }

        $('html').trigger('wallet_ready');
    })
    .catch(err => {
        console.log(err.code + ': ' + err);
        switch(err.code) {
            case 'NOT_INITIALIZED':
                return showEthClientError(errorText[err.code]);
            default:
                return showEthClientError(err);
        }
    });


$('html').on('wallet_ready', () => {
    $('.js-btn-create').attr('disabled', false);
    $('.js-btn-transfer').attr('disabled', false);
    $('.js-btn-activate').attr('disabled', false);
    $('.js-btn-score-work').attr('disabled', false);
});


function handleEthError(err) {
    console.log(err.code + ' ' + err);
    switch(err.code) {
        case 'INSUFFICIENT_TOKEN_BALANCE':
            showEthCreditAlert(null, clientAddress);
            return showEthClientError('Oops! Not enough tokens');
        case 'NOT_INITIALIZED':
            return  showEthClientError('Oops! Ethereum client was not initialized. Please reload page');
        case 'TRANSACTION_ALREADY_RUNNING':
            return showEthClientError('Oops! Transaction already running. Reload page');
        case 'CONTRACT_NOT_FOUND':
            return showEthClientError('Oops! Contract not found');
        case 'INSUFFICIENT_ETHER_BALANCE':
            showEthCreditAlert(null, clientAddress);
            return showEthClientError('Oops! Not enough ether');
        case 'INVALID_CONTRACT_STATE':
            return showEthClientError('Oops! Invalid contract state');
        case 'UNAUTHORIZED':
            return showEthClientError('Oops! Unauthorized. Check permissions');
        case 'TRANSACTION_FAILED':
            return showEthClientError('Oops! Transaction failed');
        default:
            return showEthClientError(err);
    }
}


$('.js-btn-create').click(_ => {
    $('.js-btn-create').addClass('m-loader m-loader--right');
});


$('.js-btn-transfer').on('click', e => {
    e.preventDefault();
    $('.js-btn-transfer').attr('disabled', true).addClass('m-loader m-loader--right');

    graphGrailEther.activeTransactionFinishedPromise()
        .then(_ => {
            notifyCheckEthClient();
            return graphGrailEther.transferTokensTo(contractAddress, tokensValue)
        })
        .catch(err => {
            handleEthError(err);
        })
        .then(_ => {
            if (_ === false) return;
            $('.js-form').submit();
        })
});


$('.js-btn-activate').on('click', e => {
    e.preventDefault();
    $('.js-btn-activate').attr('disabled', true).addClass('m-loader m-loader--right');

    graphGrailEther.activeTransactionFinishedPromise()
        .then(_ => {
            notifyCheckEthClient();
            return graphGrailEther.activateContract(contractAddress)
        })
        .catch(err => {
            handleEthError(err);
        })
        .then(_ => {
            if(_ === false) {
                return;
            }
            $('.js-form').submit();
        })
});


$('.js-btn-score-work').on('click', e => {
    e.preventDefault();

    if (!$('.js-workers').val()) {
        alert('Score work to send results to blockchain');
        return false;
    }

    $('.js-btn-score-work').attr('disabled', true);

    let workers = JSON.parse($('.js-workers').val());

    console.log(workers);

    graphGrailEther.activeTransactionFinishedPromise()
        .then(_ => {
            notifyCheckEthClient();
            return graphGrailEther.scoreWork(contractAddress, workers);
        })
        .catch(err => {
            handleEthError(err);
        })
        .then(_ => {
            $('.js-form').submit();
        });
});


$('.finalize-task-btn').on('click', e => {
    e.preventDefault();

    const taskId = $(this).data('id');
    const contractAddress = $(this).data('contract-address');
    if (!contractAddress) return;
    if (!clientAddress) return;

    $(this).attr('disabled', true).addClass('m-loader m-loader--right');

    graphGrailEther.activeTransactionFinishedPromise()
        .then(_ => {
            notifyCheckEthClient();
            return graphGrailEther.finalizeContract(contractAddress)
        })
        .catch(err => {
            handleEthError(err);
        })
        .then(_ => {
            if (_ === false) return;
            syncStatus(taskId);
            setTimeout(() => {window.location.reload()}, 3000);
        })
});


$('.js-get-credit').on('click', e => {
    $('.js-get-credit').attr('disabled', true);
    $.get('/task/get-credit/' + clientAddress)
        .done(function( data ) {
            //console.log(data);
            if (data.error) {
                $('.js-credit-text').text(data.error_text);
                return;
            }
            $('.js-credit-invitation').hide();
            $('.js-credit-waiting').show();
            let timerId = setTimeout(function tick() {
                graphGrailEther.checkBalances(clientAddress)
                    .then(balance => {
                        //console.log(balance)
                        if (balance.ether > 0 && balance.token > 0) {
                            window.location.reload()
                        }
                    });
                timerId = setTimeout(tick, 5000);
            }, 5000);
        })
        .fail(function() {
            $('.js-get-credit').attr('disabled', false);
        })

});



function lowBalances() {
    return clientBalances.ether == 0 || clientBalances.token == 0;
}


function showWallet() {
    console.log('User wallet address: ' + clientAddress);
    console.log('Ether: ' + clientBalances.ether + ', tokens: ' + clientBalances.token);

    $('.js-user-addr').text(clientAddress);
    $('.js-user-ether').text(new bigNum(clientBalances.ether).dividedBy('1e18').toFormat(6));
    $('.js-user-token').text(new bigNum(clientBalances.token).dividedBy('1e18').toFormat(6));
    $('.js-user-wallet').removeClass('m--hide');
}


function syncStatus(taskId) {
    $.post('/task/' + taskId + '/sync-status', (response) => {
        console.log(response);
    })
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


function showEthClientError(message) {
    const container = $('.eth-errors');
    container.find('.m-alert__text').html(message);
    container.show();
    return false;
}


function notifyCheckEthClient() {
    swal({
        position: 'top-right',
        title: '<span class="text-success"><i class="fa fa-check"></i> Check your Ethereum client</span>',
        showConfirmButton: false,
        timer: 2500,
    });
}


/*    function initWallet() {
        graphGrailEther.init(tokenContractAddress, expectedNetworkId)
            .catch(err => null);
        return !graphGrailEther.isInitialized() ?
            new Promise(resolve => setTimeout(initWallet, 50)) :
            showWallet(); */


