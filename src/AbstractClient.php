<?php

namespace TwelveAndUs\API\Connect;

abstract class AbstractClient
{

    private $base = "https://12andus.com/services/api/rest/json/";

    private $secret = "";
    private $public = "";

    /**
     * Set the endpoint (defaults probably fine)
     *
     * @param string $base
     * @return void
     */
    public function setEndpoint(string $base): void
    {
        $this->base = $base;
    }

    /**
     * Return the endpoint base.
     *
     * @return string
     */
    public function getEndpoint() : string {
        return $this->base;
    }

    /**
     * Set your API keys.
     * Contact 12andus support for details.
     *
     * @param string $public
     * @param string $secret
     * @return void
     */
    public function setKeys(string $public, string $secret): void
    {
        $this->public = $public;
        $this->secret = $secret;
    }

    /**
     * Retrieve the public key
     *
     * @return string
     */
    public function getPublicKey() : string {
        return $this->public;
    }

    /**
     * Retrieve the secret key
     *
     * @return string
     */
    public function getSecretKey() : string {
        return $this->secret;
    }

    /**
     * Make an api call.
     *
     * @param string $method Method to use
     * @param array $params Params to send
     * @param string $verb Type of call (defaults GET)
     * @param boolean $raw If true, return just the plain json
     * @return array|string containing content, status code (http), and headers
     */
    protected function call( string $method, array $params = [], string $verb = 'GET', bool $raw = false) : array|string
    {

        $time = time();
        $nonce = sha1(rand());

        // Construct a hash of your post data
        $ctx = hash_init('sha1');
        hash_update($ctx, http_build_query($params));
        $posthash = hash_final($ctx);

        // Construct a signed hash of the query itself
        $ctx = hash_init('sha1', HASH_HMAC, $this->getSecretKey()); // Private key from api admin plugin
        hash_update($ctx, trim($time));
        hash_update($ctx, trim($nonce));
        hash_update($ctx, trim($this->getPublicKey()));
        hash_update($ctx, trim('method=' . $method));
        hash_update($ctx, trim($posthash));

        $hmac = urlencode(base64_encode(hash_final($ctx, true)));

        // HMAC authentication ( see http://learn.elgg.org/en/stable/guides/web-services/hmac.html)
        $_headers = [
            'X-Elgg-apikey' => $this->getPublicKey(),
            'X-Elgg-time' => $time,
            'X-Elgg-nonce' => $nonce,
            'X-Elgg-hmac' => $hmac,
            'X-Elgg-hmac-algo' => 'sha1',
            'X-Elgg-posthash' => $posthash,
            'X-Elgg-posthash-algo' => 'sha1'
        ];

        $headers = [];
        foreach ($_headers as $k => $v) {
            $headers[] = "$k: $v";
        }

        // Use cURL to make the API call.
        $curl_handle = curl_init();

        $url = $this->getEndpoint() . "?method=$method";
        if ($verb == 'POST') {
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            $url .= '&' . http_build_query($params);
        }
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded"));

        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl_handle, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLINFO_HEADER_OUT, 1);
        curl_setopt($curl_handle, CURLOPT_HEADER, 1);

        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);


        $buffer = curl_exec($curl_handle);
        $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        // Get HTTP Header / body
        $header_size = curl_getinfo($curl_handle, CURLINFO_HEADER_SIZE);
        $header = substr($buffer, 0, $header_size);
        $content = substr($buffer, $header_size);

        if ($error = curl_error($curl_handle)) {
            throw new \RuntimeException('Error send Webservice request: ' . $error);
        }

        curl_close($curl_handle);

        if ($raw) {
            return $content;
        } else {
            return [
                'content' => json_decode($content, true),
                'header' => $header,
                'status' => $http_status
            ];
        }
    }
}
