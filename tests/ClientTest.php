<?php
namespace HolidayAPI\Tests;
use HolidayAPI\Client;
use HolidayAPI\Request;

require __DIR__ . '/../vendor/autoload.php';

if (
    !class_exists('\PHPUnit_Framework_TestCase')
    && class_exists('\PHPUnit\Framework\TestCase')
) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = 'https://holidayapi.com/v1/';
    const KEY = '8e4de28c-4b18-49f0-9aba-0bd6b424fc38';

    public function testMissingKey()
    {
        try {
            $client = new Client(array());
        } catch (\Exception $e) {
            $this->assertRegExp('/missing api key/i', $e->getMessage());
        }
    }

    public function testInvalidKey()
    {
        try {
            $client = new Client(array(
                'key' => 'zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz',
            ));
        } catch (\Exception $e) {
            $this->assertRegExp('/invalid api key/i', $e->getMessage());
        }
    }

    public function testVersionTooLow()
    {
        try {
            $client = new Client(array('key' => self::KEY, 'version' => 0));
        } catch (\Exception $e) {
            $this->assertRegExp('/invalid version/i', $e->getMessage());
        }
    }

    public function testVersionTooHigh()
    {
        try {
            $client = new Client(array('key' => self::KEY, 'version' => 2));
        } catch (\Exception $e) {
            $this->assertRegExp('/invalid version/i', $e->getMessage());
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

    public function testSearchCountries()
    {
        $url = self::BASE_URL . 'countries?key=' . self::KEY . '&search=Sao';

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
        ), $client->countries(array('search' => 'Sao')));
    }

    public function testReturnCountryByCode()
    {
        $url = self::BASE_URL . 'countries?key=' . self::KEY . '&country=ST';

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
        ), $client->countries(array('country' => 'ST')));
    }

    public function testReturnCountryWithPublic()
    {
        $url = self::BASE_URL . 'countries?key=' . self::KEY . '&public=1';

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
        ), $client->countries(array('public' => true)));
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

    public function testSearchHolidays()
    {
        $url = self::BASE_URL . 'holidays?key=' . self::KEY . '&country=US&year=2015&search=Independence';

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
            'search' => 'Independence',
        )));
    }

    public function testHolidaysCountryMissing()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->holidays(array('year' => 2015));
        } catch (\Exception $e) {
            $this->assertRegExp('/missing country/i', $e->getMessage());
        }
    }

    public function testHolidaysYearMissing()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->holidays(array('country' => 'US'));
        } catch (\Exception $e) {
            $this->assertRegExp('/missing year/i', $e->getMessage());
        }
    }

    public function testHolidaysBothPreviousAndUpcoming()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->holidays(array(
                'country' => 'US',
                'year' => 2015,
                'month' => 7,
                'day' => 4,
                'upcoming' => true,
                'previous' => true,
            ));
        } catch (\Exception $e) {
            $this->assertRegExp('/previous and upcoming/i', $e->getMessage());
        }
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

    public function testSearchLanguages()
    {
        $url = self::BASE_URL . 'languages?key=' . self::KEY . '&search=Eng';

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
        ), $client->languages(array('search' => 'Eng')));
    }

    public function testReturnLanguageByCode()
    {
        $url = self::BASE_URL . 'languages?key=' . self::KEY . '&language=en';

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
        ), $client->languages(array('language' => 'en')));
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

    public function testWorkdayCountryMissing()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->workday(array());
        } catch (\Exception $e) {
            $this->assertRegExp('/missing country/i', $e->getMessage());
        }
    }

    public function testWorkdayStartMissing()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->workday(array('country' => 'US'));
        } catch (\Exception $e) {
            $this->assertRegExp('/missing start date/i', $e->getMessage());
        }
    }

    public function testWorkdayDaysMissing()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->workday(array(
                'country' => 'US',
                'start' => '2019-07-01',
            ));
        } catch (\Exception $e) {
            $this->assertRegExp('/missing days/i', $e->getMessage());
        }
    }

    public function testWorkdayDaysNegative()
    {
        $client = new Client(array('key' => self::KEY));

        try {
            $client->workday(array(
                'country' => 'US',
                'start' => '2019-07-01',
                'days' => -10,
            ));
        } catch (\Exception $e) {
            $this->assertRegExp('/days must be 1 or more/i', $e->getMessage());
        }
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
}

