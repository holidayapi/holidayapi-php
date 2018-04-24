<?php

namespace HolidayAPI;

class v1
{
    const FORMAT_CSV  = 'csv';
    const FORMAT_JSON = 'json';
    const FORMAT_PHP  = 'php';
    const FORMAT_TSV  = 'tsv';
    const FORMAT_YAML = 'yaml';
    const FORMAT_XML  = 'xml';

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * v1 constructor.
     * @param string $key
     */
    public function __construct($key = null)
    {
        if ($key) {
            $this->setKey($key);
        }
    }

    public function holidays($parameters = array())
    {
        $parameters = array_merge($this->parameters, $parameters);
        $this->validateParameters($parameters);
        $parameters = http_build_query($parameters);

        $url = 'https://holidayapi.com/v1/holidays?' . $parameters;
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

    /**
     * @param array $parameters
     * @throws \Exception
     */
    protected function validateParameters(array $parameters)
    {
        $allowedParameters = $this->getAllowedParameters();
        foreach ($parameters as $key => $value) {
            if (!in_array($key, $allowedParameters)) {
                throw new \UnexpectedValueException(sprintf("Parameter %s not allowed, only the following are allowed: %s", $key, implode(", ", $allowedParameters)));
            }
        }

        foreach ($this->getRequiredParameters() as $requiredParameter) {
            if (!array_key_exists($requiredParameter, $parameters)) {
                throw new \Exception(sprintf("Missing parameter %s, please use set%s(\$value)", $requiredParameter, ucfirst($requiredParameter)));
            }
        }
    }

    /**
     * @return array
     */
    protected function getRequiredParameters()
    {
        return array(
            'key',
            'country',
            'year'
        );
    }

    /**
     * @return array
     */
    protected function getOptionalParameters()
    {
        return array(
            'month',
            'day',
            'previous',
            'upcoming',
            'public',
            'format',
            'pretty',
        );
    }

    /**
     * @return array
     */
    protected function getAllowedParameters()
    {
        return array_merge($this->getRequiredParameters(), $this->getOptionalParameters());
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->parameters['key'] = $key;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->parameters['country'] = $country;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->parameters['year'] = $year;
    }

    /**
     * @param string $month
     */
    public function setMonth($month)
    {
        $this->parameters['month'] = $month;
    }

    /**
     * @param string $day
     */
    public function setDay($day)
    {
        $this->parameters['day'] = $day;
    }

    /**
     * @param string $previous
     */
    public function setPrevious($previous)
    {
        $this->parameters['previous'] = $previous;
    }

    /**
     * @param string $upcoming
     */
    public function setUpcoming($upcoming)
    {
        $this->parameters['upcoming'] = $upcoming;
    }

    /**
     * @param string $public
     */
    public function setPublic($public)
    {
        $this->parameters['public'] = $public;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->parameters['format'] = $format;
    }

    /**
     * @param string $pretty
     */
    public function setPretty($pretty)
    {
        $this->parameters['pretty'] = $pretty;
    }
    
}

