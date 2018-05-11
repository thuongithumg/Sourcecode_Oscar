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


class Magestore_Webpos_Model_Cart_Data_ItemRequest extends Magestore_Webpos_Model_Abstract implements Magestore_Webpos_Api_Cart_ItemRequestInterface
{
    /**
     * @return string
     */
    public function getId(){
        return $this->getData(self::ID);
    }

    /**
     * @param string $id
     * @return Magestore_Webpos_Api_Cart_ItemRequestInterface
     */
    public function setId($id){
        return $this->setData(self::ID, $id);
    }

    /**
     * @return Magestore_Webpos_Api_Cart_BuyRequestInterface
     */
    public function getBuyRequest(){
        return $this->getData(self::INFO_BUYREQUEST);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_BuyRequestInterface $buyRequest
     * @return Magestore_Webpos_Api_Cart_ItemRequestInterface
     */
    public function setBuyRequest($buyRequest){
        return $this->setData(self::INFO_BUYREQUEST, $buyRequest);
    }

    public function convertData(){
        $buyRequest = $this->getData();
        if(!empty($buyRequest['giftvoucher_options'])){

            foreach ($buyRequest['giftvoucher_options'] as $key => $giftvoucherOption) {
                $buyRequest[$key] = $giftvoucherOption;
            }

            unset($buyRequest['giftvoucher_options']);
        }
        $this->setData($buyRequest);
    }

    public function convertOption($options){
        $newOptions = array();
        if($options){
            foreach ($options as $option) {
                if(!empty($option) && isset($option['value'])){
                    $newOptions[$option['code']] = $option['value'];
                }
            }
            if(!empty($newOptions)){
                $options = $newOptions;
            }
        }
        return $options;
    }
}
