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

    public function getListToken($keyword, $baseUrl) {
        return $this->sendCurl('/token/list', 'GET', ['keyword' => $keyword, 'domain' => $baseUrl, 'platform' => 'magento 2']);
    }

    public function getExchange($originCurrency, $currency) {
        $exchangeRate = $this->sendCurl("/token/exchange/".$originCurrency."%3A".$currency, 'GET');

        if ($exchangeRate) {
            return json_decode($exchangeRate)->data;
        }
    }

    public function getWebsiteData () {
        $publicKey = $this->_scopeConfig->getValue('payment/ezdefi_payment/public_key');
        $webSiteData = $this->sendCurl('/website/' . $publicKey, 'GET');
        return json_decode($webSiteData)->data;
    }

    public function getCurrency($id) {
        $coins = json_decode(json_encode($this->getWebsiteData()->coins), true);
        $currencyKey = array_search($id, array_column($coins, '_id'));
        return $coins[$currencyKey];
    }

    public function getCurrencies() {
        $coins = json_decode(json_encode($this->getWebsiteData()->coins), true);
        return $coins;
    }

    public function getCurrenciesWithPrice($currencies, $price, $originCurrency) {
        $symbols = '';
        foreach ($currencies as $key => $currency) {
            $symbols .= $symbols === '' ? $currency->token->symbol : ','.$currency->token->symbol;
        }
        $exchangesResponse = $this->sendCurl('/token/exchanges?amount='.$price.'&from='.$originCurrency.'&to='.$symbols, 'GET');

        if($exchangesResponse) {
            $exchangesData = json_decode($exchangesResponse)->data;
            foreach ($exchangesData as $currencyExchange) {
                foreach ($currencies as $key => $currency) {
                    if ($currency->token->symbol == $currencyExchange->token) {
                        $price = $currencyExchange->amount * ((100 - $currency->discount) / 100);
                        $currencies[$key]->token->price = $this->convertExponentialToFloat($price);
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
            $paymentData = json_decode($payment);
            if($paymentData->code == -1) {
                echo $paymentData->error; die;
            } else {
                return json_decode($payment)->data;
            }
        }
    }

    public function getTransaction($transactionId, $explorerUrl) {
        $transactionResponse = $this->sendCurl( '/transaction/get?id=' . $transactionId, 'GET');
        $transactionData = json_decode($transactionResponse)->data;

        return $transactionData;
    }

    public function checkApiKey($apiKey, $apiUrl) {
        $userData = $this->sendCurl('/user/show', "GET", [], $apiKey, $apiUrl);

        $userData = json_decode($userData);

        if($userData && $userData->code == 1 && $userData->message == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    public function checkPublicKey($publicKey, $apiKey, $apiUrl) {
        $websiteData = $this->sendCurl('/website/' . $publicKey, "GET", [], $apiKey, $apiUrl);

        $userData = json_decode($websiteData);

        if($userData && $userData->code == 1 && $userData->message == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    public function convertExponentialToFloat($amount, $decimal = null) {
        if($decimal) {
            $value = sprintf('%.'.$decimal.'f',$amount);
        }
        else {
            $value = sprintf('%.10f',$amount);
        }
        $afterDot = explode('.', $value)[1];
        $lengthToCut = 0;
        for($i = strlen($afterDot) -1; $i >=0; $i--) {
            if($afterDot[$i] === '0') {
                $lengthToCut++;
            } else {
                break;
            }
        }
        $value = substr($value, 0, strlen($value) - $lengthToCut);
        if ($value [strlen($value ) - 1] === '.') {
            $value  = substr($value , 0, -1);
        }
        return $value;
    }

    public function sendCurl($api, $method, $params = [], $apiKey = null, $apiUrl = null) {
        if(!$apiUrl) {
            $apiUrl = $this->_scopeConfig->getValue('payment/ezdefi_payment/gateway_api_url');
        }

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
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYPEER => false,
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
