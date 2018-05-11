<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Staff\Staff\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Staff\Staff\Grid\Collection
 *
 * Web POS Staff Grid Collection resource model
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends SearchResult
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public function getData()
    {
        $data = parent::getData();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $requestInterface = $objectManager->get('Magento\Framework\App\RequestInterface');
        if( ($requestInterface->getActionName() == 'gridToCsv') || ($requestInterface->getActionName() == 'gridToXml')){
            $options = array(
                self::STATUS_ENABLED => __('Enabled'),
                self::STATUS_DISABLED => __('Disabled')
            );
            $locationOptions = $objectManager->get('Magestore\Webpos\Model\Location\Location')->getHashOption();
            $roleOptions = $objectManager->get('Magestore\Webpos\Model\Staff\Role')->getHashOption();
            foreach ($data as &$item) {
                if($item['status']) {
                    $item['status'] = $options[$item['status']];
                }
                if($item['location_id']) {
                    $locationArray = explode(',', $item['location_id']);
                    $locationNameArray = array();
                    foreach ($locationArray as $locationId) {
                        if (isset($locationOptions[$locationId])) {
                            $locationName = $locationOptions[$locationId];
                            $locationNameArray[] = $locationName;
                        }
                    }
                    $item['location_id'] = implode(',', $locationNameArray);
                }
                if($item['role_id']) {
                    $item['role_id'] = $roleOptions[$item['role_id']];
                }
            }
        }
        return $data;
    }
}