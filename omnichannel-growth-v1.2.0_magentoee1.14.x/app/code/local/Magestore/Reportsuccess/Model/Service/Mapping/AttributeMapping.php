<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * ReportSuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Model_Service_Mapping_AttributeMapping
{
    /**
     * @var array
     */
    protected $_attribute_code = array(
        //'status' => 'value',
        'sku'=>'sku',
        'name'=>'value',);

    /**
     * @param $collection
     * @param $columnId
     * @param $value
     * @return mixed
     */
    public function attributeFillter($collection,$columnId,$value)
    {
        $field = $columnId.'_table';
        $metricsFilter = $this->_attribute_code[$columnId];
        return $collection->getSelect()->where($field.'.'.$metricsFilter.' like ?', '%'.$value.'%');
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function attributeMapping($collection)
    {
        $attributeCode = $this->_attribute_code;
        foreach($attributeCode as $code => $value){
            $alias = $code . '_table';
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $code);
            if(($code == 'name') || ($code == 'status')){
                $collection->getSelect()->join(
                    array($alias => $attribute->getBackendTable()),
                    "main_table.product_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()} AND $alias.store_id=0",
                    array($code => $value)
                );
            }else{
                $collection->getSelect()->join(
                    array($alias => $attribute->getBackendTable()),
                    "main_table.product_id = $alias.entity_id",
                    array($code => $value)
                );
            }
        }
        return $collection;
    }
}