<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Shipping;
/**
 * class \Magestore\Webpos\Model\Shipping\ShippingRepository
 *
 * Web POS Customer Complain model
 * Use to work with Web POS complain table
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class ShippingRepository implements \Magestore\Webpos\Api\Shipping\ShippingRepositoryInterface
{
    /**
     * webpos shipping source model
     *
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Shipping
     */
    protected $_shippingModelSource;

    /**
     * webpos shipping result interface
     *
     * @var \Magestore\Webpos\Api\Data\Shipping\ShippingResultInterfaceFactory
     */
    protected $_shippingResultInterface;

    /**
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Shipping $shippingModelSource
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Shipping $shippingResultInterface
     */
    public function __construct(
        \Magestore\Webpos\Model\Source\Adminhtml\Shipping $shippingModelSource,
        \Magestore\Webpos\Api\Data\Shipping\ShippingResultInterfaceFactory $shippingResultInterface
    ) {
        $this->_shippingModelSource = $shippingModelSource;
        $this->_shippingResultInterface = $shippingResultInterface;
    }

    /**
     * Get shippings list
     *
     * @api
     * @return array|null
     */
    public function getList() {
       $shippingList = $this->_shippingModelSource->getPosShippingMethods();
       $shippings = $this->_shippingResultInterface->create();
       $shippings->setItems($shippingList);
       $shippings->setTotalCount(count($shippingList));
       return $shippings;
    }
}