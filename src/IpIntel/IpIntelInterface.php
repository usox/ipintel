<?php

namespace Usox\IpIntel;

interface IpIntelInterface
{

    /**
     * @throws Exception\ServiceException
     */
    public function validate(string $ip, float $fraudProbability = 0.95): bool;
}