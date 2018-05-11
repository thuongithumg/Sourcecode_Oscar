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


class Magestore_Webpos_Block_Content_Main extends Magestore_Webpos_Block_AbstractBlock
{

    /**
     * @var Magestore_Webpos_Helper_Permission
     */
    protected $_helperPermission = false;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helperPermission = Mage::helper('webpos/permission');
        $this->_helper = Mage::helper('webpos');
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $isLogin = $this->_helperPermission->getCurrentUser();
        if ($isLogin && !$this->_helperPermission->isShowChoosePosLocation()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    /**
     * @return mixed
     */
    public function getWebposData(){
        return Mage::getModel('webpos/dataManager')->getWebposData();
    }

    /**
     * Retrieve webpos color
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getWebposColor()
    {
        $color = $this->_helper->getStoreConfig('webpos/general/webpos_color');
        if($color)
            return $color;
        return '00A679';
    }

}
