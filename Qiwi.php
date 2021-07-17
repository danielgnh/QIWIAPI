<?php

// https://yougame.biz/threads/215053/

class Qiwi {
    private $_userPhone;
    private $_userToken;
    private $_url;
    private $_headers;
    private $_debug;

    function __construct($phone, $token, $debug) {
        $this->_userPhone = $phone;
        $this->_userToken = $token;
        $this->_url   = 'https://edge.qiwi.com/';
        $this->_headers = [
            'Accept: application/json',
            'Authorization: Bearer '.$this->_userToken,
            'Content-type: application/json',
            'Host: edge.qiwi.com'
        ];
        if(is_bool($debug)){
            $this->_debug = $debug;
        }
        if($this->_debug == true){
            error_reporting(E_ALL);
        }
    }
    private function request($method, array $content = [], $post = false) {
        $curl = curl_init();
        if ($post) {
            curl_setopt($curl, CURLOPT_URL, $this->_url . $method);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));
        } else {
            curl_setopt($curl, CURLOPT_URL, $this->_url . $method . '/?' . http_build_query($content));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if($this->_debug == true){
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, 1);
    }

    public function getProfile() {
        return $this->request('person-profile/v1/profile/current');
    }
    public function getIdentification() {
        return $this->request('/identification/v1/persons/' . $this->_userPhone . '/identification');
    }
    public function getLimits($params = []) {
        return $this->request('/qw-limits/v1/persons/' . $this->_userPhone . '/actual-limits', $params);
    }
    public function getRestrictions(){
        return $this->request('person-profile/v1/persons/' . $this->_userPhone . '/status/restrictions');
    }
    public function getPaymentsHistory($params = []) {
        return $this->request('payment-history/v2/persons/' . $this->_userPhone . '/payments', $params);
    }
    public function getPaymentsStats($params = []) {
        return $this->request('payment-history/v2/persons/' . $this->_userPhone . '/payments/total', $params);
    }
    public function getPaymentInfo($txnId) {
        return $this->request('/payment-history/v2/transactions/'. $txnId);
    }
    public function getCheque($txnId, $params = []) {
	       return $this->request('payment-history/v1/transactions/' . $txnId .'/cheque/file', $params);
    }
    public function getBalance() {
        return $this->request('funding-sources/v2/persons/' . $this->_userPhone . '/accounts');
    }
    public function getTax($id, $data) {
        $data = json_encode($data);
        $url = "https://edge.qiwi.com/sinap/providers/". $id. "/onlineCommission";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Bearer 67e34bf6c3ede65012a72b688163774e",
            "Host: edge.qiwi.com",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //debug
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }
    public function getBills($id,$params = []) {
        return $this->request('/checkout-api/api/bill/search', $params);
    }
    public function getMyNickName() {
        return $this->request('/qw-nicknames/v1/persons/' . $this->_userPhone . '/nickname');
    }
    public function sendIdentification($params = []) {
        return $this->request('/identification/v1/persons/' . $this->_phone . '/identification', $params);
    }
    public function sendMoneyToQiwi($params = []) {
        return $this->request('sinap/api/v2/terms/99/payments', $params);
    }
    public function sendConverted($params = []) {
        return $this->request('/sinap/api/v2/terms/1099/payments', $params);
    }
    public function sendMoneyToProvider($providerId, Array $params = []) {
        return $this->request('sinap/api/v2/terms/'. $providerId .'/payments', $params, 1);
    }
    public function sendMoneyToOther($params = []) {
        return $this->request('sinap/api/v2/terms/1717/payments', $params);
    }
    public function sendBillPayment($params = []) {
        return $this->request('/checkout-api/invoice/pay/wallet', $params);
    }
}
