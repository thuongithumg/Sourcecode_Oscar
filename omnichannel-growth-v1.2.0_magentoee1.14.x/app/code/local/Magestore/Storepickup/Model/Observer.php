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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Observer
 */
class Magestore_Storepickup_Model_Observer
{

    public function sales_order_create_cancel($observer){
        Mage::getSingleton('adminhtml/session')->unsetData('storepickup_shipping_price_flag');

    }

    /*
     *
     * Use for Magestore One Step Checkout module
     */
    /**
     * @param $observer
     */
    public function onestepcheckout_index_save_shipping($observer)
    {

        $action = $observer->getEvent()->getControllerAction();
        $shippingMethod = $action->getRequest()->getParam('shipping_method');
        $data = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        if ($shippingMethod == 'storepickup_storepickup') {
            $data['is_storepickup'] = 1;
            Mage::getSingleton('checkout/session')->setData('storepickup_session', $data);
        } else {
            Mage::getSingleton('checkout/session')->unsetData('storepickup_session');
        }
    }

    /**
     * @param $observer
     */
    public function update_shippingaddress($observer)
    {

        $datashipping = array();
        $storeShipping = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        $storeShipping = isset($storeShipping['store_id'])?$storeShipping['store_id']:null;
        $shippingMethod = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
        if (isset($storeShipping) && $storeShipping && ($shippingMethod == 'storepickup_storepickup')) {
            $store = Mage::getModel('storepickup/store')->load($storeShipping);
            $datashipping['firstname'] = Mage::helper('storepickup')->__('Store');
            $datashipping['lastname'] = $store->getData('store_name');
            $datashipping['street'][0] = $store->getData('address');
            $datashipping['city'] = $store->getCity();
            $datashipping['region'] = $store->getState();
            $datashipping['region_id'] = $store->getData('state_id');
            $datashipping['postcode'] = $store->getData('zipcode');
            $datashipping['country_id'] = $store->getData('country');
            $datashipping['company'] = '';
            if ($store->getStoreFax()) {
                $datashipping['fax'] = $store->getStoreFax();
            } else {
                unset($datashipping['fax']);
            }

            if ($store->getStorePhone()) {
                $datashipping['telephone'] = $store->getStorePhone();
            } else {
                unset($datashipping['telephone']);
            }

            $datashipping['save_in_address_book'] = 1;
        }
        try {
            $result = $this->saveShipping($datashipping, null);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $observer
     */
    public function unset_session_storepickup_shipping_price($observer)
    {
        Mage::getSingleton('checkout/session')->unsetData('storepickup_shipping_price');
    }

    /**
     * @param $data
     * @return array
     */
    public function saveShipping($data){
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('storepickup')->__('Invalid data.'));
        }
        $quote = $this->getQuote();
        $address = $quote->getShippingAddress();
        if (isset($data['address_id'])) {
            unset($data['address_id']);
        }
        $address->addData($data);
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }
        if ($quote->getId()) {
            if ($address->getAddressType() == 'shipping') {
                $storepickup_shipping_price = Mage::getSingleton('checkout/session')->getData('storepickup_shipping_price');
                if (isset($storepickup_shipping_price)) {
                    $price = Mage::getSingleton('checkout/session')->getData('storepickup_shipping_price');
                } else {
                    $price = 0;
                }
                $rates = $address->collectShippingRates()
                                 ->getGroupedAllShippingRates();
                foreach ($rates as $carrier) {
                    foreach ($carrier as $rate) {
                        if ($rate->getCode() == 'storepickup_storepickup') {
                            $rate->setPrice($price);
                            $rate->save();
                        }
                    }
                }
                $this->collectTotals($quote, $price);
            }
            $quote->collectTotals()->save();
        }
        return array();
    }

    /**
     * @param $quote
     * @param $price
     */
    public function collectTotals($quote, $price)
    {
        $quoteId = $quote->getId();
        $shippingcode = 'storepickup_storepickup';
        $session = Mage::getSingleton('checkout/session');
        if ($quoteId) {
            try {
                if ($quote->getCouponCode() && !Mage::helper('giftvoucher')->getGeneralConfig('use_with_coupon')
                    && ($session->getUseGiftCreditAmount() > 0 || $session->getGiftVoucherDiscount() > 0)
                ) {
                    if ($session->getUseGiftCard()) {
                        $session->setUseGiftCard(null)
                            ->setGiftCodes(null)
                            ->setBaseAmountUsed(null)
                            ->setBaseGiftVoucherDiscount(null)
                            ->setGiftVoucherDiscount(null)
                            ->setCodesBaseDiscount(null)
                            ->setCodesDiscount(null)
                            ->setGiftMaxUseAmount(null);
                    }
                    if ($session->getUseGiftCardCredit()) {
                        $session->setUseGiftCardCredit(null)
                            ->setMaxCreditUsed(null)
                            ->setBaseUseGiftCreditAmount(null)
                            ->setUseGiftCreditAmount(null);
                    }

                    $session->setMessageApplyGiftcardWithCouponCode(true);
                }

                if ($codes = $session->getGiftCodes()) {
                    $codesArray = array_unique(explode(',', $codes));
                    foreach ($codesArray as $key => $value) {
                        $codesArray[$key] = 0;
                    }
                    $session->setBaseAmountUsed(implode(',', $codesArray));
                } else {
                    $session->setBaseAmountUsed(null);
                }
                $session->setBaseGiftVoucherDiscount(0);
                $session->setGiftVoucherDiscount(0);
                $session->setUseGiftCreditAmount(0);

                foreach ($quote->getAllAddresses() as $address) {

                    $address->setGiftcardCreditAmount(0);
                    $address->setBaseUseGiftCreditAmount(0);
                    $address->setUseGiftCreditAmount(0);

                    $address->setBaseGiftVoucherDiscount(0);
                    $address->setGiftVoucherDiscount(0);

                    $address->setGiftvoucherBaseHiddenTaxAmount(0);
                    $address->setGiftvoucherHiddenTaxAmount(0);

                    $address->setMagestoreBaseDiscount(0);
                    $address->setMagestoreBaseDiscountForShipping(0);

                    foreach ($address->getAllItems() as $item) {
                        if ($item->getParentItemId()) {
                            continue;
                        }
                        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                            foreach ($item->getChildren() as $child) {
                                $child->setBaseGiftVoucherDiscount(0)
                                    ->setGiftVoucherDiscount(0)
                                    ->setBaseUseGiftCreditAmount(0)
                                    ->setMagestoreBaseDiscount(0)
                                    ->setUseGiftCreditAmount(0);
                            }
                        } elseif ($item->getProduct()) {
                            $item->setBaseGiftVoucherDiscount(0)
                                ->setGiftVoucherDiscount(0)
                                ->setBaseUseGiftCreditAmount(0)
                                ->setMagestoreBaseDiscount(0)
                                ->setUseGiftCreditAmount(0);
                        }
                    }
                }

                $quote->setSubtotal(0);
                $quote->setBaseSubtotal(0);
                $quote->setSubtotalWithDiscount(0);
                $quote->setBaseSubtotalWithDiscount(0);
                $quote->setGrandTotal(0);
                $quote->setBaseGrandTotal(0);

                $quote->getShippingAddress()->setShippingMethod($shippingcode) /* ->collectTotals() */->save();
                $quote->save();
                foreach ($quote->getAllAddresses() as $address) {
                    $address->setSubtotal(0);
                    $address->setBaseSubtotal(0);

                    $address->setGrandTotal(0);
                    $address->setBaseGrandTotal(0);

                    $address->collectTotals();

                    $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
                    $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

                    $quote->setSubtotalWithDiscount(
                        (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
                    );
                    $quote->setBaseSubtotalWithDiscount(
                        (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
                    );

                    $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
                    $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

                    $address->setShippingAmount($price);
                    $address->setBaseShippingAmount($price);
                    $address->save();
                }

                if ($code = trim(Mage::app()->getRequest()->getParam('coupon_code'))) {
                    if ($code != $quote->getCouponCode()) {
                        $codes = Mage::getSingleton('giftvoucher/session')->getCodes();
                        $codes[] = $code;
                        $codes = array_unique($codes);
                        Mage::getSingleton('giftvoucher/session')->setCodes($codes);
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * @param $observer
     * @return $this|void
     */
    public function checkout_type_onepage_save_order_after($observer)
    {
        if (Mage::registry('UPDATE_STORE_SAVE')) {
            return $this;
        }

        Mage::register('UPDATE_STORE_SAVE', true);
        $data = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        if (isset($data['store_id']) && $data['store_id']) {
            $this->update_shippingaddress($observer);
        }
        $order = $observer['order'];
        $this->save_storeaddress($order);
        $data = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        if (isset($data['store_id']) && $data['store_id']) {
//            $order->setStatus("store_pickup");
            $order->addStatusToHistory('store_pickup', 'Order Store Pickup', false);
            $this->send_mail($order);
        }
        return $this;
    }

    /**
     * @param $observer
     */
    public function adminhtml_sales_order_save_before($observer)
    {
        $order = $observer->getOrder();
        $order_id = $order->getId();
        $shippingMethod = $order->getShippingMethod();
        $shippingMethod = explode('_', $shippingMethod);

        $shippingCode = $shippingMethod[0];
        if ($shippingCode != "storepickup") {
            return;
        }

        $storepickup = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        try {
            $shippingtime['time'] = isset($storepickup['time']) ? $storepickup['time'] : null;
            $shippingtime['date'] = isset($storepickup['date']) ? $storepickup['date'] : null;
            $store_id = isset($storepickup['store_id']) ? $storepickup['store_id'] : null;
            if (!$store_id) {
                return;
            }

            if (isset($storepickup['date'])) {
                $date = substr($shippingtime['date'], 6, 4) . '-' . substr($shippingtime['date'], 0, 2) . '-' . substr($shippingtime['date'], 3, 2);
            } else {
                $date = null;
            }

            $storeorder = Mage::getModel('storepickup/storeorder');
            $storeorder->setData('store_id', $store_id);
            $storeorder->setData('order_id', $order_id);
            $storeorder->setData('shipping_time', $shippingtime['time']);
            $storeorder->setData('shipping_date', $date);
            $storeorder->save();
            $shippingdesct = $order->getShippingDescription();
            if ($shippingtime['time'] != null && $shippingtime['date'] != null) {
                $shippingdesct .= '<br/>' . Mage::helper('storepickup')->__('Pickup Time: %s %s ', $date, $shippingtime['time']);
            }

            //IMAGE
            //$store = Mage::helper('storepickup')->getStorepickupByOrderId($order->getId());
            $store = Mage::getModel('storepickup/store')->load($store_id);
            if ($store) {
                $latitude = $store->getStoreLatitude();
                $longitude = $store->getStoreLongitude();
                if ($latitude != 0 && $longitude != 0) {
                    $shippingdesct .= '<br/><img src="http://maps.google.com/maps/api/staticmap?key='.Mage::getModel("storepickup/shipping_storepickup")->getConfigData("gkey").'&center=' . $latitude . ',' . $longitude . '&zoom=14&size=200x200&markers=color:red|label:S|' . $latitude . ',' . $longitude . '&sensor=false" /><br/>';
                }
            }
            $order->setShippingDescription($shippingdesct)
                  ->save()
            ;
            $order->sendNewOrderEmail();
        } catch (Exception $e) {

        }
        Mage::getSingleton('checkout/session')->unsetData('storepickup_session');
    }

    public function inventorysuccess_new_order_warehouse($observer){
        $storepickup = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        try {
            $shippingtime['time'] = isset($storepickup['time']) ? $storepickup['time'] : null;
            $shippingtime['date'] = isset($storepickup['date']) ? $storepickup['date'] : null;
            $store_id = isset($storepickup['store_id']) ? $storepickup['store_id'] : null;
            if (!$store_id) {
                return;
            }
            $store = Mage::getModel('storepickup/store')->load($store_id);
            if($store->getId()){
                $observer->getWarehouse()->setWarehouseId($store->getWarehouseId());
            }
        } catch (Exception $e) {

        }
    }

    /**
     * @param $order
     */
    public function save_storeaddress($order)
    {
        $quote = $this->getQuote();
        $order_id = $order->getId();
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $shippingMethod = explode('_', $shippingMethod);
        $shippingCode = $shippingMethod[0];
        if ($shippingCode != "storepickup") {
            Mage::getSingleton('checkout/session')->unsetData('storepickup_session');
            return;
        }

        $storepickup = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        try {
            $shippingtime['time'] = isset($storepickup['time']) ? $storepickup['time'] : null;
            $shippingtime['date'] = isset($storepickup['date']) ? $storepickup['date'] : null;
            $store_id = isset($storepickup['store_id']) ? $storepickup['store_id'] : null;
            if (!$store_id) {
                return;
            }

            $formatDateLocale = str_replace('%', '', Mage::helper('storepickup')->getDateFormat());
            $formatDateDatabase = 'Y-m-d';

            if (isset($storepickup['date'])) {
                // $date = substr($shippingtime['date'], 6, 4) . '-' . substr($shippingtime['date'], 0, 2) . '-' . substr($shippingtime['date'], 3, 2);
                if (is_object(date_create_from_format($formatDateLocale, $storepickup['date']))) {
                    $date = date_format(date_create_from_format($formatDateLocale, $storepickup['date']), $formatDateDatabase);
                }
            } else {
                $date = null;
            }

            $storeorder = Mage::getModel('storepickup/storeorder');
            $storeorder->setData('store_id', $store_id);
            $storeorder->setData('order_id', $order_id);
            $storeorder->setData('shipping_time', $shippingtime['time']);
            $storeorder->setData('shipping_date', $date);
            $storeorder->save();
            $shippingdesct = $order->getShippingDescription();
            $store = Mage::getModel('storepickup/store')->load($store_id);
            if ($store) {
                $latitude = $store->getStoreLatitude();
                $longitude = $store->getStoreLongitude();
                if ($latitude != 0 && $longitude != 0) {
                    $shippingdesct .= '<br/><img src="http://maps.google.com/maps/api/staticmap?key='.Mage::getModel("storepickup/shipping_storepickup")->getConfigData("gkey").'&center=' . $latitude . ',' . $longitude . '&zoom=14&size=200x200&markers=color:red|label:S|' . $latitude . ',' . $longitude . '&sensor=false" /><br/>';
                }
            }

            $order->setShippingDescription($shippingdesct)
                  ->save();
        } catch (Exception $e) {

        }
//        Mage::getSingleton('checkout/session')->unsetData('storepickup_session');
    }

    /**
     * @param $observer
     */
    public function storepickup_sales_convert_order_to_quote($observer)
    {
//        $order = $observer->getOrder();
        //$quote = $observer->getQuote();
//        $storeorder = Mage::helper('storepickup')->getStorepickupByOrderId($order->getId());
//        if ($order->getShippingMethod() == 'storepickup_storepickup') {
//            Mage::getSingleton('adminhtml/session')->setStorepickupStore($storeorder->getId());
//        }

    }

    /**
     * @return mixed
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * @param $order
     * @return $this
     */
    public function send_mail($order)
    {

        /* Edit by Tien*/
        // Send email to store owner when pickup order is created
        Mage::helper('storepickup/email')->sendNoticeEmailToStoreOwner($order);
        // Send email to admin when pickup order is created
        Mage::helper('storepickup/email')->sendNoticeEmailToAdmin($order);
        /* End by Tien*/
        return $this;
    }

    /**
     * @param $observer
     * @return $this
     */
    public function saleOrderAfter($observer)
    {
		Mage::getSingleton('adminhtml/session')->unsetData('storepickup_shipping_price_flag');
        $order = $observer->getOrder(); //->getData();
        /* Edit by Tien*/
        if (Mage::helper('storepickup')->checkEmailOwner($order->getId()) == 0) {
            Mage::helper('storepickup/email')->sendStautsEmailToStoreOwner($order);
        }
        /* End by Tien*/
        return $this;
    }
    /*
     *
     * Use for Gomage Light Checkout module
     */
    /**
     * @param $observer
     */
    public function gomage_checkout_save_shipping($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $shippingMethod = $action->getRequest()->getParam('shipping_method');
        $data = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        if ($shippingMethod == 'storepickup_storepickup') {
            $data['is_storepickup'] = 1;
            Mage::getSingleton('checkout/session')->setData('storepickup_session', $data);
        } else {
            Mage::getSingleton('checkout/session')->unsetData('storepickup_session');
        }

    }

    /**
     * @param $observer
     */
    public function inventorySuccess_warehouse_save_after(Varien_Event_Observer $observer){
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
        $warehouse = $observer->getEvent()->getDataObject();
        if($warehouse->getId()) {
            /** @var Magestore_Storepickup_Model_Store $store */
            $store = Mage::getModel('storepickup/store');
            $storeCollection = $store->getCollection()
                ->addFieldToFilter('warehouse_id',$warehouse->getId());
            if(!$storeCollection->getSize()){
                $store->convertWarehouseToStore($warehouse)->save();
            }
        }
    }
    /*
     *
     * Use for Gomage Light Checkout module
     */
    /**
     * @param $observer
     * @return array
     */
    public function update_shippingaddress_gomage($observer)
    {

        $datashipping = Mage::getSingleton('checkout/session')->getData('storepickup_session');

        if (isset($datashipping['store_id']) && $datashipping['store_id']) {
            $store = Mage::getModel('storepickup/store')->load($datashipping['store_id']);
            $datashipping['firstname'] = Mage::helper('storepickup')->__('Store');
            $datashipping['lastname'] = $store->getData('store_name');
            $datashipping['street'][0] = $store->getData('address');
            $datashipping['city'] = $store->getCity();
            $datashipping['region'] = $store->getState();
            $datashipping['region_id'] = $store->getData('state_id');
            $datashipping['postcode'] = $store->getData('zipcode');
            $datashipping['country_id'] = $store->getData('country');

            $datashipping['company'] = '';
            if ($store->getStoreFax()) {
                $datashipping['fax'] = $store->getStoreFax();
            } else {
                unset($datashipping['fax']);
            }

            if ($store->getStorePhone()) {
                $datashipping['telephone'] = $store->getStorePhone();
            } else {
                unset($datashipping['telephone']);
            }

            $datashipping['save_in_address_book'] = 1;
        }

        try {

            if (empty($datashipping)) {
                return array('error' => -1, 'message' => Mage::helper('storepickup')->__('Invalid data.'));
            }

            unset($datashipping['address_id']);
            Mage::getSingleton('checkout/session')->setShippingAsBilling(false);
            Mage::getSingleton('gomage_checkout/type_onestep')->saveShipping($datashipping, false);
        } catch (Exception $e) {

        }
        return array();
    }

}
