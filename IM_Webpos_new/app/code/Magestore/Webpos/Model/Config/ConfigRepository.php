<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Config;
/**
 * class \Magestore\Webpos\Model\Config\ConfigRepository
 *
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class ConfigRepository implements \Magestore\Webpos\Api\Config\ConfigRepositoryInterface
{
    /**
     * webpos config model
     *
     * @var \Magestore\Webpos\Model\Config\Config
     */
    protected $_configModel;

    /**
     * webpos config result interface
     *
     * @var \Magestore\Webpos\Api\Data\Config\ConfigResultInterfaceFactory
     */
    protected $_configResultInterface;

    /**
     * @param \Magestore\Webpos\Model\Config\Config $configModel
     * @param \Magestore\Webpos\Api\Data\Config\ConfigResultInterfaceFactory $configResultInterface
     */
    public function __construct(
        \Magestore\Webpos\Model\Config\Config $configModel,
        \Magestore\Webpos\Api\Data\Config\ConfigResultInterfaceFactory $configResultInterface
    ) {
        $this->_configModel = $configModel;
        $this->_configResultInterface = $configResultInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getList() {
        $config = $this->_configModel->getAllConfiguration();
        $configInterFace = $this->_configResultInterface->create();
        $configInterFace->setItems($config);
        $configInterFace->setTotalCount(count($config));
        return $configInterFace;
    }

    /**
     * Get config value by path
     *
     * @api
     * @return string|null
     */
    public function getConfigByPath($path) {
        $config = $this->_configModel->getConfigByPath($path);
        return $config;
    }
}