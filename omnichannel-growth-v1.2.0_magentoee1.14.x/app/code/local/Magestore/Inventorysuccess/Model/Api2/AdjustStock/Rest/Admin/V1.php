<?php

class Magestore_Inventorysuccess_Model_Api2_AdjustStock_Rest_Admin_V1 extends
    Magestore_Inventorysuccess_Model_Api2_Abstract
{
    const ACTION_TYPE_RETRIEVE = 'retrieve';
    const ACTION_TYPE_CREATE   = 'create';

    public function dispatch()
    {
        switch ( $this->getActionType() ) {
            case self::ACTION_TYPE_RETRIEVE:
                if ( $this->getRequest()->isGet() ) {
                    $adjustStockCode = $this->getRequest()->getParam('adjustStockCode');
                    $result          = $this->getAdjustStock($adjustStockCode);
                }
                break;
            case self::ACTION_TYPE_CREATE:
                if ( $this->getRequest()->isPost() ) {
                    $data   = $this->getRequest()->getBodyParams();
                    $result = $this->createAdjustStock($data);
                }
                break;
            default:
                $result = array();
        }
        $this->_render($result);
        $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
    }

    /**
     * @param $adjustStockCode
     * @return array
     */
    public function getAdjustStock( $adjustStockCode )
    {
        $collection = Mage::getResourceModel('inventorysuccess/adjustStock_collection')
                          ->addFieldToFilter(Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE, $adjustStockCode);
        $data       = $collection->getFirstItem()->getData();
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Adjuststock_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('inventorysuccess/adjustStock_product_collection')
                                 ->addFieldToFilter(Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_ID, $data[Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_ID]);
        $data['products']  = $productCollection->load()->toArray();
        return $data;
    }


    /**
     * @param $data
     * @return array
     * @throws Exception
     */
    public function createAdjustStock( $data )
    {
        $input = new Varien_Object($data);

        /** @var Magestore_Inventorysuccess_Model_Adjuststock $newAdjustStock */
        $newAdjustStock = Mage::getModel('inventorysuccess/adjustStock');
        /** check if adjust stock exists */
        if ( $input->getAdjuststockCode() ) {
            $newAdjustStock->load($input->getAdjuststockCode(),
                                  Magestore_Inventorysuccess_Model_Adjuststock::ADJUSTSTOCK_CODE);
        }
        if ( $newAdjustStock->getAdjuststockId() ) {
            throw new \Exception('The Adjust Stock with code ' . $newAdjustStock->getAdjuststockCode() . ' already exists.');
        }

        /** save adjust stock */
        try {
            $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
            $newAdjustStock->addData($input->getData());
            $newAdjustStock = $adjustStockService->createAdjustment($newAdjustStock, $input);
            if ( $newAdjustStock->getId() ) {
                if ( $input->getAction() == 'complete' ) {
                    $adjustStockService->complete($newAdjustStock);
                }
            }
        } catch ( \Exception $e ) {
            throw new \Exception($e->getMessage());
        }
        return $this->getAdjustStock($newAdjustStock->getAdjustStockCode());
    }


}
