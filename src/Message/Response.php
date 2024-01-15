<?php
namespace Omnipay\Epayco\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Epayco Response
 *
 * This is the response class for all Epayco requests.
 *
 * @see \Omnipay\Epayco\Gateway
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    protected $endpoint = 'https://cms.epayco.io/omnipay/checkout/payment';

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        $data = $this->getRedirectQueryParameters();
        return $this->getCheckoutEndpoint().'?'.http_build_query(["session" => $data], '', '&');
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return $this->data ?? null;
    }

    public function getTransactionReference()
    {
        $data = $this->data ?? null;
        $public_key = $data["public_key"];
        $private_key = $data["private_key"];
        $headers = [
            "apikey: ${public_key}",
            "privatekey: ${private_key}",
            'Content-Type: application/json'
        ];
        $data = $this->formatOmniPayPaylod($data);
        $token = $this->makeApifyRequest($headers, $data);
        return $token->success ? $token->data->sessionId : null;
    }

    protected function getRedirectQueryParameters()
    {
        return $this->getTransactionReference();
    }

    public function getTransactionId()
    {
        return $this->data['reference'] ?? null;
    }

    public function getCardReference()
    {
        return $this->data['reference'] ?? null;
    }

    public function getMessage()
    {
        return $this->data['message'] ?? null;
    }

    protected function getCheckoutEndpoint()
    {
        return $this->endpoint;
    }
    
    public function makeApifyRequest($headers,$data)
    {
        $url = "https://apify.epayco.io/checkout/payment/session";
        try {
            $jsonData = json_encode($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$jsonData,
                CURLOPT_HTTPHEADER => $headers,
            ));
            $resp = curl_exec($curl);
            if ($resp === false) {
                return array('curl_error' => curl_error($curl), 'curerrno' => curl_errno($curl));
            }
            curl_close($curl);
            return json_decode($resp);

        }catch (Exception $e) {
            return [
                "success" => false,
                "titleResponse" => "error",
                "textResponse" => $e->getMessage(),
                "data" => []
            ];
        }
    }
    
    public function formatOmniPayPaylod($data)
    {  
        if($data["test"] == '1'){
            $testMode = "true";
        }else{
            $testMode = "false";
        }
        $data_ico = "0";
        $data_hasCvv = "false";
        $formattedData = array(
            "test" => $testMode,
            "extra1" => $data["transactionId"],
            "extra2" => "0",
            "extra5" => 'P49',
            "name" =>$data["description"],
            "description" =>$data["description"],
            "invoice" => strval($data["transactionId"]),
            "currency" => $data["currency"],
            "amount" => strval($data["amount"]),
            "tax_base" => strval($data["subTotal"]),
            "tax" => strval($data["tax"]),
            "taxIco" => $data_ico,
            "country" => $data["country"],
            "lang" => $data["lang"],
            "external" => "true",
            "confirmation" => $data["notifyUrl"],
            "response" => $data["returnurl"],
            "name_billing" => $data["firstName"] ." ". $data["lastName"],
            "address_billing" => $data["address"],
            "email_billing" => $data["email"],
            "autoclick"=>"true",
            "ip" => $this->getIp()
        );
       return $formattedData;
    }
    
    public function getIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
