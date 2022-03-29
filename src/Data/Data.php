<?php

namespace TwelveAndUs\API\Connect\Data;

abstract class Data {

    private $params = [];

    /**
     * Set params
     *
     * @param array $params
     */
    protected function setParams( array $params = []) {
        $this->params = $params;
    }

    /**
     * Retrieve parameters
     * 
     * @param string $suffix Optional suffix to add to the key value (in the case of multiple parameters)
     */
    public function getParams(string $suffix = null) : array {
        if (!empty($suffix)) {
            $params = []; 
            
            foreach ($this->params as $key => $value) {
                $params[$key.$suffix] = $value;
            }

            return $params;
        }
        return $this->params;
    }
}