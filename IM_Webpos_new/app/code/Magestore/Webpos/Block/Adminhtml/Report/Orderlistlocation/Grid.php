<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Report\Orderlistlocation;

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
        $this->_columnGroupBy = 'location.display_name';  
        $this->_firstColumnReportKey = 'location.display_name';
        $this->_firstColumnReportName = 'Location';      
        $this->_secondColumnReportKey = 'increment_id';
        $this->_secondColumnReportName = 'Order ID';   
        $this->_isShowOrderNumber = false;     
        return parent::_construct();
    }

    /**
     * set resource model
     *
     * @return Magestore\Webpos\Model\ResourceModel\{collectionName}
     */
    public function getResourceCollectionName()
    {                
        return 'Magestore\Webpos\Model\ResourceModel\Report\OrderListLocation\Collection';
    }
}
