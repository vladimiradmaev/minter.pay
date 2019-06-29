<?php

namespace Minter\Pay;

/**
 * Class MinterAPI
 * Переопределённый класс MinterApi (необходимо для правильного построения URL Api)
 * @package Minter\Pay
 */
class MinterAPI extends \Minter\MinterAPI
{
    /**
     * MinterAPI constructor.
     * @param bool $nodeUrl
     */
    public function __construct($nodeUrl)
    {
        parent::__construct($nodeUrl);
    }

    /**
     * Get status of node
     *
     * @return \stdClass
     * @throws \Exception
     */
    public function getStatus(): \stdClass
    {
        return $this->get('/api/v1/status');
    }

    /**
     * Returns the balance of given account and the number of outgoing transaction.
     *
     * @param string $address
     * @param null|int $height
     * @return \stdClass
     * @throws \Exception
     */
    public function getBalance(string $address, ?int $height = null): \stdClass
    {
        return $this->get('/api/v1/addresses/' . $address);
    }
}