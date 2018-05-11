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
 * Interface Magestore_Webpos_Api_Cart_ItemRequestInterface
 */
interface Magestore_Webpos_Api_Cart_ItemRequestInterface
{
    /**#@+
     * Data key
     */
    const ID = 'id';
    const INFO_BUYREQUEST = 'info_buyRequest';
    /**#@- */

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return Magestore_Webpos_Api_Cart_ItemRequestInterface
     */
    public function setId($id);

    /**
     * @return Magestore_Webpos_Api_Cart_BuyRequestInterface
     */
    public function getBuyRequest();

    /**
     * @param Magestore_Webpos_Api_Cart_BuyRequestInterface $buyRequest
     * @return Magestore_Webpos_Api_Cart_ItemRequestInterface
     */
    public function setBuyRequest($buyRequest);
}
