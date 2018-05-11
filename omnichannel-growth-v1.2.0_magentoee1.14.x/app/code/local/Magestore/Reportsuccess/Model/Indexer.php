<?php

/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * class Magestore_Reportsuccess_Model_Indexer
 */

class Magestore_Reportsuccess_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'reportsuccess_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = array(
            Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('reportsuccess/historics');
        //$this->_init('reportsuccess/indexer');
    }

    /**
     * Retrieve Indexer name
     * @return string
     */
    public function getName()
    {
        return 'Historical Inventory Report';
    }

    /**
     * Retrieve Indexer description
     * @return string
     */
    public function getDescription()
    {
        return 'Build history inventory report data';
    }

    /**
     * Register data required by process in event object
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $dataObj = $event->getDataObject();
        if($event->getType() == Mage_Index_Model_Event::TYPE_REINDEX){
            $event->addNewData(self::EVENT_MATCH_RESULT_KEY, 1000);
        }
    }

    /**
     * Process event
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        // run if setting = Updated on Save
        $data = $event->getNewData();
        if(!empty($data[self::EVENT_MATCH_RESULT_KEY])){
           // $this->doSomethingOnUpdateEvent();
           $this->reindexAll();
        }
    }

    /**
     * match whether the reindexing should be fired
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }
        $entity = $event->getEntity();
        $result = true;
        if($entity != Magestore_Reportsuccess_Model_Observer::ENTITY){
            return;
        }
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);
        return $result;
    }

}

