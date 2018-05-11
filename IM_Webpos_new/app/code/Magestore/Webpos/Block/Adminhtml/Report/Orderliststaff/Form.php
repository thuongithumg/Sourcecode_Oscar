<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report\Orderliststaff;

/**
 * Report location form filter.
 * @category Magestore
 * @package  Magestore_Webpos
 * @module   Webpos
 * @author   Magestore Developer
 */
class Form extends \Magestore\Webpos\Block\Adminhtml\Report\Filter\Form
{
    /**
     * Order config
     *
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Staff $staffSourceModel
     */
    protected $_staffSourceModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Sales\Model\Order\ConfigFactory $orderConfig
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Staff $staffSourceModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\ConfigFactory $orderConfig,
        \Magestore\Webpos\Model\Source\Adminhtml\Staff $staffSourceModel,
        \Magestore\Webpos\Helper\Data $helper,
        array $data = []
    ) {
        $this->_staffSourceModel = $staffSourceModel;
        parent::__construct($context, $registry, $formFactory, $orderConfig, $helper, $data);
    }

    protected function _construct()
    {
        $this->_newFieldFilterCode = 'period_type';
        $this->_newFieldFilterName = 'Staff';
        $this->_newFieldFilterOption = $this->_staffSourceModel->getOptionArray();
    }

}
