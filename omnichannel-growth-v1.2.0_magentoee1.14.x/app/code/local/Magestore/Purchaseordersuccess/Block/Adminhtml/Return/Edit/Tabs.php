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
use Magestore_Purchaseordersuccess_Model_Return_Options_Status as ReturnStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    public function __construct()
    {
        parent::__construct();
        $this->setId('return_request_tabs');
        $this->setDestElementId('edit_form');
        /** @var Magestore_Purchaseordersuccess_Model_Return $returnRequest */
        $this->returnRequest = Mage::registry('current_return_request');
        $title = 'Return Request Information';
        $this->setTitle($title);
    }

    /**
     * prepare before render block to html
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $status = $this->returnRequest->getStatus();
        if ($this->returnRequest->getReturnOrderId()) {
            $this->addTab('summary_information', array(
                'label' => $this->__('Summary'),
                'title' => $this->__('Summary'),
                'content' => $this->getLayout()
                    ->createBlock('purchaseordersuccess/adminhtml_return_edit_tab_returnsummary')
                    ->toHtml(),
            ));
            if ($status != ReturnStatus::STATUS_PENDING) {
//                if (Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess')) {
                    $this->addTab('transferred_item', array(
                        'label' => $this->__('Delivered Item'),
                        'url' => $this->getUrl('*/*/transferreditem', array('_current' => true)),
                        'class' => 'ajax'
                    ));
//                }
            }
        }

        /** information form */
        $this->addTab('general_information', array(
            'label' => $this->__('General Information'),
            'title' => $this->__('General Information'),
            'content' => $this->getLayout()
                ->createBlock('purchaseordersuccess/adminhtml_return_edit_tab_general')
                ->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}