<?php
namespace Omnipay\Epayco\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Epayco\Gateway;

/**
 * Epayco Authorize/Purchase Request
 *
 * This is the request that will be called for any transaction which submits a credit card,
 * including `authorize` and `purchase`
 */
class CreditCardRequest extends AbstractRequest
{
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPkey()
    {
        return $this->getParameter('pkey');
    }

    public function setPkey($value)
    {
        return $this->setParameter('pkey', $value);
    }

    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
    }

    public function setPublicKey($value)
    {
        return $this->setParameter('publicKey', $value);
    }

    public function getLang()
    {
        return $this->getParameter('lang');
    }

    public function setLang($value)
    {
        return $this->setParameter('lang', $value);
    }

    public function getFirstName()
    {
        return $this->getParameter('firstName');
    }

    public function setFirstName($value)
    {
        return $this->setParameter('firstName', $value);
    }

    public function getLastName()
    {
        return $this->getParameter('lastName');
    }

    public function setLastName($value)
    {
        return $this->setParameter('lastName', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getAddress()
    {
        return $this->getParameter('address');
    }

    public function setAddress($value)
    {
        return $this->setParameter('address', $value);
    }

    public function getSubTotal()
    {
        return $this->getParameter('subTotal');
    }

    public function setSubTotal($value)
    {
        return $this->setParameter('subTotal', $value);
    }

    public function getTax()
    {
        return $this->getParameter('tax');
    }

    public function setTax($value)
    {
        return $this->setParameter('tax', $value);
    }

    public function getCountry()
    {
        return $this->getParameter('country');
    }

    public function setCountry($value)
    {
        return $this->setParameter('country', $value);
    }

    /**
     * Getter: get cart items.
     *
     * @return array
     */
    public function getCart()
    {
        return $this->getParameter('cart');
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setCart($value)
    {
        return $this->setParameter('cart', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'returnUrl', 'notifyUrl');
        $baseData = $this->getBaseData();
        $data['public_key'] = $baseData['publicKey'];
        $data['lang'] = $baseData['lang'];
        $data['amount'] = $this->getAmount();
        $data['subTotal'] = $this->getSubTotal();
        $data['tax'] = $this->getTax();
        $data['currency'] = $this->getCurrency();
        $data['cancelurl'] = $this->getCancelUrl();
        $data['returnurl'] = $this->getReturnUrl();
        $data['notifyUrl'] = $this->getNotifyUrl();
        $data['transactionId'] = $this->getTransactionId();
        $data['description'] = $this->getDescription();
        $data['firstName'] = $this->getFirstName();
        $data['lastName'] = $this->getLastName();
        $data['email'] = $this->getEmail();
        $data['address'] = $this->getAddress();
        $data['test'] = $this->getTestMode();
        $data['cart'] = $this->getCart();
        $data['country'] = $this->getCountry();

        return $data;
    }

    public function sendData($data)
    {
        $data['reference'] = $data['transactionId'] ?? uniqid();
        $data['success'] = true;
        $data['message'] = $data['success'] ? 'Success' : 'Failure';

        return $this->response = new Response($this, $data);
    }

    protected function getBaseData()
    {
        $data = array();
        $data['user'] = $this->getUsername();
        $data['pkey'] = $this->getPkey();
        $data['publicKey'] = $this->getPublicKey();
        $data['lang'] = $this->getLang();

        return $data;
    }
}
