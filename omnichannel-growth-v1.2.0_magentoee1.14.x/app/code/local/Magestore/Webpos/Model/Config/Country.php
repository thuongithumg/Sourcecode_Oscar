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

class Magestore_Webpos_Model_Config_Country extends Magestore_Webpos_Model_Abstract
{
    /**
     * Get countries information
     *
     * @api
     * @return array|null
     */
    public function getList()
    {
        $collection = Mage::getModel('directory/country')->getCollection();
        $countriesInfo = array();
        foreach ($collection as $country) {
            /* @var $country Mage_Directory_Model_Country */
            $countriesInfo[] = array(
                'country_id' => $country->getId(),
                'country_name' => $country->getName()
            );
        }
        return $countriesInfo;
    }

}
