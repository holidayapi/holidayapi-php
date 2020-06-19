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

    public function workday($request)
    {
        if (!isset($request['country'])) {
            throw new \Exception('Missing country');
        } elseif (!isset($request['start'])) {
            throw new \Exception('Missing start date');
        } elseif (!isset($request['days'])) {
            throw new \Exception('Missing days');
        } elseif ($request['days'] < 1) {
            throw new \Exception('Days must be 1 or more');
        }

        return $this->request('workday', $request);
    }
}

