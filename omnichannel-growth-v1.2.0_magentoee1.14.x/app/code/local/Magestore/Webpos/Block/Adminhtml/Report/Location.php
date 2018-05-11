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
 * Marketingautomation Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Marketingautomation
 * @author      Magestore Developer
 */
class Magestore_Webpos_Block_Adminhtml_Report_Location extends Magestore_Webpos_Block_Adminhtml_Report_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportGrid');
//        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $locationIds = array();

        $locationCollection = Mage::getModel('webpos/userlocation')->getCollection();
        if (count($locationCollection) > 0) {
            foreach ($locationCollection as $location) {
                $locationIds[$location->getId()] = $location->getDisplayName();
            }
        }
        $this->locationIds = $locationIds;
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Contact_Grid
     */
    protected function _prepareCollection()
    {
        $session = Mage::getModel('core/session');
        $filterConditions = $session->getData('rp_conditions');
        if (!$filterConditions) $filterConditions = $this->_filterConditions;
        $posorderCollection = Mage::getModel('webpos/posorder')->getCollection();
        /* Jack - create an empty collection */
        $collection = $posorderCollection;
        foreach ($collection->getItems() as $key => $item) {
            $collection->removeItemByKey($key);
        }
        /**/

        /* Define variables and set default data */
        $totalSales = 0;
        $incTimeStrings = array('2' => ' +1 day', '3' => ' +1 month', '4' => ' +1 day', '5' => ' +1 day', '6' => ' +1 week', '7' => ' +1 week');
        $periodFormat = ($filterConditions['period'] == 3 || $filterConditions['period'] == 6 || $filterConditions['period'] == 7) ? "Y-m-d" : "Y-m-d";
        $specialPeriod = array('1', '2', '3', '4');
        $stringTimeFrom = array('1' => 'monday this week', '2' => 'monday last week', '3' => 'first day of this month', '4' => 'first day of previous month');
        $stringTimeTo = array('1' => 'sunday this week', '2' => 'sunday last week', '3' => 'last day of this month', '4' => 'last day of previous month');
        if (($filterConditions['from'] == '' || $filterConditions['to'] == '') && in_array($filterConditions['period'], $specialPeriod) == false) {
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
        $startTime = (in_array($filterConditions['range'], $specialPeriod)) ? (date($periodFormat, strtotime($stringTimeFrom[$filterConditions['range']]))) : date($periodFormat, strtotime($filterConditions['from']));
        $timeFrom = (in_array($filterConditions['range'], $specialPeriod)) ? (date($periodFormat, strtotime($stringTimeFrom[$filterConditions['range']]))) : date($periodFormat, strtotime($filterConditions['from']));
        $endTime = $timeTo = (in_array($filterConditions['range'], $specialPeriod)) ? (date($periodFormat, strtotime($stringTimeTo[$filterConditions['range']]))) : date("Y-m-d", strtotime($filterConditions['to']));
        $lastTime = $timeFrom;
        if ($timeFrom && $timeTo) {
            while ($timeFrom <= $timeTo) {
                $i = 0;
                foreach ($this->locationIds as $locationId => $displayName) {
                    $isSave = true;

                    if ($filterConditions['period'] == 1) {
                        $endTime = $this->lastDayOf('year', new DateTime($timeFrom))->format('Y-m-d');
                    } else if ($filterConditions['period'] == 3) {
                        $endTime = $this->lastDayOf('month', new DateTime($timeFrom))->format('Y-m-d');
                    } else{
                        $endTime = date('Y-m-d', strtotime($timeFrom . $incTimeStrings[$filterConditions['period']]));
                    }
                    $endTime = (strtotime($endTime) <= strtotime($timeTo)) ? $endTime : $timeTo;
                    $itemDataObject = new Varien_Object();
                    if ($i == 0) {
                        if ($filterConditions['period'] == 1) {
                            $exTimeFrom = explode('-', $timeFrom);
                            $itemDataObject->setData('period', $exTimeFrom[0]);
                        } else if ($filterConditions['period'] == 3) {
                            $exTimeFrom = explode('-', $timeFrom);
                            $itemDataObject->setData('period', $exTimeFrom[0] . '-' . $exTimeFrom[1]);
                        } else
                            $itemDataObject->setData('period', $timeFrom);
                    } else
                        $itemDataObject->setData('period', '');
                    if ($itemDataObject->getData('period'))
                        $itemDataObject->setData('period', date('F j, Y', strtotime($itemDataObject->getData('period'))));
                    $webposOrderCollection = $this->getSalesCollection($timeFrom, $endTime, array('location_id' => $locationId));
                    $totalU = 0;
                    if (count($webposOrderCollection) > 0) {
                        foreach ($webposOrderCollection as $order) {
                            $totalU += $order->getTotals();
                        }
                    }
                    $totalSales += $totalU;
                    if (!$totalU && $filterConditions['rp_settings']['show_empty_result'] == 'false')
                        $isSave = false;
                    if ($isSave) {
                        $itemDataObject->setData('location', $displayName);

                        $itemDataObject->setData('totals_sales', ($totalU > 0) ? $totalU : '0.00');
                        $collection->addItem($itemDataObject);
                    }
                    $i++;
                }
                if ($filterConditions['period'] == 1 || $filterConditions['period'] == 3){
                    $timeFrom = date('Y-m-d', strtotime($endTime. ' +1 day'));
                }
                else
                    $timeFrom = date($periodFormat, strtotime($timeFrom . $incTimeStrings[$filterConditions['period']]));

                if ($timeTo < $timeFrom) {
                    break;
                }else{
                    $lastTime = $timeFrom;
                }
            }
            /* get last item data
            $i = 0;
            foreach($this->locationIds as $locationId => $displayName){
                $isSave = true;
                $beforeLastItem = new Varien_Object();
                if($i == 0){
                    if($filterConditions['period'] == 1){
                        $exTimeFrom = explode('-',$timeTo);
                        $beforeLastItem->setData('period',$exTimeFrom[0]);
                    }
                    else if($filterConditions['period'] == 3){
                        $exTimeFrom = explode('-',$timeTo);
                        $beforeLastItem->setData('period',$exTimeFrom[0].'-'.$exTimeFrom[1]);
                    }
                    else
                        $beforeLastItem->setData('period',$timeTo);
                }
                else
                    $beforeLastItem->setData('period','');
                if($beforeLastItem->getData('period'))
                    $beforeLastItem->setData('period',date('F j, Y',  strtotime($beforeLastItem->getData('period'))));
                $webposOrderCollection = $this->getSalesCollection($timeTo,$timeTo,array('user_location_id' => $locationId))->getFirstItem();
                if( !$webposOrderCollection->getTotals() && $filterConditions['rp_settings']['show_empty_result'] == 'false')
                    $isSave = false;
                if($isSave){
                    $beforeLastItem->setData('location',$displayName);
                    $beforeLastItem->setData('totals_sales',$webposOrderCollection->getTotals()?$webposOrderCollection->getTotals():'0.00');
                    $collection->addItem($beforeLastItem);
                }
                $i++;
           }
           end last item*/
        }
        /* set data for totals row */
        $lastItemDataObject = new Varien_Object();
        $lastItemDataObject->setData('period', 'Totals:');
        $lastItemDataObject->setData('totals_sales', $totalSales);
        $collection->addItem($lastItemDataObject);
        $this->setCollection($collection);
        /*set session for chart*/
        $sessionObject = new Varien_Object();
        foreach ($this->locationIds as $locationId => $locationName) {
            $orders = $this->getSalesTotal($startTime, $lastTime, array('location_id' => $locationId));


            $totalU = 0;
            if (count($orders) > 0) {
                foreach ($orders as $order) {
                    $totalU += $order->getTotals();
                }
            }
            $sessionObject->setData($locationName, $totalU);
        }
        Mage::getSingleton('core/session')->setData('total_sales', $sessionObject->toArray());
        Mage::getSingleton('core/session')->setType('location');
        $this->setTotalRow($sessionObject->toArray());
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Marketingautomation_Block_Adminhtml_Contact_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header' => Mage::helper('webpos')->__('Period'),
            'align' => 'left',
            'total' => 'sum',
            'sortable' => false,
            'filter' => false,
            'index' => 'period',
            'width' => '200px',
        ));
        $this->addColumn('location', array(
            'header' => Mage::helper('webpos')->__('Location'),
            'align' => 'left',
            'total' => 'sum',
            'sortable' => false,
            'filter' => false,
            'index' => 'location',
            'width' => '400px',
        ));
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);
        $this->addColumn('total_paid', array(
            'header'    => Mage::helper('sales')->__('Sales Total'),
            'index'     => 'total_paid',
            'type'      => 'currency',
            'total'     => 'sum',
            'sortable'  => false,
            'currency_code' => $currencyCode,
            'rate'          => $rate
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('webpos')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('webpos')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
