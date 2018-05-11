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

class Magestore_Webpos_Model_Api2_Response extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_ResponseInterface
{

    /**
     * @return string
     */
    public function getStatus(){
        return $this->getData(self::STATUS);
    }
    /**
     * @param string $status
     * @return string
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return array
     */
    public function getMessages(){
        return $this->getData(self::MESSAGES);
    }

    /**
     * @param array $message
     * @return array
     */
    public function setMessages($message){
        return $this->setData(self::MESSAGES, $message);
    }

    /**
     * @return array
     */
    public function getResponseData(){
        return $this->getData(self::DATA);
    }

    /**
     * @param array $data
     * @return array
     */
    public function setResponseData($data){
        return $this->setData(self::DATA, $data);
    }
}
