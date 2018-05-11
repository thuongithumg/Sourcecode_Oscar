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
 * Class Magestore_Storepickup_Model_Mysql4_Storepickup
 */
class Magestore_Storepickup_Model_Mysql4_Storepickup extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the storepickup_id refers to the key field in your database table.
        $this->_init('storepickup/storepickup', 'storepickup_id');
    }
}