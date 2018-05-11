<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Settings;

/**
 * Class General
 * @package Magestore\Webpos\Block\Settings
 */
class General extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $_roleTitleArray = [];

    /**
     * General constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Webpos\Helper\Permission $webposPermission
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staff
     * @param \Magestore\Webpos\Model\Staff\RoleFactory $role
     * @param \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory $authorizationRule
     * @param \Magestore\Webpos\Model\Staff\Acl\AclResource\Provider $aclResourceProvider
     * @param \Magento\Integration\Helper\Data $integrationData
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return parent::getJsLayout();
    }
}
