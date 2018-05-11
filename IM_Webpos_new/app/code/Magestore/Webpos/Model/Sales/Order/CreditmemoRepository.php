<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Sales\Order;

use Magento\Sales\Model\ResourceModel\Metadata as Metadata;;
use Magento\Sales\Api\Data\CreditmemoSearchResultInterfaceFactory as SearchResultFactory;

/**
 * Repository class for @see \Magento\Sales\Api\Data\CreditmemoInterface
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreditmemoRepository extends \Magento\Sales\Model\Order\CreditmemoRepository
    implements \Magestore\Webpos\Api\Sales\CreditmemoRepositoryInterface
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    protected $creditmemoManagement;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\Webpos\Helper\Currency
     */
    protected $currencyHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var EventManager
     */
    protected $eventManager;

    protected $_shiftHelper;

    /**
     * CreditmemoRepository constructor.
     * @param Metadata $metadata
     * @param SearchResultFactory $searchResultFactory
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender
     * @param \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository
     * @param \Magestore\Webpos\Helper\Currency $currencyHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Event\Manager $eventManager
     */
    public function __construct(
        Metadata $metadata,
        SearchResultFactory $searchResultFactory,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender,
        \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository,
        \Magestore\Webpos\Helper\Currency $currencyHelper,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Event\Manager $eventManager
    ) {
        $this->metadata = $metadata;
        $this->searchResultFactory = $searchResultFactory;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditmemoManagement = $creditmemoManagement;
        $this->creditmemoSender = $creditmemoSender;
        $this->orderRepository = $orderRepository;
        $this->currencyHelper = $currencyHelper;
        $this->productFactory = $productFactory;
        $this->request = $request;
        $this->eventManager = $eventManager;
        $this->_shiftHelper = $shiftHelper;
    }

    /**
     * Performs persist operations for a specified credit memo.
     *
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $entity The credit memo.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface Order interface.
     */
    public function saveCreditmemo(\Magestore\Webpos\Api\Data\Sales\CreditmemoInterface $entity){
        $data = $this->prepareCreditmemo($entity);
        $this->creditmemoLoader->setOrderId($data['order_id']);
        $this->creditmemoLoader->setCreditmemo($data['creditmemo']);
        
        $this->request->setParams($data);
        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            $creditmemo->setData('webpos_shift_id',$this->_shiftHelper->getCurrentShiftId());
            if (!$creditmemo->isValidGrandTotal()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The credit memo\'s total must be positive.')
                );
            }
            if (!empty($data['creditmemo']['comment_text'])) {
                $creditmemo->addComment(
                    $data['creditmemo']['comment_text'],
                    isset($data['creditmemo']['comment_customer_notify']),
                    true
                );
                if(isset($data['creditmemo']['comment_text']))
                    $creditmemo->setCustomerNote($data['creditmemo']['comment_text']);
                if(isset($data['creditmemo']['comment_customer_notify']))
                    $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }
            $this->creditmemoManagement->refund($creditmemo, true, !empty($data['creditmemo']['send_email']));

            if (!empty($data['creditmemo']['send_email'])) {
                $this->creditmemoSender->send($creditmemo);
            }
        }

        $this->eventManager->dispatch('refund_storecredit_ee',['creditmeno'=>$creditmemo]);
        if ($entity->getRefundByCash()){
            $this->eventManager->dispatch('webpos_refund_by_cash',['creditmeno'=>$creditmemo]);
        }
        return $this->orderRepository->get($data['order_id']);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Sales\CreditmemoInterface $entity
     * @return array
     */
    protected function prepareCreditmemo(\Magestore\Webpos\Api\Data\Sales\CreditmemoInterface $entity){
        $data = [];
        $items = $entity->getItems();
        $orderId = $entity->getOrderId();
        if(count($items>0) && $orderId){
            $data['order_id'] = $orderId;
            $creditmemo = [];
            foreach ($items as $item){
                $creditmemo['items'][$item->getOrderItemId()]['qty'] = $item->getQty();
                if($item->getAdditionalData() == 'back_to_stock') {
                    $creditmemo['items'][$item->getOrderItemId()]['back_to_stock'] = 1;
                }
            }
            $creditmemo['send_email'] = $entity->getEmailSent();
            $comments = $entity->getComments();
            if(count($comments) && $comment = $comments[0]){
                $creditmemo['comment_text'] = $comment->getComment();
                if($creditmemo['send_email'])
                    $creditmemo['comment_customer_notify'] = 1;
            }
            $baseCurrencyCode = $entity->getBaseCurrencyCode();
            $storeCurrencyCode = $entity->getStoreCurrencyCode();
            $creditmemo['shipping_amount'] = $this->currencyHelper->currencyConvert($entity->getShippingAmount(), $storeCurrencyCode, $baseCurrencyCode);
            $creditmemo['adjustment_positive'] = $this->currencyHelper->currencyConvert($entity->getAdjustmentPositive(), $storeCurrencyCode, $baseCurrencyCode);
            $creditmemo['adjustment_negative'] = $this->currencyHelper->currencyConvert($entity->getAdjustmentNegative(), $storeCurrencyCode, $baseCurrencyCode);
            $creditmemo['refund_points'] = $entity->getRefundPoints();
            $creditmemo['webpos_shift_id'] = 'shift_sfsd';
            $creditmemo['refund_earned_points'] = $entity->getRefundEarnedPoints();
            $data['creditmemo'] = $creditmemo;
            return $data;
        }
    }
}
