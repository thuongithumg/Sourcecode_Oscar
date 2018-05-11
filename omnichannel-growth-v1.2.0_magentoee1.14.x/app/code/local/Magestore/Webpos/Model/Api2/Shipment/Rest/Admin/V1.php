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

/**
 * API2 for catalog_product (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Webpos_Model_Api2_Shipment_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_CREATE_SHIPMENT = 'save';


    public function dispatch()
    {
        switch ($this->getActionType()) {
            case self::OPERATION_CREATE_SHIPMENT:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->saveShipment($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
        }
    }

    public function saveShipment($entity){
        $helper = Mage::helper('webpos/order');
        $emailSent = $entity['entity']['emailSent'];
        $data = $this->_prepareShipment($entity);
        $qty = $data['shipment']['items'];
        $orderId = $data['order_id'];
        $order = Mage::getModel('sales/order')->load($orderId);;
        $shipment = $order->prepareShipment($qty);
        if (!empty($data['shipment']['comment_text'])) {
            $shipment->addComment(
                $data['shipment']['comment_text'],
                isset($data['shipment']['comment_customer_notify']),
                isset($data['shipment']['is_visible_on_front'])
            );

            $shipment->setCustomerNote($data['shipment']['comment_text']);
            $shipment->setCustomerNoteNotify(isset($data['shipment']['comment_customer_notify']));
        }

        $tracks = $data['tracking'];

        if ($tracks) {
            foreach ($tracks as $trackData) {
                if (empty($trackData['number'])) {
                    Mage::throwException(Mage::helper('sales')->__('Tracking number cannot be empty.'));
                }
                $track = Mage::getModel('sales/order_shipment_track')
                    ->addData($trackData);
                $shipment->addTrack($track);
            }
        }

        $shipment->register();
        $this->_saveShipment($shipment);
        if ($emailSent) {
            $shipment->sendEmail(true)
                ->setEmailSent(true)
                ->save();
            $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                ->getUnnotifiedForInstance($shipment, Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
            if ($historyItem) {
                $historyItem->setIsCustomerNotified(1);
                $historyItem->save();
            }
        }
        $result = $helper->getAllOrderInfo($order);
        return $result;
    }

    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        return $this;
    }

    protected function _prepareShipment($params){
        $data = array();
        if (isset($params['entity'])) {
            $entity = $params['entity'];
            $items = $entity['items'];
            $orderId = $entity['orderId'];
        }

        if(count($items>0) && $orderId){
            $data['order_id'] = $orderId;
            $tracks = $entity['tracks'];
            $data['tracking'] = null;
            if(count($tracks) && $track = $tracks[0]){
                $trackData = array();
                $trackData['carrier_code'] = 'custom';
                $trackData['number'] = $track['track_number'];
                $data['tracking'][] = $trackData;
            }
            $shipment = array();
            foreach ($items as $item){
                $shipment['items'][$item['orderItemId']] = $item['qty'];
            }
            $comments = $entity['comments'];
            if(count($comments) && $comment = $comments[0]){
                $shipment['comment_text'] = $comment['comment'];
                if(!empty($shipment['send_email']))
                    $shipment['comment_customer_notify'] = 1;
            }
            $data['shipment'] = $shipment;
            return $data;
        }
        return null;
    }

}
