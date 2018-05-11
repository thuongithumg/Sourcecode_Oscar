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
 * Catalog Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Inventorysuccess_Model_LowStockNotification_Observer
{
    /**
     * notification
     */
    public function notification()
    {
        /** @var Magestore_Inventorysuccess_Model_Service_LowStockNotification_RuleService $ruleService */
        $ruleService = Magestore_Coresuccess_Model_Service::ruleService();
        $availableRules = $ruleService->getAvailableRules();
        if (count($availableRules)) {
            foreach ($availableRules as $rule) {
                $ruleService->startNotification($rule);
            }
        }
    }

    /**
     * redirect notify url to view notification detail
     * @param Varien_Event_Observer $observer
     * @return mixed
     */
    public function redirect(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $id = $request->getParam('id');
        if (($controller == 'inventorysuccess_lowstocknotification_notification')
            && $action == 'notify') {
            /** @var Mage_Adminhtml_Model_Url $backendUrl */
            $backendUrl = Mage::getModel('adminhtml/url');
            $redirectUrl = $backendUrl->getUrl(
                'adminhtml/inventorysuccess_lowstocknotification_notification/edit', array('id' => $id)
            );
            return $observer->getControllerAction()->getResponse()->setRedirect($redirectUrl);
        }
    }
}
