<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Integration\Storecredit;

/**
 * Store credit api model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreditRepository implements \Magestore\Webpos\Api\Integration\Storecredit\CreditRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterfaceFactory
     */
    protected $searchResultsInterface;

    /**
     * CreditRepository constructor.
     * @param \Magestore\Webpos\Helper\Data $helperData
     * @param \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterfaceFactory $searchResultsInterface
     */
    public function __construct(
        \Magestore\Webpos\Helper\Data $helperData,
        \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterfaceFactory $searchResultsInterface
    ) {
        $this->_helper = $helperData;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->searchResultsInterface = $searchResultsInterface;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function refundByCredit($orderId, $orderIncrementId, $customerId, $amount){
        $data = [];
        if($customerId){
            $transaction = $this->_objectManager->create('\Magestore\Customercredit\Model\Transaction');
            $customercredit = $this->_objectManager->create('\Magestore\Customercredit\Model\Customercredit');
            $type_id = \Magestore\Customercredit\Model\TransactionType::TYPE_REFUND_ORDER_INTO_CREDIT;
            $transaction_detail = __("Refund order") ." #". $orderIncrementId;
            if ($transaction && $customercredit && !empty($amount)) {
                $transaction->addTransactionHistory($customerId, $type_id, $transaction_detail , $orderIncrementId, $amount);
                $customercredit->changeCustomerCredit($amount, $customerId);
            }
            $data['success'] = true;
        }else{
            $data['message'] = __('Customer account not found');
            $data['error'] = true;
        }
        return \Zend_Json::encode($data);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBalance($customerId){
        $data = [];
        if($customerId){
            $model = $this->_objectManager->create('Magestore\Customercredit\Model\Customercredit');
            $resource = $this->_objectManager->create('Magestore\Customercredit\Model\ResourceModel\Customercredit');
            $resource->load($model, $customerId, 'customer_id');
            if($model->getId() > 0){
                $data['balance'] = floatval($model->getCreditBalance());
            }else{
                $data['balance'] = floatval(0);
            }
            $data['success'] = true;
        }else{
            $data['message'] = __('Please choose customer account');
            $data['error'] = true;
        }
        return \Zend_Json::encode($data);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getList(){
        $items = [];
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
        $collection = $this->_objectManager->create('Magestore\Customercredit\Model\ResourceModel\Customercredit\Collection');
        $collection->addFieldToFilter('credit_balance', array('gt' => 0));
        $total = $collection->getSize();
        $collection->setPageSize($request->getParam('searchCriteria')["pageSize"]);
        $collection->setCurPage($request->getParam('searchCriteria')["currentPage"]);
        $collection->load();
        if($total > 0){
            $items = $collection->getItems();
        }
        $result = $this->searchResultsInterface->create();
        $result->setItems($items);
        $result->setTotalCount($total);
        return $result;
    }
}