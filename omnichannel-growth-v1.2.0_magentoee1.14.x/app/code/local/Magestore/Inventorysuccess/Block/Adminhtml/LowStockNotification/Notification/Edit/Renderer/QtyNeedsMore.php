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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Coupon codes grid "Used" column renderer
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Notification_Edit_Renderer_QtyNeedsMore
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{

    /**
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Notification_Product $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Notification $notification */
        $notificationService = Magestore_Coresuccess_Model_Service::notificationService();
        $notification = $notificationService->getCurrentNotification();
        $lowStockThresholdType = $notification->getLowstockThresholdType();
        $qtyNeedsMore = 0;
        /** availability qty */
        if ($lowStockThresholdType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY) {
            $qtyNeedsMore = ceil($notification->getLowstockThresholdQty() - $row->getCurrentQty());
        }

        /** Available Date */
        if ($lowStockThresholdType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {
            $qtyNeedsMore = ceil($notification->getLowstockThreshold() * $row->getSoldPerDay() - $row->getCurrentQty());
        }

        return $qtyNeedsMore;

    }
}
