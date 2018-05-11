<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report\Filter;

/**
 * Adminhtml report filter form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Reports\Block\Adminhtml\Filter\Form
{
    /**
     * Order config
     *
     * @var \Magento\Sales\Model\Order\ConfigFactory
     */
    protected $_orderConfig;

    /**
     * Status CollectionFactory
     *
     * @var Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var string
     */
    protected $_newFieldFilterCode;

    /**
     * @var string
     */
    protected $_newFieldFilterName;

    /**
     * @var array
     */
    protected $_newFieldFilterOption;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Sales\Model\Order\ConfigFactory $orderConfig
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\ConfigFactory $orderConfig,
        \Magestore\Webpos\Helper\Data $helper,
        array $data = []
    ) {
        $this->_orderConfig = $orderConfig;
        $this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fieldset with general report fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/sales');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'filter_form',
                    'action' => $actionUrl,
                    'method' => 'get'
                ]
            ]
        );

        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $webposFieldSet = $form->addFieldset('base_fieldset', ['legend' => __('Filter')]);

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $webposFieldSet->addField('store_ids', 'hidden', ['name' => 'store_ids']);

        if($this->_newFieldFilterCode)
            $webposFieldSet->addField(
                $this->_newFieldFilterCode,
                'select',
                [
                    'name' => $this->_newFieldFilterCode,
                    'label' => __($this->_newFieldFilterName),
                    'options' => $this->_newFieldFilterOption,
                ],
                'to'
            );

        $webposFieldSet->addField(
            'from',
            'date',
            [
                'name' => 'from',
                'date_format' => $dateFormat,
                'label' => __('From'),
                'title' => __('From'),
                'required' => true
            ]
        );

        $webposFieldSet->addField(
            'to',
            'date',
            [
                'name' => 'to',
                'date_format' => $dateFormat,
                'label' => __('To'),
                'title' => __('To'),
                'required' => true
            ]
        );

        $statuses = $this->_orderConfig->create()->getStatuses();
        $values = [];
        foreach ($statuses as $code => $label) {
            $values[] = ['label' => __($label), 'value' => $code];
        }

        $webposFieldSet->addField(
            'show_order_statuses',
            'select',
            [
                'name' => 'show_order_statuses',
                'label' => __('Order Status'),
                'options' => ['0' => __('Any'), '1' => __('Specified')],
                'note' => __('Applies to Any of the Specified Order Statuses except canceled orders')
            ],
            'to'
        );

        $webposFieldSet->addField(
            'order_statuses',
            'multiselect',
            ['name' => 'order_statuses', 'values' => $values, 'label' => '', 'display' => 'none'],
            'show_order_statuses'
        );

        // define field dependencies
        if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
            $this->setChild(
                'form_after',
                $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Widget\Form\Element\Dependence'
                )->addFieldMap(
                    "{$htmlIdPrefix}show_order_statuses",
                    'show_order_statuses'
                )->addFieldMap(
                    "{$htmlIdPrefix}order_statuses",
                    'order_statuses'
                )->addFieldDependence(
                    'order_statuses',
                    'show_order_statuses',
                    '1'
                )
            );
        }
        $this->helper->dispatchEvent('webpos_prepare_reports_filter_form', ['form' => $form, 'fieldset' => $webposFieldSet]);
        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

}
