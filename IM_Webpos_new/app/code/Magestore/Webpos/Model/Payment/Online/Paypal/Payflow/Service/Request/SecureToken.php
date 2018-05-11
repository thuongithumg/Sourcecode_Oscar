<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Payment\Online\Paypal\Payflow\Service\Request;

use Magento\Framework\Math\Random;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Paypal\Model\Payflow\Transparent;
use Magento\Paypal\Model\Payflowpro;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order\Payment;

/**
 * Class SecureToken
 */
class SecureToken extends \Magento\Paypal\Model\Payflow\Service\Request\SecureToken
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var Transparent
     */
    private $transparent;

    /**
     * @param UrlInterface $url
     * @param Random $mathRandom
     * @param Transparent $transparent
     */
    public function __construct(
        UrlInterface $url,
        Random $mathRandom,
        Transparent $transparent
    ) {

        $this->url = $url;
        $this->mathRandom = $mathRandom;
        $this->transparent = $transparent;
    }

    /**
     * Get the Secure Token from Paypal for TR
     *
     * @param Quote $quote
     *
     * @return DataObject
     * @throws \Exception
     */
    public function requestToken(Quote $quote)
    {
        $request = $this->transparent->buildBasicRequest();

        $request->setTrxtype(Payflowpro::TRXTYPE_AUTH_ONLY);
        $request->setVerbosity('HIGH');
        $request->setAmt(0);
        $request->setCreatesecuretoken('Y');
        $request->setSecuretokenid($this->mathRandom->getUniqueHash());
        $request->setReturnurl($this->url->getUrl('webpos/paypal_payflowpro/response'));
        $request->setErrorurl($this->url->getUrl('webpos/paypal_payflowpro/response'));
        $request->setCancelurl($this->url->getUrl('paypal/transparent/cancel'));
        $request->setDisablereceipt('TRUE');
        $request->setSilenttran('TRUE');

        $this->transparent->fillCustomerContacts($quote, $request);

        $result = $this->transparent->postRequest($request, $this->transparent->getConfig());

        return $result;
    }
}
