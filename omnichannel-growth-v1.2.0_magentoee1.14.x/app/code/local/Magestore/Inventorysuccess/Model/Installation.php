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
 * Class Magestore_Inventorysuccess_Model_Installation
 */
class Magestore_Inventorysuccess_Model_Installation extends Mage_Core_Model_Abstract
{
    
    const ID = 'id';
    const STEP = 'step';
    const CURRENT_INDEX = 'current_index';
    const STATUS = 'status';
    
    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETED = 2;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/installation');
    }    
}