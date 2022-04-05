<?php
namespace HolidayAPI\Tests;
use HolidayAPI\Client;
use HolidayAPI\Request;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const BASE_URL = 'https://holidayapi.com/v1/';
    const KEY = '8e4de28c-4b18-49f0-9aba-0bd6b424fc38';

    public function testMissingKey()
    {
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            $assertRegExp = 'assertMatchesRegularExpression';
        } else {
            $assertRegExp = 'assertRegExp';
        }

        try {
            new Client(array());
        } catch (\Exception $e) {
            $this->$assertRegExp('/missing api key/i', $e->getMessage());
        }
    }

    public function testInvalidKey()
    {
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            $assertRegExp = 'assertMatchesRegularExpression';
        } else {
            $assertRegExp = 'assertRegExp';
        }

        try {
            new Client(array(
                'key' => 'zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz',
            ));
        } catch (\Exception $e) {
            $this->$assertRegExp('/invalid api key/i', $e->getMessage());
        }
    }

    public function testVersionTooLow()
    {
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            $assertRegExp = 'assertMatchesRegularExpression';
        } else {
            $assertRegExp = 'assertRegExp';
        }

        try {
            new Client(array('key' => self::KEY, 'version' => 0));
        } catch (\Exception $e) {
            $this->$assertRegExp('/invalid version/i', $e->getMessage());
        }
    }

    public function testVersionTooHigh()
    {
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            $assertRegExp = 'assertMatchesRegularExpression';
        } else {
            $assertRegExp = 'assertRegExp';
        }

        try {
            new Client(array('key' => self::KEY, 'version' => 2));
        } catch (\Exception $e) {
            $this->$assertRegExp('/invalid version/i', $e->getMessage());
        }
    }

    public function testAssignClassMembers()
    {
        $client = new Client(array('key' => self::KEY));

        $this->assertSame(self::BASE_URL, $client->baseUrl);
        $this->assertSame(self::KEY, $client->key);
    }

    public function testReturnCountries()
    {
        $url = self::BASE_URL . 'countries?key=' . self::KEY;

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 200,
                        'countries' => array(
                            array(
                                'code' => 'ST',
                                'name' => 'Sao Tome and Principle',
                            ),
                        ),
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        $this->assertEquals(array(
            'status' => 200,
            'countries' => array(
                array(
                    'code' => 'ST',
                    'name' => 'Sao Tome and Principle',
                ),
            ),
        ), $client->countries());
    }

    public function testCountriesRaise4xxErrors()
    {
        $url = self::BASE_URL . 'countries?key=' . self::KEY;

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 429,
                        'error' => 'Rate limit exceeded',
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->countries();
        } catch (\Exception $e) {
            $this->assertSame(429, $e->getCode());
            $this->assertSame('Rate limit exceeded', $e->getMessage());
        }
    }

    public function testCountriesRaise5xxErrors()
    {
        $url = self::BASE_URL . 'countries?key=' . self::KEY;

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return false;
                },
            ),
            'error' => array(
                $url => function ()
                {
                    return 'Internal server error';
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->countries();
        } catch (\Exception $e) {
            $this->assertSame('Internal server error', $e->getMessage());
        }
    }

    public function testReturnHolidays()
    {
        $url = self::BASE_URL . 'holidays?key=' . self::KEY . '&country=US&year=2015&month=7&day=4';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 200,
                        'holidays' => array(
                            array(
                                'name' => 'Independence Day',
                                'date' => '2015-07-04',
                                'observed' => '2015-07-03',
                                'public' => true,
                            ),
                        ),
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        $this->assertEquals(array(
            'status' => 200,
            'holidays' => array(
                array(
                    'name' => 'Independence Day',
                    'date' => '2015-07-04',
                    'observed' => '2015-07-03',
                    'public' => true,
                ),
            ),
        ), $client->holidays(array(
            'country' => 'US',
            'year' => 2015,
            'month' => 7,
            'day' => 4,
        )));
    }

    public function testHolidaysRaise4xxErrors()
    {
        $url = self::BASE_URL . 'holidays?key=' . self::KEY . '&country=US&year=2019';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 429,
                        'error' => 'Rate limit exceeded',
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->holidays(array('country' => 'US', 'year' => 2019));
        } catch (\Exception $e) {
            $this->assertSame(429, $e->getCode());
            $this->assertSame('Rate limit exceeded', $e->getMessage());
        }
    }

    public function testHolidaysRaise5xxErrors()
    {
        $url = self::BASE_URL . 'holidays?key=' . self::KEY . '&country=US&year=2019';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return false;
                },
            ),
            'error' => array(
                $url => function ()
                {
                    return 'Internal server error';
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->holidays(array('country' => 'US', 'year' => 2019));
        } catch (\Exception $e) {
            $this->assertSame('Internal server error', $e->getMessage());
        }
    }

    public function testReturnLanguages()
    {
        $url = self::BASE_URL . 'languages?key=' . self::KEY;

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 200,
                        'languages' => array(
                            array(
                                'code' => 'en',
                                'name' => 'English',
                            ),
                        ),
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        $this->assertEquals(array(
            'status' => 200,
            'languages' => array(
                array(
                    'code' => 'en',
                    'name' => 'English',
                ),
            ),
        ), $client->languages());
    }

    public function testLanguagesRaise4xxErrors()
    {
        $url = self::BASE_URL . 'languages?key=' . self::KEY;

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 429,
                        'error' => 'Rate limit exceeded',
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->languages();
        } catch (\Exception $e) {
            $this->assertSame(429, $e->getCode());
            $this->assertSame('Rate limit exceeded', $e->getMessage());
        }
    }

    public function testLanguagesRaise5xxErrors()
    {
        $url = self::BASE_URL . 'languages?key=' . self::KEY;

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return false;
                },
            ),
            'error' => array(
                $url => function ()
                {
                    return 'Internal server error';
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->languages();
        } catch (\Exception $e) {
            $this->assertSame('Internal server error', $e->getMessage());
        }
    }

    public function testReturnWorkday()
    {
        $url = self::BASE_URL . 'workday?key=' . self::KEY . '&country=US&start=2019-07-01&days=10';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 200,
                        'workday' => array(
                            'date' => '2019-07-16',
                        ),
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        $this->assertEquals(array(
            'status' => 200,
            'workday' => array(
                'date' => '2019-07-16',
            ),
        ), $client->workday(array(
            'country' => 'US',
            'start' => '2019-07-01',
            'days' => 10,
        )));
    }

    public function testWorkdayRaise4xxErrors()
    {
        $url = self::BASE_URL . 'workday?key=' . self::KEY . '&country=US&start=2019-07-01&days=10';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 429,
                        'error' => 'Rate limit exceeded',
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->workday(array(
                'country' => 'US',
                'start' => '2019-07-01',
                'days' => 10,
            ));
        } catch (\Exception $e) {
            $this->assertSame(429, $e->getCode());
            $this->assertSame('Rate limit exceeded', $e->getMessage());
        }
    }

    public function testWorkdayRaise5xxErrors()
    {
        $url = self::BASE_URL . 'workday?key=' . self::KEY . '&country=US&start=2019-07-01&days=10';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return false;
                },
            ),
            'error' => array(
                $url => function ()
                {
                    return 'Internal server error';
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->workday(array(
                'country' => 'US',
                'start' => '2019-07-01',
                'days' => 10,
            ));
        } catch (\Exception $e) {
            $this->assertSame('Internal server error', $e->getMessage());
        }
    }

    public function testReturnWorkdays()
    {
        $url = self::BASE_URL . 'workdays?key=' . self::KEY . '&country=US&start=2019-07-01&end=2019-07-10';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 200,
                        'workdays' => 7,
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        $this->assertEquals(array(
            'status' => 200,
            'workdays' => 7,
        ), $client->workdays(array(
            'country' => 'US',
            'start' => '2019-07-01',
            'end' => '2019-07-10',
        )));
    }

    public function testWorkdaysRaise4xxErrors()
    {
        $url = self::BASE_URL . 'workdays?key=' . self::KEY . '&country=US&start=2019-07-01&end=2019-07-10';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return json_encode(array(
                        'status' => 429,
                        'error' => 'Rate limit exceeded',
                    ));
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->workdays(array(
                'country' => 'US',
                'start' => '2019-07-01',
                'end' => '2019-07-10',
            ));
        } catch (\Exception $e) {
            $this->assertSame(429, $e->getCode());
            $this->assertSame('Rate limit exceeded', $e->getMessage());
        }
    }

    public function testWorkdaysRaise5xxErrors()
    {
        $url = self::BASE_URL . 'workdays?key=' . self::KEY . '&country=US&start=2019-07-01&end=2019-07-10';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return false;
                },
            ),
            'error' => array(
                $url => function ()
                {
                    return 'Internal server error';
                },
            ),
        ));

        $client = new Client(array('key' => self::KEY, 'handler' => $request));

        try {
            $client->workdays(array(
                'country' => 'US',
                'start' => '2019-07-01',
                'end' => '2019-07-10',
            ));
        } catch (\Exception $e) {
            $this->assertSame('Internal server error', $e->getMessage());
        }
    }
}

