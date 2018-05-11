<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Register;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class WebposBeforeRenderLayoutObserver
 * @package Magestore\WebposBambora\Observer
 */
class WebposBeforeRenderLayoutObserver implements ObserverInterface
{
    /**
     * @var \Magestore\WebposBambora\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magestore\Webpos\Helper\Permission
     */
    protected $permission;
    /**
     * @var \Magestore\Webpos\Model\PosFactory
     */
    protected $posFactory;

    /**
     * WebposBeforeRenderLayoutObserver constructor.
     * @param \Magestore\WebposBambora\Helper\Data $data
     */
    public function __construct(
        \Magestore\Webpos\Helper\Data $data,
        \Magestore\Webpos\Helper\Permission $permission,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory
    ){
        $this->helper = $data;
        $this->permission = $permission;
        $this->posFactory = $posFactory;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $resultLayout = $observer->getData('layout');
        $canChangePinPermission = $this->permission->isAllowResource('Magestore_Webpos::edit_pin');
        $posId = $this->permission->getCurrentPosId();
        $posModel = $this->posFactory->create()->load($posId);

        if ($this->helper->getStoreConfig('webpos/general/enable_session') && $this->helper->getStoreConfig('webpos/general/enable_session') && $canChangePinPermission) {
            if ($posModel->getData('is_allow_to_lock')){
                $resultLayout->addHandle('webpos_register_security');
            }
        }
        return $this;
    }

}