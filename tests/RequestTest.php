<?php
namespace HolidayAPI\Tests;
use HolidayAPI\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
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

