<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration\Response;

/**
 * Interface StorecreditInterface
 * @package Magestore\Webpos\Api\Integration\Response
 */
interface StorecreditInterface
{

    const BALANCE = 'balance';

    /**
     * Get balance
     *
     * @api
     * @return string
     */
    public function getBalance();

    /**
     * Set balance
     *
     * @api
     * @param string $balance
     * @return $this
     */
    public function setBalance($balance);

}
