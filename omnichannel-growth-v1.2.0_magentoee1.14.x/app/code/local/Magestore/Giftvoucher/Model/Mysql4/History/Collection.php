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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher history resource collection
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Mysql4_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('giftvoucher/history');
    }

    /**
     * @return $this
     */
    public function joinGiftVoucher()
    {
        if ($this->hasFlag('join_giftvoucher') && $this->getFlag('join_giftvoucher')) {
            return $this;
        }
        $this->setFlag('join_giftvoucher', true);
        $this->getSelect()->joinLeft(
            array('giftvoucher' => $this->getTable('giftvoucher/giftvoucher')), 
            'main_table.giftvoucher_id = giftvoucher.giftvoucher_id', array('gift_code')
        )->where('main_table.action = ?', Magestore_Giftvoucher_Model_Actions::ACTIONS_SPEND_ORDER);
        return $this;
    }

}
