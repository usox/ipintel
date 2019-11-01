<?php

declare(strict_types=1);

namespace Usox\IpIntel;

use Curl\Curl;

final class IpIntel implements IpIntelInterface
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var string Service Url
     */
    private $serviceUrl;

    /**
     * @var int The connection timeout in seconds
     */
    private $timeout;

    /**
     * @var string Contact mail address
     */
    private $contactEmailAddress;

    /**
     * @var string|null Custom flag to append to the url
     */
    private $customFlag;

    public function __construct(
        Curl $curl,
        string $contactEmailAddress,
        ?string $customFlag = null,
        int $timeout = 5,
        string $serviceUrl = 'https://check.getipintel.net'
    ) {
        $this->curl = $curl;
        $this->contactEmailAddress = $contactEmailAddress;
        $this->customFlag = $customFlag;
        $this->timeout = $timeout;
        $this->serviceUrl = $serviceUrl;
    }

    public function validate(string $ip, float $fraudProbability = 0.95): bool
    {
        $data = [
            'ip' => $ip,
            'contact' => $this->contactEmailAddress
        ];

        if ($this->customFlag !== null) {
            $data['flags'] = $this->customFlag;
        }

        $this->curl->setTimeout($this->timeout);

        $response = $this->curl->get(
            sprintf(
                '%s/check.php',
                $this->serviceUrl
            ),
            $data
        );

        if ($response < 0 || strcmp($response, "") == 0) {
            throw new Exception\ServiceException();
        }

        return (float) $response < $fraudProbability;
    }
}