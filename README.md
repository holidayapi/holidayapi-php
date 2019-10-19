# Holiday API PHP Library

[![License](https://img.shields.io/packagist/l/holidayapi/holidayapi-php?style=for-the-badge)](https://github.com/holidayapi/holidayapi-php/blob/master/LICENSE)
![PHP Version](https://img.shields.io/packagist/php-v/holidayapi/holidayapi-php?style=for-the-badge)
![Build Status](https://img.shields.io/travis/holidayapi/holidayapi-php/master?style=for-the-badge)
[![Coverage Status](https://img.shields.io/coveralls/github/holidayapi/holidayapi-php/master?style=for-the-badge)](https://coveralls.io/github/holidayapi/holidayapi-php?branch=master)

Official PHP library for [Holiday API](https://holidayapi.com) providing quick
and easy access to holiday information from applications written in PHP.

## Migrating from 1.x

Please note, version 2.x of this library is a full rewrite of the 1.x series.
The interfacing to the library has been simplified and existing applications
upgrading to 2.x will need to be updated.

| Version 1.x Syntax (Old)                   | Version 2.x Syntax (New)                                  |
|--------------------------------------------|-----------------------------------------------------------|
| `$holiday_api = new \HolidayAPI\v1($key);` | `$holiday_api = new \HolidayAPI\Client(['key' => $key]);` |

Version 1.x of the library can still be found
[here](https://github.com/joshtronic/php-holidayapi).

## Documentation

Full documentation of the Holiday API endpoints is available
[here](https://holidayapi.com/docs).

## Installation

```shell
composer require holidayapi/holidayapi-php
```

## Usage

```php
$key = 'Insert your API key here';
$holiday_api = new \HolidayAPI\Client(['key' => $key]);

try {
    // Fetch supported countries and subdivisions
    $countries = $holiday_api->countries();

    // Fetch supported languages
    $languages = $holiday_api->languages();

    // Fetch holidays with minimum parameters
    $holidays = $holiday_api->holidays([
      'country' => 'US',
      'year' => 2019,
    ]);

    var_dump($countries, $languages, $holidays);
} catch (Exception $e) {
    var_dump($e);
}
```

## Examples

### Countries

#### Fetch all supported countries

```php
$holiday_api->countries();
```

#### Search for a country by code or name

```php
$holiday_api->countries([
  'search' => 'united',
]);
```

### Languages

#### Fetch all supported languages

```php
$holiday_api->languages();
```

#### Search for a language by code or name

```php
$holiday_api->languages([
  'search' => 'Chinese',
]);
```

### Holidays

#### Fetch holidays for a specific year

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
]);
```

#### Fetch holidays for a specific month

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'month' => 7,
]);
```

#### Fetch holidays for a specific day

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'month' => 7,
  'day' => 4,
]);
```

#### Fetch upcoming holidays based on a specific date

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'month' => 7,
  'day' => 4,
  'upcoming' => true,
]);
```

#### Fetch previous holidays based on a specific date

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'month' => 7,
  'day' => 4,
  'previous' => true,
]);
```

#### Fetch only public holidays

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'public' => true,
]);
```

#### Fetch holidays for a specific subdivision

```php
$holiday_api->holidays([
  'country' => 'GB-ENG',
  'year' => 2019,
]);
```

#### Include subdivision holidays with countrywide holidays

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'subdivisions' => true,
]);
```

#### Search for a holiday by name

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'search' => 'New Year',
]);
```

#### Translate holidays to another language

```php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'language' => 'zh', // Chinese (Simplified)
]);
```

#### Fetch holidays for multiple countries

```php
$holiday_api->holidays([
  'country' => 'US,GB,NZ',
  'year' => 2019,
]);

$holiday_api->holidays([
  'country' => ['US', 'GB', 'NZ'],
  'year' => 2019,
]);
```
