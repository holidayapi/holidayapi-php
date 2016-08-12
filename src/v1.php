<?php
namespace HolidayAPI;

class v1
{
    private $parameters = array();

    public function __set($variable, $value)
    {
        $this->parameters[$variable] = $value;
    }

    public function __construct($key = null)
    {
        if ($key) {
            $this->key = $key;
        }
    }

    public function holidays($parameters = array())
    {
        $parameters = array_merge($this->parameters, $parameters);
        $parameters = http_build_query($parameters);

        $url  = 'https://holidayapi.com/v1/holidays?' . $parameters;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);

        if ($error = curl_error($curl)) {
            return false;
        }

        curl_close($curl);
        $response = json_decode($response, true);

        if (!$response) {
            return false;
        }

        return $response;
    }
}

