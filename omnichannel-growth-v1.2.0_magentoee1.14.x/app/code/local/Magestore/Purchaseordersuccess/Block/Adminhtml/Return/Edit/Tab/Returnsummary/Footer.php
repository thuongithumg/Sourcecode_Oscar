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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Footer
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->returnRequest = Mage::registry('current_return_request');
    }

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/return/edit/tab/return_summary/footer.phtml';

    public function getReturnReason(){
        return $this->returnRequest->getReason();
    }
}