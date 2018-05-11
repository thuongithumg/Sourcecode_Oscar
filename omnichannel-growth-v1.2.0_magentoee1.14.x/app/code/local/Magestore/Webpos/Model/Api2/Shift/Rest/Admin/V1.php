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

class Magestore_Webpos_Model_Api2_Shift_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract implements Magestore_Webpos_Api_ShiftInterface
{
    protected $_transactionService;

    /**
     * Magestore_Webpos_Model_Api2_Shift_Rest_Admin_V1 constructor.
     */
    public function __construct() {
        $this->_service = $this->_createService('shift_shift');
        $this->_transactionService = $this->_createService('transaction_transaction');
        $this->_helper = Mage::helper('webpos');
    }
    /**
     * Dispatch actions
     */
    public function dispatch()
    {
        $this->_initStore();

        switch ($this->getActionType()) {
            case self::ACTION_GET_DATA:
                $cashDrawerId = $this->_processRequestParams(self::TILL_ID);
                $result = $this->_service->getShiftData($cashDrawerId);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_CLOSE_SHIFT:
                $zReportData = $this->_processRequestParams(self::DATA);
                $result = $this->_service->closeShift($zReportData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_SAVE:
                $zReportData = $this->_processRequestParams(self::DATA);
                $result = $this->_service->save($zReportData);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::ACTION_GET_LIST:
                $result = $this->getList();
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    /**
     * get shift list
     * @return mixed
     */
    public function getList(){
        $shiftCollection = Mage::getResourceModel('webpos/zreport_collection');
        $shiftCollection->getSelect()->joinLeft(
                    array('till' => $shiftCollection->getTable('webpos/till')),
                    'main_table.till_id = till.till_id',
                    array('till_name', 'store_id','location_id'));
        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }
        $orderField = $this->getRequest()->getOrderField();
        if (null !== $orderField) {
            $shiftCollection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $this->_applyFilterTo($shiftCollection);
        $numberOfShifts = $shiftCollection->getSize();
        $items = array();
        foreach ($shiftCollection as $shift) {
            $shiftData =  $this->_service->getShiftResponse($shift);
            $items[] = $shiftData;
        }

        if ($pageNumber <= ($numberOfShifts/$pageSize+1)) {
            $result['items'] = $items;
            $result['total_count'] = $numberOfShifts;
        } else {
            $result = array(
                'items' => array(),
            );
        }

        return $result;
    }
}
