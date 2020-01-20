<?php
namespace Ezdefi\Payment\Helper;

class GatewayHelper
{
    protected $_scopeConfig;

    const PENDING = 'pending';
    const DONE = 'processing';

    const DEFAULT_DECIMAL_LIST_COIN = 12;

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

    public function getCurrenciesWithPrice($currencies, $price, $originCurrency) {
        $symbols = '';
        foreach ($currencies as $currency) {
            $symbols .= $symbols === '' ? $currency['symbol'] : ','.$currency['symbol'];
        }
        $exchanges_response = $this->sendCurl('/token/exchanges?amount='.$price.'&from='.$originCurrency.'&to='.$symbols, 'GET');

        if($exchanges_response) {
            $exchanges_data = json_decode($exchanges_response)->data;
            foreach ($exchanges_data as $currency_exchange) {
                foreach ($currencies as $key => $currency) {
                    if ($currency['symbol'] == $currency_exchange->token) {
                        $currencies[$key]['price'] = round($currency_exchange->amount * ((100 - $currency['discount']) / 100), self::DEFAULT_DECIMAL_LIST_COIN);
                    }
                }
            }
        }
        return $currencies;
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

    public function getTransaction($transactionId, $explorerUrl) {
        $transactionResponse = $this->sendCurl( '/transaction/get?id=' . $transactionId, 'GET');
        $transactionData = json_decode($transactionResponse)->data;

        return $transactionData;
    }

    public function checkApiKey($apiKey) {
        $userData = $this->sendCurl('/user/show', "GET", [], $apiKey);

        $userData = json_decode($userData);

        if($userData && $userData->code == 1 && $userData->message == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    public function sendCurl($api, $method, $params = [], $apiKey = null) {
        $apiUrl = $this->_scopeConfig->getValue('payment/ezdefi_payment/gateway_api_url');
        if(!$apiKey) {
            $apiKey = $this->_scopeConfig->getValue('payment/ezdefi_payment/api_key');
        }

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
