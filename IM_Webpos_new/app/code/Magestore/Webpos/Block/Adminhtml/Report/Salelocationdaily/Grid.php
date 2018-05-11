<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report\Salelocationdaily;

/**
 * Report grid container.
 * @category Magestore
 * @package  Magestore_Webpos
 * @module   Webpos
 * @author   Magestore Developer
 */
class Grid extends \Magestore\Webpos\Block\Adminhtml\Report\AbstractGrid
{

    /**
     * contructor
     */
    protected function _construct()
    {
        $this->_columnGroupBy = 'created_at';        
        $this->_timeColumnReportKey = 'created_at';        
        $this->_firstColumnReportKey = 'location.display_name';
        $this->_firstColumnReportName = 'Location';
        return parent::_construct();
    }

    /**
     * set resource model
     *
     * @return Magestore\Webpos\Model\ResourceModel\{collectionName}
     */
    public function getResourceCollectionName()
    {                
        return 'Magestore\Webpos\Model\ResourceModel\Report\SaleLocationDaily\Collection';
    }
}
