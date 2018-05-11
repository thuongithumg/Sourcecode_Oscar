<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Integration\Storecreditee;

/**
 * Store credit api model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreditRepository implements \Magestore\Webpos\Api\Integration\Storecreditee\CreditRepositoryInterface
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterfaceFactory
     */
    protected $searchResultsInterface;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customer;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkoutSessionFactory;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /** @var \Magento\Sales\Api\Data\OrderInterface $order **/
    protected $order;

    /**
     * CreditRepository constructor.
     * @param \Magestore\Webpos\Helper\Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterfaceFactory $searchResultsInterface
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterfaceFactory $searchResultsInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customer,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magestore\Webpos\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->searchResultsInterface = $searchResultsInterface;
        $this->_checkoutSession = $checkoutSession;
        $this->customer = $customer;
        $this->_objectManager = $objectmanager;
        $this->_helperData = $helperData;
        $this->_storeManager = $storeManager;
        $this->order = $order;
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
    public function apply(){
        $this->_checkoutSession->setData('use_storecredit_ee', true);
        $isUsedCredit =  $this->_checkoutSession->getData('use_storecredit_ee');
        return $isUsedCredit;
    }

    /**
     * @param string $orderId
     * @param string $orderIncrementId
     * @param string $customerId
     * @return string
     */
    public function refund($orderId, $orderIncrementId, $customerId){

    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomerBalance($customerId){
        if ($this->_helperData->checkMagentoEE()) {
             $credit = $this->_objectManager->create('\Magento\CustomerBalance\Model\BalanceFactory')->create()->getCollection()->addFieldToFilter(
                 'customer_id',
                 $customerId
             )->getFirstItem();
             return $credit->getAmount();
         }
    }


    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function cancel(){
        $this->_checkoutSession->setData('use_storecredit_ee', false);
        $this->_checkoutSession->setData('credit_amount', 0);
        $isUsedCredit =  $this->_checkoutSession->getData('use_storecredit_ee');
        return $isUsedCredit;
    }

}