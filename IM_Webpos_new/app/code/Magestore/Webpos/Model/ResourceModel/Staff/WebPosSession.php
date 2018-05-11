<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Staff;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Staff\Staff
 *
 * Web POS PosUser resource model
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class WebPosSession extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     * @var \Magestore\Webpos\Model\PosUserFactory
     */
    protected $_posUserFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magestore\Webpos\Model\Staff\StaffFactory $posUserFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_posUserFactory = $posUserFactory;
        $this->_timezone = $timezone;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webpos_session', 'id');
    }



}
