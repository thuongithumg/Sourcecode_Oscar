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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Historics_Grid
 */

use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Historics_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     *
     */
    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('backupsGrid');
        $this->setDefaultSort('time', 'desc');
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('reportsuccess/fs_collection');
        $warehouseId = Mage::getResourceModel('reportsuccess/costofgood_collection')->getWarehouseIds();
        $warehouseId[] = '1';
        $collection->addFieldToFilter('display_name', array('in' => $warehouseId));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare mass action controls
     *
     * @return Mage_Adminhtml_Block_Backup_Grid
     */
//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('id');
//        $this->getMassactionBlock()->setFormFieldName('ids');
//        $this->getMassactionBlock()->addItem('delete', array(
//            'label'=> Mage::helper('adminhtml')->__('Delete'),
//            'url'  => $this->getUrl('*/*/massDelete'),
//            'confirm' => Mage::helper('backup')->__('Are you sure you want to delete the selected backup(s)?')
//        ));
//        return $this;
//    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Backup_Grid
     */
    protected function _prepareColumns()
    {
        $url7zip = Mage::helper('adminhtml')->__('The archive can be uncompressed with <a href="%s">%s</a> on Windows systems', 'http://www.7-zip.org/', '7-Zip');

        $this->addColumn('time', array(
            'header'    => Mage::helper('reportsuccess')->__('Date'),
            'index'     => 'date_object',
            'type'      => 'date',
            'width'     => '20%'
        ));

        $warehouseoptions = Magestore_Reportsuccess_Model_Mysql4_Costofgood_Collection::getWarehouseIdsxName();
        $warehouseoptions[1] = 'All';
        $this->addColumn('display_name', array(
            'header'    => Mage::helper('reportsuccess')->__('Warehouse Name'),
            'index'     => 'display_name',
            'sortable'  => true,
            'type'      =>  'options',
            'options'   =>  $warehouseoptions,
            'width'     => '20%'
        ));

        $this->addColumn('size', array(
            'header'    => Mage::helper('reportsuccess')->__('File Size (bytes)'),
            'index'     => 'size',
            'type'      => 'number',
            'sortable'  => true,
            'filter'    => false,
            'width'     => '20%'
        ));

        $this->addColumn('download', array(
            'header'    => Mage::helper('reportsuccess')->__('Download'),
            'format'    => '<a href="' . $this->getUrl('*/*/download', array('time' => '$time', 'type' => '$type'))
                . '">$extension</a> &nbsp; <small>('.$url7zip.')</small>',
            'index'     => 'type',
            'sortable'  => false,
            'filter'    => false
        ));
        return $this;
    }

    /**
     * @return mixed
     */
    public function editColumnUrl(){
        return  Mage::helper('adminhtml')->getUrl('adminhtml/dashboard/editColumns');
    }

}
