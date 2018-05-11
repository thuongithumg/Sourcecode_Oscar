<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:42
 */

namespace Magestore\Webpos\Model\Shift;
use Magestore\Webpos\Api\Data\Shift\CashTransactionInterface;
use Magestore\Webpos\Api\Data\Shift\ShiftInterface;
use Magento\Framework\Exception\CouldNotSaveException;


class CashTransactionRepository implements \Magestore\Webpos\Api\Shift\CashTransactionRepositoryInterface
{
    /** @var  $cashTransactionResource \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction */
    protected $cashTransactionResource;

    /** @var  \Magestore\Webpos\Model\Shift\ShiftFactory */
    protected $_shiftFactory;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    /**
     * @var CashTransactionFactory
     */
    protected $cashTransactionFactory;

    /**
     * CashTransactionRepository constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction $cashTransactionResource
     * @param ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Helper\Shift $shiftHelper
     * @param CashTransactionFactory $cashTransactionFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction $cashTransactionResource,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory
    ) {
        $this->cashTransactionResource = $cashTransactionResource;
        $this->cashTransactionFactory = $cashTransactionFactory;
        $this->_shiftFactory = $shiftFactory;
        $this->_shiftHelper = $shiftHelper;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface $cashTransactionInterface $cashTransactionInterface
     * @return \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface $cashTransactionInterface
     * @throws CouldNotSaveException
     */
    public function save(CashTransactionInterface $cashTransactionInterface)
    {
        $shiftId = $cashTransactionInterface->getShiftId();
        $shiftModel = $this->_shiftFactory->create();
        $shiftModel->load($shiftId, "shift_id");

        if(!$shiftModel->getShiftId()){
            return;
        }

        try {
            $shiftData = $shiftModel->recalculateData($cashTransactionInterface);
            $cashTransactionInterface->setBalance($shiftData['balance']);
            $cashTransactionInterface->setShiftId($shiftData['shift_id']);
            $cashTransactionInterface->setBaseBalance($shiftData['base_balance']);
            $this->cashTransactionResource->save($cashTransactionInterface);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        $shiftData = $this->_shiftHelper->prepareOfflineShiftData($shiftData['shift_id']);
        $response[] = $shiftData;

        return $response;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createTransaction($data){
        $transaction = $this->cashTransactionFactory->create();
        if(!empty($data)){
            foreach ($data as $key => $value){
                $transaction->setData($key, $value);
            }
        }
        return $transaction;
    }
}