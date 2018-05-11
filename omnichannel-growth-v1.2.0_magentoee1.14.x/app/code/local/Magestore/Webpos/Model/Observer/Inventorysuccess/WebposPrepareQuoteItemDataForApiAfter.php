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

class Magestore_Webpos_Model_Observer_Inventorysuccess_WebposPrepareQuoteItemDataForApiAfter
    extends Magestore_Webpos_Model_Observer_Abstract
{

    /**
     * Get warehouse id
     * @param $observer
     */
    public function execute($observer)
    {
        $item = $observer->getData('item');
        $itemDataObject = $observer->getData('itemData');
        if ($this->_helper->isInventorySuccessEnable() && $item) {
            $data = $itemDataObject->getData();
            if (empty($data['warehouse_id']) && $item->getBuyRequest()->getData(Magestore_Webpos_Api_Cart_BuyRequestInterface::QUOTE_ITEM_DATA)) {
                $itemData = $item->getBuyRequest()->getData(Magestore_Webpos_Api_Cart_BuyRequestInterface::QUOTE_ITEM_DATA);
                foreach ($itemData as $data) {
                    if($data[Magestore_Webpos_Api_Checkout_ExtensionDataInterface::FIELD_KEY] == 'ordered_warehouse_id'){
                        $itemDataObject->setData('warehouse_id',$data[Magestore_Webpos_Api_Checkout_ExtensionDataInterface::FIELD_VALUE]);
                    }
                }
            }
        }
    }
}