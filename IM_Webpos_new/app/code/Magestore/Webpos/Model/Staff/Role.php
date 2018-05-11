<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff;

/**
 * class \Magestore\Webpos\Model\Staff\Role
 *
 * Web POS Role model
 * Use to work with Web POS role table
 * Methods:
 *  getValuesForForm
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Role extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Staff\Role');
    }

    /**
     * get location list for form select element
     * return array
     */
    public function getValuesForForm(){
        $collection = $this->getCollection();
        $options = array();
        if($collection->getSize() > 0){
            foreach ($collection as $role){
                $options[] = array('value' => $role->getId(), 'label' => $role->getData('display_name'));
            }
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getHashOption() {
        $options = array();
        $collection = $this->getCollection();
        foreach ($collection as $role) {
            $options[$role->getId()] = $role['display_name'];
        }
        return $options;
    }

}