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
 * Class Magestore_Storepickup_Model_Sales_Quote_Address_Total_Storepickup
 */
class Magestore_Storepickup_Model_Sales_Quote_Address_Total_Storepickup extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $datashipping = Mage::getSingleton('checkout/session')->getData('storepickup_session');
        $shippingMethod = $address->getShippingMethod();
        $shippingMethod = explode('_', $shippingMethod);
        $shippingCode = $shippingMethod[0];
        if ($shippingCode != "storepickup")
            return $this;
        if ((isset($datashipping['date']) && $datashipping['date'])) {
            $description = $address->getShippingDescription() . ' ' . Mage::helper('storepickup')->__('Pickup Time:') . ' ' . $datashipping['date'];
        }

        if  ((isset($datashipping['time']) && $datashipping['time'])) {
            $description = $description . ' ' . $datashipping['time'];
        }

        $address->setShippingDescription($description);
        return $this;
    }

}
