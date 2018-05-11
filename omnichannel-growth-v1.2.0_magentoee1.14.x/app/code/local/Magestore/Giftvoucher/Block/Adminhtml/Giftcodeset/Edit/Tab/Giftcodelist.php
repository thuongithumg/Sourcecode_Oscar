<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Adminhtml Giftvoucher Generategiftcard Edit Tab Giftcodelist Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Adminhtml_Giftcodeset_Edit_Tab_Giftcodelist
    extends Mage_Adminhtml_Block_Widget_Grid
{


    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Giftcodeset_Edit_Tab_Giftcodelist constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcodelistGrid');
        $this->setDefaultSort('giftvoucher_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('giftvoucher/giftvoucher')->getCollection()->addFieldToFilter('set_id', $id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Get the gift code's status options as array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            Magestore_Giftvoucher_Model_Giftvoucher::STATUS_YES => Mage::helper('giftvoucher')->__('Yes'),
            Magestore_Giftvoucher_Model_Giftvoucher::STATUS_NO => Mage::helper('giftvoucher')->__('No'),

        );
    }


    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('giftvoucher_id', array(
            'header' => Mage::helper('giftvoucher')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'giftvoucher_id',
            'filter_index' => 'giftvoucher_id'
        ));

        $this->addColumn('gift_code', array(
            'header' => Mage::helper('giftvoucher')->__('Gift Card Code'),
            'align' => 'left',
            'index' => 'gift_code',
            'filter_index' => 'gift_code'
        ));

        $this->addColumn('used',array(
            'header' => Mage::helper('giftvoucher')->__('Used'),
            'width' => '10px',
            'align' => 'left',
            'index' => 'used',
            'type' => 'options',
            'options' => $this->getOptionArray()
        ));
        $this->addColumn('action', array(
            'header' => __('Action'),
            'width' => '70px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => __('Edit'),
                    'url' => array('base' => '*/giftvoucher_giftvoucher/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }


    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/giftvoucher_giftvoucher/edit', array('id' => $row->getId()));
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/giftcodelist', array(
                '_current' => true,
        ));
    }

}
