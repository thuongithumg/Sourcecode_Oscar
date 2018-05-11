<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Location\Location;
/**
 * class \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection
 *
 * Web POS Location Collection resource model
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'location_id';
    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Location\Location',
            'Magestore\Webpos\Model\ResourceModel\Location\Location');
    }

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('location_id','display_name');
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'location_id') {
            $field = 'main_table.location_id';
        }
        return parent::addFieldToFilter($field, $condition);
    }
}