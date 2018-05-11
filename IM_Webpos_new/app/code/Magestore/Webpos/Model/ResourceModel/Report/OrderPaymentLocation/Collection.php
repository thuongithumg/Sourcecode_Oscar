<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Report\OrderPaymentLocation;

/**
 * Report order collection
 *
 * @author      Magestore Developer
 */
class Collection extends \Magestore\Webpos\Model\ResourceModel\Report\Collection
{
     /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_firstColumnGroup = 'location.location_id';
        $this->_secondColumnGroup = 'payment.method';
        $this->_thirdColumnGroup = 'increment_id';
    }

}