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
 * Reportsuccess Magestore_Reportsuccess_Model_Stockreport_Applytime
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Stockreport_Applytime
{
    const _LAST_7_DAY = 'last_7_day';
    const _LAST_30_DAY = 'last_30_day';
    const _LAST_3_MONTH = 'last_3_month';

    /**
     * @var array
     */
    public $_options = array(
        'last_7_day' => 7,
        'last_30_day' => 30,
        'last_3_month' => 90,
    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = array(
            self::_LAST_7_DAY=> 'Last 7 days',
            self::_LAST_30_DAY=> 'Last 30 days',
            self::_LAST_3_MONTH=> 'Last 3 months',
        );
        return $result;
    }
}

