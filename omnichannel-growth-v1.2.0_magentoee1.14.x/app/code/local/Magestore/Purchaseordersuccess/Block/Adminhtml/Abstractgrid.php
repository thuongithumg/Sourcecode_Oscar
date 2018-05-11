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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Abstractgrid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterDateCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if ($column->getType() == 'date') {
            if (isset($value['from'])) {
                $collection->addFieldToFilter(
                    $column->getIndex(),
                    array(
                        'gteq' => $value['from']->set(
                                $value['orig_from'], Zend_Date::DATE_SHORT, $value['locale']
                            )->toString('Y-M-d') . ' 00:00:00'
                    )
                );
            }
            if (isset($value['to'])) {
                $collection->addFieldToFilter(
                    $column->getIndex(),
                    array(
                        'lteq' => $value['to']->set(
                                $value['orig_to'], Zend_Date::DATE_SHORT, $value['locale']
                            )->toString('Y-M-d') . ' 23:59:59'
                    )
                );
            }
        }
        return $collection;
    }
}