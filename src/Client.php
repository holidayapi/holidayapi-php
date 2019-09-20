<?php
/**
 * Copyright (c) Gravity Boulevard, LLC
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace HolidayAPI;
use HolidayAPI;

class Client
{
    private $handler;
    public $baseUrl;
    public $key;

    public function __construct($options)
    {
        $getYours = 'get yours at HolidayAPI.com';
        $uuidRegExp = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/';

        if (!isset($options['key'])) {
            throw new \Exception("Missing API key, {$getYours}");
        }

        if (!preg_match($uuidRegExp, $options['key'])) {
            throw new \Exception("Invalid API key, {$getYours}");
        }

        $version = (isset($options['version']) ? $options['version'] : 1);

        if ($version != 1) {
            throw new \Exception('Invalid version number, expected "1"');
        }

        $this->baseUrl = "https://holidayapi.com/v{$version}/";
        $this->key = $options['key'];

        if (isset($options['handler'])) {
            $this->handler = $options['handler'];
        } else {
            $this->handler = new Request();
        }
    }

    private function createUrl($endpoint, $request = array())
    {
        $parameters = array_merge(array('key' => $this->key), $request);
        $parameters = http_build_query($parameters);

        return "{$this->baseUrl}{$endpoint}?{$parameters}";
    }

    private function request($endpoint, $request)
    {
        return $this->handler->get($this->createUrl($endpoint, $request));

        /*
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->createUrl($endpoint, $request),
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);

        if ($error = curl_error($curl)) {
            throw new \Exception($error);
        }

        curl_close($curl);
        $response = json_decode($response, true);

        if (!$response) {
            throw new \Exception('Empty response received');
        }

        return $response;
        */
    }

    public function countries($request = array())
    {
        return $this->request('countries', $request);
    }

    public function holidays($request)
    {
        if (!isset($request['country'])) {
            throw new \Exception('Missing country');
        } elseif (!isset($request['year'])) {
            throw new \Exception('Missing year');
        } elseif (
            isset($request['previous'], $request['upcoming'])
            && $request['previous'] && $request['upcoming']
        ) {
            throw new \Exception('Previous and upcoming are mutually exclusive');
        }

        return $this->request('holidays', $request);
    }

    public function languages($request = array())
    {
        return $this->request('languages', $request);
    }
}

