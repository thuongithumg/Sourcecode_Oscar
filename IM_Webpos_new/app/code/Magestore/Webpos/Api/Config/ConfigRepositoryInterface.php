<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Config;

/**
 * Config repository interface.
 *
 * An order is a document that a web store issues to a customer. Magento generates a sales order that lists the product
 * items, billing and shipping addresses, and shipping and payment methods. A corresponding external document, known as
 * a purchase order, is emailed to the customer.
 * @api
 */
interface ConfigRepositoryInterface
{
    /**
     *
     * @param
     * @return \Magestore\Webpos\Api\Data\Config\ConfigResultInterface
     */
    public function getList();

    /**
     *
     * @param string $path
     * @return string
     */
    public function getConfigByPath($path);

}
