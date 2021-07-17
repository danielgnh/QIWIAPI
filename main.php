<?php
// preview file. I think you need it only for getting params.

require_once 'Qiwi.php';

$qiwiData = [
    'phone' => '',
    'token' => ''
];

$qiwi = new Qiwi($qiwiData['phone'], $qiwiData['token'], false);

function _debug($data){
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

// Functions with params. Last Updated by Horman (16/07/2021)

$profile = $qiwi->getProfile();
_debug($profile);
$walletIdentification = $qiwi->getIdentification();
_debug($walletIdentification);
$walletLimits = $qiwi->getLimits(['types' => 'REFILL']);
// REFILL - максимальный допустимый остаток на счёте
// TURNOVER - оборот в месяц
// PAYMENTS_P2P - переводы на другие кошельки в месяц
// PAYMENTS_PROVIDER_INTERNATIONALS - платежи в адрес иностранных компаний в месяц
// PAYMENTS_PROVIDER_PAYOUT - Переводы на банковские счета и карты, кошельки других систем
// WITHDRAW_CASH - снятие наличных в месяц. Должен быть указан хотя бы один тип операций.
_debug($walletLimits);
$walletRestrictions = $qiwi->getRestrictions();
_debug($walletRestrictions);
$paymentHistory = $qiwi->getPaymentsHistory([ // https://developer.qiwi.com/ru/qiwi-wallet-personal/?http#payments_list
    'rows' => '2'
]);
_debug($paymentHistory);
$paymentStatistics = $qiwi->getPaymentsStats([
    'startDate' => '2021-05-12T13:20:22+03:00',
    'endDate' => '2021-07-12T13:20:22+03:00'
]);
_debug($paymentStatistics);
$tid = 9429000444; // 'txnId' from 'data' from $paymentHistory
$transactionInfo = $qiwi->getPaymentInfo($tid);
$transactionCheque = $qiwi->getCheque($tid, [ // https://developer.qiwi.com/ru/qiwi-wallet-personal/?http#payment_receipt
    'format' => 'JPG' // JPG / PDF
]);

$walletBalance = $qiwi->getBalance();

echo 'Баланс: '. $walletBalance["accounts"][0]["balance"]["amount"] . '<br>';
$id = '99'; // https://developer.qiwi.com/ru/qiwi-wallet-personal/?http#rates
$tax = $qiwi->getTax($id,[
    'account' => '79151463799', // получатель (номер телефона с международным префиксом, номер карты/счета получателя)
    'paymentMethod' => [
        'type' => 'Account',
        'accountId' => '643'
    ],
    'purchaseTotals' => [
        'total' => [
            'amount' => 100,
            'currency' => '643'
        ]
    ]
]);
_debug(json_decode($tax));

$walletNickName = $qiwi->getMyNickName();

echo "Никнейм: " .$walletNickName['nickname'];
