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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Inventorysuccess_Model_Observer_Integration_Webpos_LocationPrepareContainer
{
    /**
     *
     * @param type $observer
     * @return $this
     */
    public function execute($observer)
    {
        $container = $observer->getEvent()->getContainer();
        $buttons = $container->getButtons();
        $buttons[] = array(
            'key'   => 'mapping',
            'class' => 'add',
            'label' => Mage::helper('inventorysuccess')->__('Mapping Locations - Warehouses'),
            'url'   => '*/inventorysuccess_warehouse_location/mapping'
        );
        $container->setButtons($buttons);
        return $this;
    }

}