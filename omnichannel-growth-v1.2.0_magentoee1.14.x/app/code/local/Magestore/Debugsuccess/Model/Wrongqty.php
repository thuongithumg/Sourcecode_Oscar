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
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Reportsuccess_Model_Historics
 */
class Magestore_Debugsuccess_Model_Wrongqty extends
    Mage_Core_Model_Abstract
{

    //const ID = 'item_id';
    const ID = 'product_id';
    public function _construct()
    {
        parent::_construct();
        $this->_init('debugsuccess/wrongqty');
    }
}