<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Catalog;

interface SwatchRepositoryInterface
{
    /**
     *
     * @param
     * @return \Magestore\Webpos\Api\Data\Catalog\SwatchResultInterface
     */
    public function getList();

}
