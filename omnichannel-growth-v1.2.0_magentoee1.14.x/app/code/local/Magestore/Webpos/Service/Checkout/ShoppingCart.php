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

class Magestore_Webpos_Service_Checkout_ShoppingCart extends Magestore_Webpos_Service_Checkout_Checkout
{
    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @return mixed
     */
    public function getItems($quoteData){
        $data = array();
        $message = array();
        if(!empty($quoteData)){
            $orderCreateModel = $this->_startAction($quoteData);
            $customerId = $orderCreateModel->getQuote()->getCustomerId();
            if($customerId){
                $cart = $orderCreateModel->getCustomerCart();
                if ($cart->getId()) {
                    $data['cart_items'] = $this->_getQuoteItemsData($cart);
                }
            }else{
                $message[] = $this->__('Customer account is invalid');
            }
            $this->_finishAction();
        }
        $status = (empty($message))?Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS:Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Magestore_Webpos_Api_Cart_QuoteDataInitInterface $quoteData
     * @param string[] $removeIds
     * @param string[] $moveIds
     * @return mixed
     */
    public function updateItems($quoteData, $removeIds = array(), $moveIds = array())
    {
        $data = array();
        $message = array();
        if(!empty($quoteData)){
            $cartItems = array();
            $orderCreateModel = $this->_startAction($quoteData);
            $customerId = $orderCreateModel->getQuote()->getCustomerId();
            if($customerId){
                $cart = $orderCreateModel->getCustomerCart();
                if ($cart->getId()) {
                    if(!empty($removeIds)){
                        try{
                            foreach ($removeIds as $itemId){
                                $orderCreateModel->removeItem($itemId, 'cart');
                            }
                        }catch (Exception $e){
                            $message[] = $e->getMessage();
                        }
                    }
                    if(!empty($moveIds)){
                        try{
                            foreach ($moveIds as $itemId){
                                $item = $cart->getItemById($itemId);
                                if ($item) {
                                    $orderCreateModel->moveQuoteItem($item, 'order', $item->getQty());
                                    //$orderCreateModel->removeItem($itemId, 'cart');
                                }
                            }
                        }catch (Exception $e){
                            $message[] = $e->getMessage();
                        }
                        $eventData = array(
                            'quote' => $this->getQuote()
                        );
                        $this->_dispatchEvent(Magestore_Webpos_Api_CheckoutInterface::EVENT_WEBPOS_SAVE_CART_AFTER, $eventData);
                    }
                    $cartItems = $this->_getQuoteItemsData($cart);
                }
            }
            $this->_finishAction();
            if(!empty($moveIds)) {
                $data = $this->_getQuoteData(array(), $orderCreateModel);
            }
            if(isset($cart) && $cart->getId()){
                $data['cart_items'] = $cartItems;
            }
            $quoteMessage = $this->_getQuoteErrors($orderCreateModel);
            $message = array_merge($message, $quoteMessage);
        }
        $status = (empty($message))?Magestore_Webpos_Api_ResponseInterface::STATUS_SUCCESS:Magestore_Webpos_Api_ResponseInterface::STATUS_ERROR;
        return $this->getResponseData($data, $message, $status);
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    protected function _getQuoteItemsData(Mage_Sales_Model_Quote $quote){
        $itemsData = array();
        $items = $quote->getAllVisibleItems();
        if(count($items)){
            foreach ($items as $item){
                $itemsData[$item->getId()] = $item->getData();
                $itemsData[$item->getId()]['offline_item_id'] =  $item->getBuyRequest()->getData('item_id');
                $itemsData[$item->getId()]['image_url'] =  $this->_getHelper('catalog/image')->init($item->getProduct(), 'thumbnail')->resize('500')->__toString();
                $itemsData[$item->getId()]['minimum_qty'] =  $item->getProduct()->getStockItem()->getMinSaleQty();
                $itemsData[$item->getId()]['maximum_qty'] =  $item->getProduct()->getStockItem()->getMaxSaleQty();
                $itemsData[$item->getId()]['qty_increment'] =  $item->getProduct()->getStockItem()->getQtyIncrements();
                $itemsData[$item->getId()]['info_buy'] =  $this->_getHelper('webpos/order')->getItemInfoBuy($item);
            }
        }
        return $itemsData;
    }
}