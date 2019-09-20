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

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $curl = curl_init();
        $request = new Request();

        $this->assertFalse($request->execute($curl));
    }

    public function testGet()
    {
        $url = 'https://holidayapi.com';

        $request = new Request(array(
            'execute' => array(
                $url => function ()
                {
                    return '';
                },
            ),
        ));

        try {
            $request->get($url);
        } catch (\Exception $e) {
            $this->assertRegExp('/empty response/i', $e->getMessage());
        }
    }
}

