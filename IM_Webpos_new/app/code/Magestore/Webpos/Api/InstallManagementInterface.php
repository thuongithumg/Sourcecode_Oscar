<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api;

interface InstallManagementInterface {
    /**
     * convert data warehouse_id from sale_order into sales_order_grid
     *
     * @return \Magestore\Webpos\Api\InstallManagementInterface
     */
    public function convertSaleItemsData();

    /**
     * Create index table for synchronization step 
     *
     * @param string
     * @return null
     */
    public function createIndexTable($type);

    /**
     * convert data warehouse_id from sale_order into sales_order_grid
     *
     * @param string
     * @return null
     */
    public function addIndexTableData($type);
}