<?php

namespace Tests;

use TwelveAndUs\API\Connect\Client;
use TwelveAndUs\API\Connect\Data\Birthdata;

class ClientTest extends \PHPUnit\Framework\TestCase {

    public function testAstro() {

        $client = new Client(getenv('PUBKEY'), getenv('SECKEY'));

        $result = $client->astro("dominant", new Birthdata("testname", "oxford", 1950, 5, 14, 10, 30));

        $this->assertNotEmpty($result);
        $this->assertFalse($result['content']['status'] ==  -1, $result['content']['message']??'');
    }
}