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
 * Magestore_Webpos Model
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @author      Magestore Developer
 */


class Magestore_Webpos_Model_Rewrite_Sales_Order extends Mage_Sales_Model_Order
{

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        $canCreditmemo = parent::canCreditmemo();
        if ($canCreditmemo) {
            $webposChange = $this->getData('webpos_change');
            if($webposChange){
                $totalPaid = $this->getStore()->roundPrice($this->getTotalPaid());
                $totalRefunded = $this->getTotalRefunded();
                if(abs($totalPaid - $totalRefunded - $webposChange) < .0001){
                    return false;
                }
            }
        }
        return $canCreditmemo;
    }


}
