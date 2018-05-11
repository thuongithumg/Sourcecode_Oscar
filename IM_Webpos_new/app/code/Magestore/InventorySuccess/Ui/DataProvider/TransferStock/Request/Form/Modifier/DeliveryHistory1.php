<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\InventorySuccess\Ui\DataProvider\TransferStock\Request\Form\Modifier;

use Magento\Ui\Component\Form;
use Magestore\InventorySuccess\Model\TransferStock;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeliveryHistory extends \Magestore\InventorySuccess\Ui\DataProvider\Form\Modifier\Dynamic
{
    protected $_sortOrder = '3';
    protected $_opened = true;

    protected $request;

    protected $_groupContainer = 'delivery_history';

    protected $_dataLinks = 'deliery_history';

    protected $_groupLabel = 'Delivery History';


    protected $_fieldsetContent = 'Please add or import products to deliver';
    

    protected $_buttonTitle = 'Add Products to Deliver';


    protected $_modalTitle = 'Add Products to Deliver';


    protected $_modalButtonTitle = 'Done';

    protected $_modifierConfig = [
        'button_set' => 'product_stock_button_set',
        'modal' => 'addDelieryModal',
        'listing' => 'os_transferstock_warehouse_product_stock_listing',
        'form' => 'transferstock_request_form',
        'history_listing' => 'transferstock_delivery_history',
        'columns_ids' => 'product_columns.ids'
    ];

    protected $_mapFields = [
        'id' => 'entity_id',
        'sku' => 'sku',
        'name' => 'name',
        'qty' => 'qty',
        'request_qty' => 'request_qty'
    ];

    /** @var \Magestore\InventorySuccess\Model\TransferStockFactory $_transferStockFactory */
    protected $_transferStockFactory;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magestore\InventorySuccess\Model\TransferStockFactory $transferStockFactory,
        array $_modifierConfig = []
    ) {
        parent::__construct($urlBuilder,$request, $_modifierConfig);
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->_modifierConfig = array_replace_recursive($this->_modifierConfig, $_modifierConfig);
        $this->productFactory = $productFactory;
        $this->_transferStockFactory = $transferStockFactory;


    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        
        return parent::modifyData($data);
        

    }

    public function getVisible(){
        $transferstock_id = $this->request->getParam('id');

        if($transferstock_id){
            $transferStock = $this->_transferStockFactory->create()->load($transferstock_id);
            if($transferStock->getStatus() != TransferStock::STATUS_PENDING ){
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    protected function getModifierChildren()
    {
        $children = [
            $this->_modifierConfig['button_set'] => $this->getCustomButtons(),
            $this->_modifierConfig['modal'] => $this->getCustomModal(),
            $this->_modifierConfig['history_listing'] => $this->getDeliveryHistoryListing(),
        ];
        return $children;
    }


    /**
     * Returns Modal configuration
     *
     * @return array
     */
    protected function getCustomModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' =>
                            $this->_modifierConfig['form']
                            . '.'
                            . $this->_modifierConfig['form']
                            . '_data_source',
                        'options' => [
                            'title' => __($this->_modalTitle),
                            'buttons' => [
                                [
                                    'text' => __($this->_modalButtonTitle),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => '${ $.name }.' . $this->_modifierConfig['listing'],
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [$this->_modifierConfig['listing'] => $this->getModalListing()],
        ];
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    protected function getModalListing()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->_modifierConfig['listing'],
                        'externalProvider' =>
                            $this->_modifierConfig['listing']
                            . '.'
                            . $this->_modifierConfig['listing']
                            . '_data_source',
                        'selectionsProvider' =>
                            $this->_modifierConfig['listing']
                            . '.'
                            . $this->_modifierConfig['listing']
                            . '.'
                            . $this->_modifierConfig['columns_ids'],
                        'ns' => $this->_modifierConfig['listing'],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'params' => ['id'=> $this->request->getParam('id')],
                        'realTimeLink' => true,
                        'formProvider' => 'ns = ${ $.namespace }, index = product_form',
                        'addDeliveryUrl' =>
                            $this->urlBuilder->getUrl('inventorysuccess/transferstock_request/save'),
                        'provider' =>
                            $this->_modifierConfig['form']
                            . '.'
                            . $this->_modifierConfig['form']
                            . '_data_source',
                        'dataLinks' => ['imports' => false, 'exports' => true],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'storeId' => '${ $.provider }:data.product.current_store_id',
                        ],
                        'exports' => [
                            'storeId' => '${ $.externalProvider }:params.current_store_id',
                        ],
                    ],
                ],
            ],
        ];
    }
    /**
     * Returns Buttons Set configuration
     *
     * @return array
     */
    protected function getCustomButtons()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => __($this->_fieldsetContent),
                        'template' => 'Magestore_InventorySuccess/form/components/button-list',
                    ],
                ],
            ],
            'children' => [
                'add_delivery_buttons' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' =>
                                            $this->_modifierConfig['form'] . '.' . $this->_modifierConfig['form']
                                            . '.'
                                            . $this->_groupContainer
                                            . '.'
                                            . $this->_modifierConfig['modal'],
                                        'actionName' => 'openModal',
                                    ],
                                    [
                                        'targetName' =>
                                            $this->_modifierConfig['form'] . '.' . $this->_modifierConfig['form']
                                            . '.'
                                            . $this->_groupContainer
                                            . '.'
                                            . $this->_modifierConfig['modal']
                                            . '.'
                                            . $this->_modifierConfig['listing'],
                                        'actionName' => 'render',
                                    ],
                                ],
                                'title' => __($this->_buttonTitle),
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $meta
     * @return array
     */
    private function customizeAddDeliveryModal(array $meta)
    {
        $meta['delivery_history']['children']['add_delivery_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'transferstock_request_form.transferstock_request_form_data_source',
            'options' => [
                'title' => __('Add Delivery'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Add Selected Products'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }.os_transferstock_warehouse_product_stock_listing',
                                'actionName' => 'save'
                            ],
                            [
                                'closeModal'
                            ]
                        ]
                    ]
                ],
            ],
        ];


        return $meta;
    }


    protected function getDeliveryHistoryListing(){
        $grid = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->_modifierConfig['history_listing'],
                        'ns' => $this->_modifierConfig['history_listing'],
                         'save_parameters_in_session' => 1,
                        'params' =>[
                            'id'=>$this->request->getParam('id')
                        ],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                    ],
                ],
            ],
        ];
        return $grid;
    }


    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        //return parent::modifyMeta($meta);

        $meta = array_replace_recursive(
            $meta,
            [
                $this->_groupContainer => [
                    'children' => $this->getModifierChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->_groupLabel),
                                'collapsible' => true,
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->_sortOrder
                            ],
                        ],
                    ],
                ],
            ]
        );

        //$meta = $this->customizeAddDeliveryModal($meta);

        //\Zend_Debug::dump($meta);
        return $meta;
    }

    /**
     * Fill meta columns
     *
     * @return array
     */
    protected function fillModifierMeta()
    {
        return [
            'id' => $this->getTextColumn('id', true, __('ID'), 10),
            'sku' => $this->getTextColumn('sku', false, __('SKU'), 20),
            'name' => $this->getTextColumn('name', false, __('Name'), 30),
            'qty' => $this->getTextColumn('qty', false, __('Qty in Warehouse'), 40),
            'request_qty' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'request_qty',
                            'label' => __('Qty'),
                            'fit' => true,
                            'additionalClasses' => 'admin__field-small',
                            'sortOrder' => 50,
                            'validation' => [
                                'validate-number' => true,
                            ],
                        ],
                    ],
                ],
            ],

            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 60,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'componentType' => Form\Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 70,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }
}
