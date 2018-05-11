<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Directory\Country;
/**
 * class \Magestore\Webpos\Model\Direactory\Country\CountryRepository
 *
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CountryRepository
     implements \Magestore\Webpos\Api\Directory\Country\CountryRepositoryInterface
{
    /**
     * webpos country model
     *
     * @var \Magestore\Webpos\Model\Directory\Country\Country
     */
    protected $_countryModel;

    /**
     * webpos country result interface
     *
     * @var \Magestore\Webpos\Api\Data\Directory\Country\CountryResultInterfaceFactory
     */
    protected $_countryResultInterface;

    /**
     * @param \Magestore\Webpos\Model\Directory\Country\CountryFactory $countryModel
     * @param \Magestore\Webpos\Api\Data\Directory\Country\CountryResultInterfaceFactory $countryResultInterface
     */
    public function __construct(
        \Magestore\Webpos\Model\Directory\Country\Country $countryModel,
        \Magestore\Webpos\Api\Data\Directory\Country\CountryResultInterfaceFactory $countryResultInterface
    ) {
        $this->_countryModel= $countryModel;
        $this->_countryResultInterface = $countryResultInterface;
    }

    /**
     * Get countries list
     *
     * @api
     * @return array|null
     */
    public function getList() {
        $countryList = $this->_countryModel->getList();
        $countries = $this->_countryResultInterface->create();
        $countries->setItems($countryList);
        $countries->setTotalCount(count($countryList));
        return $countries;
    }

}