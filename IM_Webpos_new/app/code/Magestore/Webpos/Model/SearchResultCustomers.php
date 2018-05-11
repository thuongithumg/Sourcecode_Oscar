<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model;

use Magento\Framework\Model\AbstractModel;

class SearchResultCustomers extends AbstractModel
{
    const CACHE_ID = 'SearchResultCustomer';
    /**
     * construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\Webpos\Model\ResourceModel\SearchResultCustomers');
    }



}