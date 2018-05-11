<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Shift\Shift\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Shift\Shift\Grid\Collection
 * 
 * Web POS Shift Grid Collection resource model
 * Methods:
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends SearchResult
{
    public function getData()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $data = parent::getData();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $om->get('Magento\Framework\App\RequestInterface');
        if($request->getParam('is_export')) {
            $staffData = $om->get('Magestore\Webpos\Ui\Component\Listing\Column\Staff')
                ->getOptionArray();
            $posData = $om->get('Magestore\Webpos\Ui\Component\Listing\Column\Pos')
                ->getOptionArray();
            foreach ($data as &$item) {
                $item['staff_id'] = $staffData[$item['staff_id']];
                $item['pos_id'] = $posData[$item['pos_id']];
            }
        }
        return $data;
    }
}