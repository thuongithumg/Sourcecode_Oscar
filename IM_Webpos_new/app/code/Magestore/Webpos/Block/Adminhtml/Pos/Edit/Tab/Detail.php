<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab;

use \Magestore\Webpos\Api\Data\Shift\ShiftInterface as ShiftInterface;
use \Magento\Framework\Locale\FormatInterface as LocaleFormat;

/**
 * Class Detail
 * @package Magestore\Webpos\Block\Adminhtml\Pos\Edit\Tab
 */
class Detail extends \Magento\Backend\Block\Template {

    /**
     * @var \Magestore\Webpos\Model\Shift\ShiftRepository
     */
    protected $shiftRepository;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    protected $posRepository;

    /**
     * @var \Magestore\Webpos\Model\Directory\Currency\CurrencyFactory
     */
    protected $webposCurrencyFactory;

    /**
     * Detail constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\Webpos\Model\Shift\ShiftRepository $shiftRepository
     * @param LocaleFormat $localeFormat
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     * @param \Magestore\Webpos\Model\Directory\Currency\CurrencyFactory $webposCurrencyFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Webpos\Model\Shift\ShiftRepository $shiftRepository,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magestore\Webpos\Model\Directory\Currency\CurrencyFactory $webposCurrencyFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shiftRepository = $shiftRepository;
        $this->localeFormat = $localeFormat;
        $this->storeManager = $context->getStoreManager();
        $this->currencyFactory = $currencyFactory;
        $this->posRepository = $posRepository;
        $this->webposCurrencyFactory = $webposCurrencyFactory;
    }

    /**
     * @return string
     */
    public function getCurrentPosId(){
        return $this->getRequest()->getParam('id');
    }

    /**
     * @param bool $isJson
     * @param string $key
     * @return array|mixed|string
     */
    public function getSessionData($isJson = true, $key = ''){
        $data = [];
        $data['current_pos_id'] = $this->getCurrentPosId();
        $data['sessions'] = $this->getCurrentSessions();
        $data['denominations'] = $this->getCurrentDenominations();
        $data['get_sessions_url'] = $this->getUrl('webposadmin/pos/getSessions', ['form_key' => $this->getFormKey()]);
        $data['save_transaction_url'] = $this->getUrl('webposadmin/pos/makeAdjustment', ['form_key' => $this->getFormKey()]);
        $data['close_session_url'] = $this->getUrl('webposadmin/pos/closeSession', ['form_key' => $this->getFormKey()]);
        if ($key) {
            $data = (isset($data[$key]))?$data[$key]:'';
        }
        return ($isJson)?\Zend_Json::encode($data):$data;
    }

    /**
     * @return array
     */
    public function getCurrentSessions(){
        $posId = $this->getCurrentPosId();
        $sessionsData = [];
        $sessions = ($posId)?$this->shiftRepository->getOpenSession($posId):[];
        if(!empty($sessions)){
            foreach ($sessions as $session){
                $sessionData = $session->getData();
                $sessionData[ShiftInterface::STAFF_NAME] =  $session->getStaffName();
                $sessionData[ShiftInterface::SALE_SUMMARY] =  $session->getSaleSummary();
                $sessionData[ShiftInterface::CASH_TRANSACTION] =  $session->getCashTransaction();
                $sessionData[ShiftInterface::ZREPORT_SALES_SUMMARY] =  $session->getZreportSalesSummary();
                $sessionData['print_url'] =  $this->getUrl('webposadmin/zreport/prints', ['id' => $session->getEntityId(), 'onlyprint' => 1]);
                $sessionsData[] = $sessionData;
            }
        }
        return $sessionsData;
    }

    /**
     * @return array
     */
    public function getCurrentDenominations(){
        $posId = $this->getCurrentPosId();
        $denominations = [];
        $pos = ($posId)?$this->posRepository->get($posId):false;
        if($pos){
            $denominations = $pos->getDenominations();
        }
        return $denominations;
    }

    /**
     * Retrieve webpos configuration
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getWebposConfig()
    {
        $output = [];
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $this->storeManager->getStore()->getCurrentCurrency()->getCode()
        );
        $output['basePriceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $this->storeManager->getStore()->getBaseCurrency()->getCode()
        );
        $output['currentCurrencyCode'] = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $output['baseCurrencyCode'] = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $currency = $this->currencyFactory->create();
        $output['currentCurrencySymbol'] = $currency->load($output['currentCurrencyCode'])->getCurrencySymbol();
        $output['currencies'] = $this->webposCurrencyFactory->create()->getCurrencyList();
        return $output;
    }
}