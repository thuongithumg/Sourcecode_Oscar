<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Rewardpoints;

use \Magento\Framework\ObjectManagerInterface as ObjectManagerInterface;


use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;


/**
 * Class StockItemRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RateRepository implements \Magestore\Webpos\Api\Integration\Rewardpoints\RateRepositoryInterface {

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface  
     */
    protected $_objectManager;
    
    /**
     *
     * @var \Magento\Framework\App\RequestInterface 
     */
    protected $_request;
    
    /**
     * 
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    
    /**
     *
     * @var \Magestore\Webpos\Helper\Permission 
     */
    protected $_permissionHelper;
    
    public function __construct(
         \Magento\Framework\App\RequestInterface $request,  
        \Magestore\Webpos\Helper\Permission $permissionHelper,    
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_permissionHelper = $permissionHelper;
        $this->_request = $request;
        $this->_moduleManager = $moduleManager;
    }
    
    /**
     * @inheritdoc
     */
    public function getList() {
        if (!$this->_moduleManager->isEnabled('Magestore_Rewardpoints')) {
            throw new NoSuchEntityException(__('Rate is not available'));
        }        
        
        $rates = $this->_objectManager->get('Magestore\Rewardpoints\Model\Rate')
                            ->getCollection()
                            ->addFieldToFilter('status','1');
        $rates->load();
        $searchResult = $this->_objectManager->get('Magento\Framework\Api\Search\SearchResultFactory')->create();
        $searchResult->setItems($rates->getItems());
        $searchResult->setTotalCount($rates->getSize());
        return $searchResult;
    }

}