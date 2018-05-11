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

class Magestore_Webpos_Block_Adminhtml_Report_Abstract extends Mage_Adminhtml_Block_Widget_Grid {
	    
	protected $_filterConditions = array('period' =>'','from' =>'','to' =>'');
	protected $locationIds = array();
        protected $userIds = array();
        public function setFilterCondition($conditions){
		$conditionLabels = array('range','period','order_statuses','from','to','rp_settings');
		foreach($conditions as $conditionLabel => $value){
			if(in_array($conditionLabel,$conditionLabels))
				$this->_filterConditions[$conditionLabel] = $value;
		}
		$session = Mage::getModel('core/session');
		$session->setData('rp_conditions',$this->_filterConditions);
	}
	
	public function getSalesCollection($timeFrom,$endTime,$contidions){
        if($timeFrom == $endTime){
            $endTime = $endTime.' 23:59:59';
        }else{
            $endTime = $endTime.' 00:00:00';
        }
        $timeFrom = $timeFrom.' 00:00:00';
		$posorderCollection = Mage::getModel('sales/order')->getCollection();
		$posorderCollection->addFieldToFilter('created_at',array('gteq' => $timeFrom));
		$posorderCollection->addFieldToFilter('created_at',array('lteq' => $endTime));
        /*order status*/
        $filterConditions = Mage::getModel('core/session')->getRpConditions();
        $orderStatuses = $filterConditions['order_statuses'];
        if(!empty($orderStatuses[0]))
            $posorderCollection->addFieldToFilter('status',array('in' => $orderStatuses));
        /**/
		if(is_array($contidions) && count($contidions) > 0)
		foreach($contidions as $fieldKey => $fieldValue){
			$posorderCollection->addFieldToFilter($fieldKey,$fieldValue);
		}
        $posorderCollection->getSelect()->columns(array(
                'totals' => 'SUM(grand_total)',
        ))->group('entity_id');
		return $posorderCollection;
	}
        public function getSalesTotal($timeFrom,$endTime,$contidions){
            if($timeFrom == $endTime){
                $endTime = $endTime.' 23:59:59';
            }else{
                $endTime = $endTime.' 00:00:00';
            }
            $timeFrom = $timeFrom.' 00:00:00';
            $posorderCollection = Mage::getModel('sales/order')->getCollection();
            $posorderCollection->addFieldToFilter('created_at',array('from' => $timeFrom,'to' => $endTime));
            /*order status*/
            $filterConditions = Mage::getModel('core/session')->getRpConditions();
            $orderStatuses = $filterConditions['order_statuses'];
            if(!empty($orderStatuses[0]))
                $posorderCollection->addFieldToFilter('status',array('in' => $orderStatuses));
            /**/
            if(is_array($contidions) && count($contidions) > 0)
            foreach($contidions as $fieldKey => $fieldValue){
                    $posorderCollection->addFieldToFilter($fieldKey,$fieldValue);
            }
            $posorderCollection->getSelect()->columns(array(
                    'totals' => 'SUM(grand_total)',
            ))->group('entity_id');
            return $posorderCollection;
        }
        function lastDayOf($period, DateTime $date = null)
        {
            $period = strtolower($period);
            $validPeriods = array('year', 'quarter', 'month', 'week');

            if ( ! in_array($period, $validPeriods))
                throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

            $newDate = ($date === null) ? new DateTime() : clone $date;

            switch ($period)
            {
                case 'year':
                    $newDate->modify('last day of december ' . $newDate->format('Y'));
                    break;
                case 'quarter':
                    $month = $newDate->format('n') ;

                    if ($month < 4) {
                        $newDate->modify('last day of march ' . $newDate->format('Y'));
                    } elseif ($month > 3 && $month < 7) {
                        $newDate->modify('last day of june ' . $newDate->format('Y'));
                    } elseif ($month > 6 && $month < 10) {
                        $newDate->modify('last day of september ' . $newDate->format('Y'));
                    } elseif ($month > 9) {
                        $newDate->modify('last day of december ' . $newDate->format('Y'));
                    }
                    break;
                case 'month':
                    $newDate->modify('last day of this month');
                    break;
                case 'week':
                    $newDate->modify(($newDate->format('w') === '0') ? 'now' : 'sunday this week');
                    break;
            }
            return $newDate;
        }
    }