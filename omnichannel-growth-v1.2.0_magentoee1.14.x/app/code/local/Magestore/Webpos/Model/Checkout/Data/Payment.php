<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Webpos_Model_Checkout_Data_Payment
 */
class Magestore_Webpos_Model_Checkout_Data_Payment extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Checkout_PaymentInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getMethod(){
        return $this->getData(self::METHOD);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMethod($method){
        return $this->setData(self::METHOD, $method);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getMethodData(){
        return $this->getData(self::DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMethodData($data){
        return $this->setData(self::DATA, $data);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getAddress(){
        return $this->getData(self::ADDRESS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setAddress($address){
        return $this->setData(self::ADDRESS, $address);
    }
}