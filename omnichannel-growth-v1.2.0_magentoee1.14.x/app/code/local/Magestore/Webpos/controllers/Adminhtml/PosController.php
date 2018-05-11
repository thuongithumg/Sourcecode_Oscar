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

class Magestore_Webpos_Adminhtml_PosController extends Mage_Adminhtml_Controller_Action
{
    /**
     *
     */
    const STAFF_NAME = "staff_name";
    /**
     *
     */
    const SALE_SUMMARY = "sale_summary";
    /**
     *
     */
    const CASH_TRANSACTION = "cash_transaction";
    /**
     *
     */
    const ZREPORT_SALES_SUMMARY = "zreport_sales_summary";
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function editAction()
    {
        $posId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('webpos/pos')->load($posId);

        if ($model->getPosId() || $posId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('pos_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('webpos/manage/webpos_pos');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Manager POS'),
                Mage::helper('adminhtml')->__('Manager POS')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('POS New'),
                Mage::helper('adminhtml')->__('POS New')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('webpos/adminhtml_pos_edit'))
                ->_addLeft($this->getLayout()->createBlock('webpos/adminhtml_pos_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webpos')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     *
     */
    public function newAction()
    {
        $this->_redirect('*/*/edit');
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('webpos/pos');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                $posId = $model->getPosId();
                if (isset($data['userlocation_user'])) {
                    $userArray = array();
                    parse_str(($data['userlocation_user']), $userArray);
                    $userArray = array_keys($userArray);
                    $userCollection=Mage::getModel('webpos/user')->getCollection()
                        ->addFieldToFilter('location_id',$this->getRequest()->getParam('id'));
                    foreach ($userCollection as $user) {
                        $userId=$user->getUserId();
                        if ($userId && !in_array($userId,$userArray)) {
                            $user->setLocationId(0);
                            $user->save();
                        }
                    }
                    foreach ($userArray as $user) {
                        if(is_numeric($user)){
                            $userModel = Mage::getModel('webpos/user')->load($user);
                            $userModel->setLocationId($posId);
                            $userModel->save();
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('webpos')->__('Pos was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('webpos')->__('Unable to find role to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('webpos/pos');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $locationIds = $this->getRequest()->getParam('webpos');
        if (!is_array($locationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Pos(s)'));
        } else {
            try {
                foreach ($locationIds as $locationId) {
                    $agent = Mage::getModel('webpos/pos')->load($locationId);
                    $agent->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($locationIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */


    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'pos.csv';
        $content    = $this->getLayout()
            ->createBlock('webpos/adminhtml_pos_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'pos.xml';
        $content    = $this->getLayout()
            ->createBlock('webpos/adminhtml_pos_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     *
     */
    protected function userAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('userlocation.edit.tab.user')
            ->setUsers($this->getRequest()->getPost('ouser', null));
        $this->renderLayout();
    }

    /**
     *
     */
    protected function sessionsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('pos.edit.tab.sessions')
            ->setUsers($this->getRequest()->getPost('ouser', null));
        $this->renderLayout();
    }

    /**
     *
     */
    protected function detailAction() {
        $this->loadLayout();
//        $this->getLayout()->getBlock('pos.edit.tab.detail');
        $this->renderLayout();
    }

    /**
     *
     */
    public function usergridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('userlocation.edit.tab.user')
            ->setUsers($this->getRequest()->getPost('ouser', null));
        $this->renderLayout();
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/manage_webpos_user_location');
    }

    /**
     *
     */
    public function closeSessionAction()
    {
        $data = Mage::getModel('api2/request')->getBodyParams();
        $sessionId = $data['session_id'];
        $realClosingBalance = $data['real_closing_balance'];
        $baseRealClosingBalance = $data['base_real_closing_balance'];
        $profitLossReason = $data['profit_loss_reason'];
        $closedAt = $data['closed_at'];
        if($sessionId){
            $messages = array(
                'success' => array(),
                'errors' => array()
            );
            try{
                $realClosingBalance = ($realClosingBalance)?floatval($realClosingBalance):0;
                $baseRealClosingBalance = ($baseRealClosingBalance)?floatval($baseRealClosingBalance):0;
                $session = Mage::getModel('webpos/shift')->load($sessionId);
                $session->setStatus(1);
                $session->setBalance(0);
                $session->setBaseBalance(0);
                $session->setClosedAmount($realClosingBalance);
                $session->setBaseClosedAmount($baseRealClosingBalance);
                $session->setCashRemoved($session->getCashRemoved() + $realClosingBalance);
                $session->setBaseCashRemoved($session->getBaseCashRemoved() + $baseRealClosingBalance);
                $session->setClosedAt($closedAt);
                $session->setProfitLossReason($profitLossReason);
                $session->save();
                $messages['success'][] = Mage::helper('webpos')->__('Session has been closed successfully');
            }catch (\Exception $e){
                $messages['errors'][] = $e->getMessage();
            }
            $this->_processResponseMessages($messages);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($this->getPosData()));
    }

    public function makeAdjustmentAction()
    {
        $data = Mage::getModel('api2/request')->getBodyParams();
        $transactionData = $data['transaction'];
        $transaction = Mage::helper('webpos/shift')->createTransaction($transactionData);
        $transaction->save();
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($this->getPosData()));
    }

    /**
     * @param array $response
     */
    protected function _processResponseMessages($response)
    {
        if (!empty($response['notices'])) {
            foreach ($response['notices'] as $message) {
                $this->_getSession()->addNotice($message);
            }
        }
        if (!empty($response['errors'])) {
            foreach ($response['errors'] as $message) {
                $this->_getSession()->addError($message);
            }
        }
        if (!empty($response['success'])) {
            foreach ($response['success'] as $message) {
                $this->_getSession()->addSuccess($message);
            }
        }
    }

    /**
     * @return array
     */
    public function getPosData(){
        $response = array();
        $response['sessions'] = $this->getCurrentSessions();
        $response['denominations'] = $this->getCurrentDenominations();
        return $response;
    }

    /**
     * @return array
     */
    public function getCurrentSessions(){
        $posId = $this->getCurrentPosId();
        $sessionsData = array();
        $sessions = ($posId)?$this->getOpenSession($posId):array();
        if(!empty($sessions)){
            foreach ($sessions as $session){
                $sessionData = $session->getData();
                $sessionData[self::STAFF_NAME] =  $session->getStaffName();
                $sessionData[self::SALE_SUMMARY] =  $session->getSaleSummary();
                $sessionData[self::CASH_TRANSACTION] =  $session->getCashTransaction();
                $sessionData[self::ZREPORT_SALES_SUMMARY] =  $session->getZreportSalesSummary();
                $sessionData['print_url'] =  $this->getUrl('adminhtml/zreport/print', array('id' => $session->getEntityId(), 'onlyprint' => 1));
                $sessionsData[] = $sessionData;
            }
        }
        return $sessionsData;
    }


    /**
     * @return array
     */
    public function getCurrentDenominations(){
        $posId = $this->getCurrentPosId();
        $denominations = array();
        $pos = ($posId)?Mage::getModel('webpos/pos')->load($posId):false;
        if($pos){
            $denominations = $pos->getDenominations();
        }
        return $denominations;
    }

    /**
     * @return mixed
     */
    public function getCurrentPosId(){
        return $this->getRequest()->getParam('pos_id');
    }

    /**
     * @param string $posId
     * @return array
     */
    public function getOpenSession($posId = '')
    {
        $collection = Mage::getModel('webpos/shift')->getCollection();
        if ($posId) {
            $collection->addFieldToFilter('pos_id', $posId);
        }
        $collection->addFieldToFilter('status', 0);
        if ($collection->getSize() > 0) {
            return $collection->getItems();
        }
        return array();
    }


}