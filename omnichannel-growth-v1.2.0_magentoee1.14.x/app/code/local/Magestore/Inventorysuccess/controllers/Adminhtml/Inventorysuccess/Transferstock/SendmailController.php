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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_SendmailController
    extends
    Mage_Adminhtml_Controller_Action
{
    public function executeAction(){
        $id = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load($id);
        Magestore_Coresuccess_Model_Service::transferEmailService()->notifyEmailNewTransfer($transfer);
        if($type == Magestore_Inventorysuccess_Model_Transferstock::TYPE_SEND){
            $this->_redirect('*/inventorysuccess_transferstock_sendstock/edit', array('id' => $id));
        }
        if($type == Magestore_Inventorysuccess_Model_Transferstock::TYPE_REQUEST){
            $this->_redirect('*/inventorysuccess_transferstock_requeststock/edit', array('id' => $id));
        }
        if($type == Magestore_Inventorysuccess_Model_Transferstock::TYPE_FROM_EXTERNAL || $type == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL){
            $this->_redirect('*/inventorysuccess_transferstock_external/edit', array('id' => $id));
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stockcontrol/stock_transfer');
    }
}
