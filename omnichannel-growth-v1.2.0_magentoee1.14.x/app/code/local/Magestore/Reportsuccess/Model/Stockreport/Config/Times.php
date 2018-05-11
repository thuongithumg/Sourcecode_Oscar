<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Magestore_Reportsuccess_Model_Stockreport_Config_Times
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Stockreport_Config_Times
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        for($i = 0;$i<=23;$i++){
            $i = sprintf("%02d", $i);
            $times[$i]= $i.':00';
        }
        $arr = array();
        foreach ($times as $id=>$value) {
            $arr[] = array('value'=>$id, 'label'=>$value);
        }
        return $arr;
    }
}

