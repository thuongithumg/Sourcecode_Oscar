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
 * Class Magestore_Storepickup_Model_Holiday
 */
class Magestore_Storepickup_Model_Holiday extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('storepickup/holiday');
    }

    /**
     * @param $date
     * @param $store_id
     * @return bool
     */
    public function isHoliday($date, $store_id) {
        $check =  false;
        $date = substr($date, 6, 4) . '-' . substr($date, 0, 3) . substr($date, 3, 2);

        $collection = $this->getCollection()
                ->addFieldToFilter('store_id',array('finset' => $store_id));
                
                //->addFieldToFilter('store_id', $store_id);
        //->addFieldToFilter('date',$date);
        foreach ($collection as $holiday) {            
            if ($date >= $holiday->getDate() && $date <= $holiday->getHolidayDateTo()) {
                $check = true;
            }
        }
        
        
        return $check;
        
    }

}
