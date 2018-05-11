<?php

/**
 * Class Magestore_Inventorysuccess_Model_Api2_Abstract
 */
abstract class Magestore_Inventorysuccess_Model_Api2_Abstract extends
    Mage_Api2_Model_Resource
{
    const PAGE_SIZE_DEFAULT = 1000;
    const PAGE_SIZE_MAX     = 10000;


    /**
     * Set navigation parameters and apply filters from URL params
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource
     */
    protected function _applyCollectionModifiersNew( Varien_Data_Collection_Db $collection )
    {
        $pageNumber = $this->getRequest()->getPageNumber();
        if ( $pageNumber != abs($pageNumber) ) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ( null == $pageSize ) {
            $pageSize = self::PAGE_SIZE_DEFAULT;
        } else {
            if ( $pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX ) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }

        $orderField = $this->getRequest()->getOrderField();

        if ( null !== $orderField ) {
            if ( !is_string($orderField) ) {
                $this->_critical(self::RESOURCE_COLLECTION_ORDERING_ERROR);
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);

        return $this->_applyFilter($collection);
    }

}
