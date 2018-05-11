<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Report;


abstract class AbstractReport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
        $this->_date = $date;
    }

    protected function _construct()
    {
        $params = new \Magento\Framework\DataObject();
        $this->setDefaultParams($params);
        return parent::_construct;
    }

    /**
     * Admin session model
     *
     * @var null|\Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession = null;

    /**
     * Retrieve admin session model
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    protected function _getSession()
    {
        if ($this->_adminSession === null) {
            $this->_adminSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
        }
        return $this->_adminSession;
    }

    /**
     * Add report breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }

    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }

        $webposRequestData = $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->prepareFilterString(
            $this->getRequest()->getParam('filter')
        );
        $inputFilter = new \Zend_Filter_Input(
            ['from' => $this->_dateFilter, 'to' => $this->_dateFilter],
            [],
            $webposRequestData
        );
        $webposRequestData = $inputFilter->getUnescaped();
        $webposRequestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new \Magento\Framework\DataObject();
        
        foreach ($webposRequestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        $this->setDefaultParams($params);
        foreach ($blocks as $blockChild) {
            if ($blockChild) {
                $blockChild->setPeriodType($params->getData('period_type'));
                $blockChild->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Add refresh statistics links
     *
     * @param string $flagCode
     * @param string $refreshCode
     * @return $this
     */
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {        
        return $this;
    }

    /**
     * Pseudo constructor
     *
     * @return void
     */
    public function setDefaultParams($reportParams)
    {
        $timeTo = $this->_date->gmtDate('Y-m-d');    
        $timeFrom = $this->_date->gmtDate('Y-m-d', strtotime("-1 months", strtotime($timeTo)));
        if(!$reportParams->getData('from'))
            $reportParams->setData('from', $timeFrom);
        if(!$reportParams->getData('to'))
            $reportParams->setData('to', $timeTo);
        if(!$reportParams->getData('show_order_statuses'))
            $reportParams->setData('show_order_statuses', 0);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::reports');
    }
    
}
