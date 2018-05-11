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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_FilterCollection_Filter
{

    protected $_attribute_code = array('status' => 'value',
//        'sku'=>'sku',
//        'name'=>'value',
        );
    static function _transfer_stock_status(){
        return array(
            "pending" => "Pending",
            "processing" =>"Processing",
            "canceled" =>"Canceled",
            "completed" =>"Completed");
    }
    public function filterStatus($collection,$columnId,$value){
            $field = $columnId."_table";
            return $collection->getSelect()->where($field.'.value = ?', $value);
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function mappingAttribute($collection,$flag = false){
            $main_id = "main_table.product_id";
            if($flag){
                $main_id = "$flag";
            }
            $attributeCode = $this->_attribute_code;
            foreach($attributeCode as $code => $value){
                $alias = $code . '_table';
                $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $code);
                if(($code == 'name') || ($code == 'status')){
                    $collection->getSelect()->join(
                        array($alias => $attribute->getBackendTable()),
                        "$main_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()} AND $alias.store_id=0",
                        array($code => $value)
                    );
                }else{
                    $collection->getSelect()->join(
                        array($alias => $attribute->getBackendTable()),
                        "$main_id = $alias.entity_id",
                        array($code => $value)
                    );
                }
            }
            return $collection;
    }
}