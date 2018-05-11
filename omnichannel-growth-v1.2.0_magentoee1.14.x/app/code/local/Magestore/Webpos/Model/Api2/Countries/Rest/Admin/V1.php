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

class Magestore_Webpos_Model_Api2_Countries_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{
    /**
     * Get countries information
     *
     * @api
     * @return array|null
     */
    public function getList()
    {
        $countryConfig = Mage::getModel('webpos/config_country')->getList();
        $configurations = array();
        if(count($countryConfig)) {
            foreach($countryConfig as $key => $data) {
                if(is_array($data)) {
                    //$data = \Zend_Json::encode($data);
                }
                $configurations[] = $data;
            }
        }

        $result = array(
            'items' => $configurations,
            'total_count' => count($configurations)
        );

        return $result;
    }

    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            /* Create */
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_RETRIEVE:
                $this->_errorIfMethodNotExist('getList');
                $retrievedData = $this->getList();
                $this->_render($retrievedData);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }
}
