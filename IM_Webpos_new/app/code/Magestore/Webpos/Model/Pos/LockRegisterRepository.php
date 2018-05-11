<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Pos;

/**
 * Class LockRegisterRepository
 * @package Magestore\Webpos\Model\Pos
 */
class LockRegisterRepository implements \Magestore\Webpos\Api\Pos\LockRegisterRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $permissionHelper;

    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    protected $posRepository;

    public function __construct(
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
    )
    {
        $this->permissionHelper = $permissionHelper;
        $this->posRepository = $posRepository;
    }

    /**
     * Lock Pos
     *
     * @param string|int|null $posId
     * @param string|int|null pin
     * @return boolean
     * @throws \Exception
     */
    public function lockPos($posId = null, $pin = null)
    {
        $response = ['success' => true];
        try {
            if (!$posId) {
                $posId = $this->permissionHelper->getCurrentPosId();
            }
            if (!$posId) {
                throw new \Exception(__('Cannot find POS to lock'));
            }
            $pos = $this->posRepository->get($posId);
            if (!$pos->getId()) {
                throw new \Exception(__('Cannot find POS to lock'));
            }
            if ($pos->getData('is_allow_to_lock') == \Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab\Form::STATUS_DISABLED) {
                throw new \Exception(__('POS is not allow to lock'));
            }
            $status = $pos->getStatus();
            if ($status == \Magestore\Webpos\Model\Pos\Status::STATUS_DISABLED) {
                throw new \Exception(__('POS is disabled currently'));
            }
            if ($status == \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED) {
                throw new \Exception(__('POS is locked currently'));
            }
            $posPin = $pos->getPin();
            if ($pin != $posPin) {
                throw new \Exception(__('Invalid security PIN. Please try again!'));
            }
            $staffId = $this->permissionHelper->getCurrentUser();
            if ($staffId) {
                if (!$this->permissionHelper->isAllowResource('Magestore_Webpos::lock_unlock_register')) {
                    throw new \Exception(__('Permission denied. Please contact Administrator to lock the register.'));
                }
                $pos->setStaffLocked($staffId);
            }
            $pos->setStatus(\Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED);
            $this->posRepository->save($pos);
        } catch (\Exception $ex) {
            $response = ['success' => false, 'message' => $ex->getMessage()];
        }
        return \Zend_Json::encode($response);
    }
}