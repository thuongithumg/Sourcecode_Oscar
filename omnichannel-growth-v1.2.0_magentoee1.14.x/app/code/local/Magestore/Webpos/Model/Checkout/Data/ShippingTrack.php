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

class Magestore_Webpos_Model_Checkout_Data_ShippingTrack extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Checkout_ShippingTrackInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCarrierCode(){
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCarrierCode($code){
        return $this->setData(self::KEY_CODE, $code);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getNumber(){
        return $this->getData(self::KEY_NUMBER);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setNumber($number){
        return $this->setData(self::KEY_NUMBER, $number);
    }
    
        /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTitle(){
        return $this->getData(self::KEY_TITLE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTitle($title){
        return $this->setData(self::KEY_TITLE, $title);
    }
}