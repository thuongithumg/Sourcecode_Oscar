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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Customercredit Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @author      Magestore Developer
 */
class Magestore_Customercredit_Block_Adminhtml_Maxbalance extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * prepare block's layout
     *
     * @return Magestore_Bannerslider_Block_Adminhtml_Addbutton
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customercredit/maxbalance.phtml');
        $this->setDefaultLimit(5);
    }

    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getTopFiveCustomerMaxCreditBalan()
    {

        return Mage::helper('customercredit')->topFiveCustomerMaxCredit();
    }

}
