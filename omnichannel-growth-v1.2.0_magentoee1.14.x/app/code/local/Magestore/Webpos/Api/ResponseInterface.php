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
 * Interface Magestore_Webpos_Api_ResponseInterface
 */
interface Magestore_Webpos_Api_ResponseInterface
{
    /**#@+
     * Data key
     */
    const STATUS = 'status';
    const MESSAGES = 'messages';
    const DATA = 'data';
    /**#@- */

    /**#@+
     * Status
     */
    const STATUS_WARNING = '2';
    const STATUS_SUCCESS = '1';
    const STATUS_ERROR = '0';
    /**#@- */

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return string
     */
    public function setStatus($status);

    /**
     * @return array
     */
    public function getMessages();

    /**
     * @param array $message
     * @return array
     */
    public function setMessages($message);

    /**
     * @return array
     */
    public function getResponseData();

    /**
     * @param array $data
     * @return array
     */
    public function setResponseData($data);
}
