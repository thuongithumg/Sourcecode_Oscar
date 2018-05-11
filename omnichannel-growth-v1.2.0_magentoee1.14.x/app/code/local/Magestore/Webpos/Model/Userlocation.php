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
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:53 SA
 *
 * Edited by NetBeans.
 * User: Daniel
 * Date: 21/01/2016
 * Time: 05:49 PM
 */
class Magestore_Webpos_Model_Userlocation extends Mage_Core_Model_Abstract {

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_location';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'data_object';

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/userlocation');
    }

    public function toOptionArray() {
        $options = array();
        $locationCollection = $this->getCollection();
        if ($locationCollection->getSize() > 1) {
            $options = array('' => '---Select Location---');
        }
        foreach ($locationCollection as $location) {
            $key = $location->getLocationId();
            $value = $location->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }

    public function getOptionArray()
    {
        $options = array();
        $locationCollection = $this->getCollection();
        foreach ($locationCollection as $location) {
            $options[] = array(
                'value' => $location->getLocationId(),
                'label' => $location->getDisplayName()
            );
        }
        return $options;
    }

}
