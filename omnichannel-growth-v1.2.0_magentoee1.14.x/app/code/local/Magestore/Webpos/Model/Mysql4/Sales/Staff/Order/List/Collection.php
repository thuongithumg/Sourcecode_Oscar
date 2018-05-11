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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Report order collection
 *
 * @author      Magestore Developer
 */
class  Magestore_Webpos_Model_Mysql4_Sales_Staff_Order_List_Collection extends Magestore_Webpos_Model_Mysql4_Collection
{
    /**
     * constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_applyFilters = 'period_type';
        $this->_firstColumnGroup = 'webpos_staff_id';
        $this->_secondColumnGroup = 'increment_id';
        parent::__construct();
    }

}