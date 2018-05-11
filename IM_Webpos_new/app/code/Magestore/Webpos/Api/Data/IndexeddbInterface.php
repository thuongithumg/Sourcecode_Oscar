<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/06/2016
 * Time: 09:21
 */

namespace Magestore\Webpos\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface IndexeddbInterface extends ExtensibleDataInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const INDEXEDDB_ID = "indexeddb_id";



    /**
     *  indexeddb_id
     * @return string|null
     */
    public function getIndexeddbId();


    /**
     * Set indexeddb_id
     *
     * @param string $indexeddb_id
     * @return $this
     */
    public function setIndexeddbId($indexeddbId);

}