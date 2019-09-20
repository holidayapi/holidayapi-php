<?php
/**
 * Copyright (c) Gravity Boulevard, LLC
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace HolidayAPI;

class Request
{
    private $handlers = array();

    public function __construct($handlers = array())
    {
        $this->handlers = $handlers;
    }

    public function execute($curl)
    {
        if (isset($this->handlers['execute'])) {
            $info = curl_getinfo($curl);
            $url = $info['url'];

            if (isset($this->handlers['execute'][$url])) {
                return $this->handlers['execute'][$url]($curl);
            }
        }

        return curl_exec($curl);
    }

    public function error($curl)
    {
        if (isset($this->handlers['error'])) {
            $info = curl_getinfo($curl);
            $url = $info['url'];

            if (isset($this->handlers['error'][$url])) {
                return $this->handlers['error'][$url]($curl);
            }
        }

        return curl_error($curl);
    }

    public function get($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = $this->execute($curl);

        if ($error = $this->error($curl)) {
            throw new \Exception($error);
        }

        curl_close($curl);
        $response = json_decode($response, true);

        if (!$response) {
            throw new \Exception('Empty response received');
        }

        if (isset($response['error'])) {
            throw new \Exception($response['error'], $response['status']);
        }

        return $response;
    }
}

