<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Denomination;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Denomination
 * 
 * Abstract Denomination action class
 * Methods:
 *  _isAllowed
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Denomination
 * @module      Webpos
 * @author      Magestore Developer
 */
abstract class AbstractDenomination extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magestore\Webpos\Model\Denomination\DenominationRepository
     */
    protected $denominationRepository;

    /**
     * @var \Magestore\Webpos\Model\Denomination\DenominationFactory
     */
    protected $denominationFactory;

    /**
     * AbstractDenomination constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\Webpos\Model\Denomination\DenominationRepository $denominationRepository
     * @param \Magestore\Webpos\Model\Denomination\DenominationFactory $denominationFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\Webpos\Model\Denomination\DenominationRepository $denominationRepository,
        \Magestore\Webpos\Model\Denomination\DenominationFactory $denominationFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->denominationRepository = $denominationRepository;
        $this->denominationFactory = $denominationFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::denomination');
    }
}