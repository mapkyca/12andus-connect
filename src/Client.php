<?php

namespace TwelveAndUs\API\Connect;

use TwelveAndUs\API\Connect\Data\Birthdata;

class Client extends AbstractClient
{

    /**
     * Create a client library.
     * You will need a key pair for this, please contact 12andus support for a key.
     *
     * @param string $publickey Your public key
     * @param string $secretkey Your secret key 
     * @param string $endpoint The endpoint, defaults to live
     */
    public function __construct( string $publickey, string $secretkey, string $endpoint = 'https://12andus.com/services/api/rest/json/') {
        $this->setKeys($publickey, $secretkey);
        $this->setEndpoint($endpoint); 
    }

    /**
     * Astro Calculation
     *
     * @param string $type List of types for birth chart and transits
     * @param Birthdata $birthdata Birth data details
     * @return array|null
     */
    public function astro (
        string $type,
        Birthdata $birthdata
    ) : ? array {
        $params = array_merge([ 'type' => $type ], $birthdata->getParams());
        return $this->call('12andus.api.astro', $params);
    }

    /**
     * Relationship Calculation
     *
     * @param string $type List of types for relationships
     * @param Birthdata $birthdata1
     * @param Birthdata $birthdata2
     * @return array|null
     */
    public function relationship (
        string $type,
        Birthdata $birthdata1,
        Birthdata $birthdata2
    ) : ? array {
        $params = array_merge([ 'type' => $type ], $birthdata1->getParams('1'), $birthdata2->getParams('2'));
        return $this->call('12andus.api.relationship', $params );
    }
}