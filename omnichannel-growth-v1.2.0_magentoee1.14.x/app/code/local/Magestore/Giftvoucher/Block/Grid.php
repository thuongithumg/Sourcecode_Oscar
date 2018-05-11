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
 * Giftvoucher Grid block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_Grid extends Mage_Core_Block_Template
{

    /**
     * Columns of Grid
     *
     * @var array
     * 
     * Example of element:
     * $_columns['id'] = array(
     * 	'header'	=> 'ID',
     * 	'align'		=> 'right',
     * 	'width'		=> '50px',
     * 	'index'		=> 'account_id',
     * 	'type'		=> 'date' | 'options' | 'action' | 'datetime' | 'price' | 'baseprice'
     * 	'format'	=> 'medium',
     * 	'options'	=> array( 'value' => 'label'),
     * 	'action'	=> array(
     * 					'label' => 'Edit',
     * 					'url' 	=> 'affiliateplus/index/index',
     * 					'name'	=> 'id',
     * 					'field'	=> 'account_id',
     * 					),
     * 	'render'	=> 'function_name_of_parent_block(row)',
     *  'searchable'    => true | false,
     *  'filter_index'  => 'main_table.account_id'
     *  'filter_function'   => 'function_name_of_parent_block(collection, filterValues)'
     * );
     */
    protected $_columns = array();

    /**
     * Grid's Collection
     */
    protected $_collection;

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * 
     * @param set collection and apply filter $collection
     * @return Magestore_Giftvoucher_Block_Grid
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        if (!$this->getData('add_searchable_row')) {
            return $this;
        }
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($column['searchable']) && $column['searchable']) {
                if (isset($column['filter_function']) && $column['filter_function']) {
                    $this->fetchFilter($column['filter_function']);
                } else {
                    $field = isset($column['index']) ? $column['index'] : $columnId;
                    $field = isset($column['filter_index']) ? $column['filter_index'] : $field;
                    if ($filterValue = $this->getFilterValue($columnId)) {
                        $this->_collection->addFieldToFilter($field, array('like' => "%$filterValue%"));
                    }
                    if ($filterValue = $this->getFilterValue($columnId, '-from')) {
                        if ($column['type'] == 'price') {
                            $store = Mage::app()->getStore();
                            $filterValue /= $store->getBaseCurrency()->convert(1, $store->getCurrentCurrency());
                        } elseif ($column['type'] == 'date' || $column['type'] == 'datetime') {
                            $filterValue = date('Y-m-d H:i:s', strtotime($filterValue) + 3600);
                        }
                        $this->_collection->addFieldToFilter($field, array('gteq' => $filterValue));
                    }
                    $filterValue = $this->getFilterValue($columnId, '-to');
                    if ($filterValue || $this->getFilterValue($columnId, '-to') == '0') {
                        if ($column['type'] == 'price') {
                            $store = Mage::app()->getStore();
                            $filterValue /= $store->getBaseCurrency()->convert(1, $store->getCurrentCurrency());
                        } elseif ($column['type'] == 'date' || $column['type'] == 'datetime') {
                            $filterValue = date('Y-m-d H:i:s', strtotime($filterValue) + 90000);
                        }
                        $this->_collection->addFieldToFilter($field, array('lteq' => $filterValue));
                    }
                }
            }
        }
        return $this;
    }

    /**
     * fetch filter value function
     * 
     * @param null|int $columnId
     * @param string $offset
     * @return mixed
     */
    public function getFilterValue($columnId = null, $offset = '')
    {
        if (!$this->hasData('filter_value')) {
            if ($filter = $this->getRequest()->getParam('filter')) {
                $filter = Mage::helper('core')->urlDecode($filter);
                parse_str($filter, $filter);
            }
            $this->setData('filter_value', $filter);
        }
        if (is_null($columnId)) {
            return $this->getData('filter_value');
        } else {
            return $this->getData('filter_value/' . $columnId . $offset);
        }
    }

    /**
     * fetch filter custom function
     * 
     * @param string $parentFuction
     * @return mixed
     */
    public function fetchFilter($parentFuction)
    {
        $parentBlock = $this->getParentBlock();
        return $parentBlock->$parentFuction($this->_collection, $this->getFilterValue());
    }

    /**
     * @return mixed
     */
    public function getFilterUrl()
    {
        if (!$this->hasData('filter_url')) {
            $this->setData('filter_url', $this->getUrl('*/*/*'));
        }
        return $this->getData('filter_url');
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->getData('add_searchable_row')) {
            return $this->getParentBlock()->getPagerHtml();
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('giftvoucher/grid.phtml');
        return $this;
    }

    /**
     * Add new Column to Grid
     *
     * @param string $columnId
     * @param array $params
     * @return Magestore_Giftvoucher_Block_Grid
     */
    public function addColumn($columnId, $params)
    {
        if (isset($params['searchable']) && $params['searchable']) {
            $this->setData('add_searchable_row', true);
            if (isset($params['type']) && ($params['type'] == 'date' || $params['type'] == 'datetime')) {
                $this->setData('add_calendar_js_to_grid', true);
            }
        }
        $this->_columns[$columnId] = $params;
        return $this;
    }

    /**
     * Call Render Function
     *
     * @param string $parentFunction
     * @param mixed $row
     * @return string
     */
    public function fetchRender($parentFunction, $row)
    {
        $parentBlock = $this->getParentBlock();

        $fetchObj = new Varien_Object(array(
            'function' => $parentFunction,
            'html' => false,
        ));

        if ($fetchObj->getHtml()) {
            return $fetchObj->getHtml();
        }

        return $parentBlock->$parentFunction($row);
    }

}
