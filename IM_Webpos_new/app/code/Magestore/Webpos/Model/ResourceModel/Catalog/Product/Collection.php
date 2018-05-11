<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magestore\Webpos\Model\ResourceModel\Catalog\Product;


/**
 * Product collection
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @method \Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface getResource()
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const   VISIBLE_ON_WEBPOS = 1;

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('Magestore\Webpos\Model\Catalog\Product', 'Magento\Catalog\Model\ResourceModel\Product\Flat');
        } else {
            $this->_init('Magestore\Webpos\Model\Catalog\Product', 'Magento\Catalog\Model\ResourceModel\Product');
        }
        $this->_initTables();
    }
    
    /**
     * filter product collection that visible on webpos
     * @return \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection
     */
    public function addVisibleFilter(){
        $this->addAttributeToFilter([
            ['attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'],
            ['attribute' => 'webpos_visible', 'eq' => self::VISIBLE_ON_WEBPOS, 'left'],
        ],'', 'left');
        return $this;
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelect();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }
}
