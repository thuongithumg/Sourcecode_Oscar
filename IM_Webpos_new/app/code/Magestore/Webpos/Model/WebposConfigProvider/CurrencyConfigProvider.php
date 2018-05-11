<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\WebposConfigProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CurrencyConfigProvider implements ConfigProviderInterface {

    /**
     * @var \Magestore\Webpos\Model\Directory\Currency\Currency
     */
    protected $_currencyModel; 

    public function __construct(
            \Magestore\Webpos\Model\Directory\Currency\Currency $currencyModel
    ) {
        $this->_currencyModel = $currencyModel;
    }

    public function getConfig() {

        $currencies = $this->_currencyModel->getCurrencyList();
        return ['currencies' => $currencies];
    }

}
