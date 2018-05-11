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
 * Inventorysuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_LowStockNotification_Source_Rule_SpecificDay extends Magestore_Inventorysuccess_Model_LowStockNotification_Source_AbstractSource
{

    /**
     * Get options
     *
     * @return array
     */

    public function toOptionArray()
    {
        $days = array();
        for ($i=1;$i<=31;$i++) {
            $days[(string)$i] = sprintf("%02d", $i);
        }
        return $days;
    }
}
