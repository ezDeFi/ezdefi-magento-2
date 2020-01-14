<?php
namespace Ezdefi\Payment\Helper;

class GatewayHelper
{
    protected $_scopeConfig;

    const PENDING = 'pending';
    const DONE = 'processing';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }


    public function getExchange($originCurrency, $currency) {
        $exchangeRate = $this->sendCurl("/token/exchange/".$originCurrency."%3A".$currency, 'GET');

        if ($exchangeRate) {
            return json_decode($exchangeRate)->data;
        }
    }

    public function checkPaymentComplete($paymentId) {
        $payment = $this->sendCurl('/payment/get?paymentid='.$paymentId, 'GET');
        if ($payment) {
            $paymentData = json_decode($payment)->data;
            $value = $paymentData->value * pow(10, - $paymentData->decimal);
            if($paymentData->status == "PENDING") {
                return ['status' => "PENDING", 'code' => self::PENDING];
            } elseif ($paymentData->status == "DONE") {
                return [
                    'status' => "DONE",
                    'code' => self::DONE,
                    'uoid'=> $paymentData->uoid,
                    'currency' => $paymentData->currency,
                    'value' => $value,
                    'explorer_url' => $paymentData->explorer->tx . $paymentData->transactionHash,
                    '_id' => $paymentData->_id
                ];
            } elseif ($paymentData->status == 'EXPIRED_DONE') {
                return [
                    'status' => 'EXPIRED_DONE',
                    'uoid'=> $paymentData->uoid,
                    'currency' => $paymentData->currency,
                    'value' => $value,
                    'explorer_url' => $paymentData->explorer->tx . $paymentData->transactionHash,
                    '_id' => $paymentData->_id];
            }
        } else {
            return ['status' => "failure"];
        }
    }

    public function showUser() {
        $this->sendCurl("/user/show", "get");
    }

    public function createPayment($param) {
        $payment = $this->sendCurl('/payment/create', 'POST', $param);
        if($payment) {
            return json_decode($payment)->data;
        }
    }

    public function sendCurl($api, $method, $params = []) {
        $apiUrl = $this->_scopeConfig->getValue('payment/ezdefi_payment/gateway_api_url');
        $apiKey = $this->_scopeConfig->getValue('payment/ezdefi_payment/api_key');

        if(!empty($params)) {
            $url =  $apiUrl.$api.'?'. http_build_query($params);
        } else {
            $url = $apiUrl.$api;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => ['accept: application/xml', 'api-key: '.$apiKey],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        } else {
            return $response;
        }
    }
}
