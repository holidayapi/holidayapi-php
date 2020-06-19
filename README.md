# Holiday API PHP Library

[![License](https://img.shields.io/packagist/l/holidayapi/holidayapi-php?style=for-the-badge)](https://github.com/holidayapi/holidayapi-php/blob/master/LICENSE)
![PHP Version](https://img.shields.io/packagist/php-v/holidayapi/holidayapi-php?style=for-the-badge)
[![Test Status](https://img.shields.io/github/workflow/status/joshtronic/holidayapi-php/Test?style=for-the-badge)](https://github.com/joshtronic/holidayapi-php/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/joshtronic/holidayapi-php?style=for-the-badge)](https://codecov.io/gh/joshtronic/holidayapi-php)

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
<?php
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
<?php
$holiday_api->countries();
```

#### Fetch only countries with public holidays

```php
<?php
$holiday_api->countries([
  'public' => true,
]);
```

#### Fetch a supported country by code

```php
<?php
$holiday_api->countries([
  'country' => 'NO',
]);
```

#### Search for countries by code or name

```php
<?php
$holiday_api->countries([
  'search' => 'united',
]);
```

### Languages

#### Fetch all supported languages

```php
<?php
$holiday_api->languages();
```

#### Fetch a supported language by code

```php
<?php
$holiday_api->languages([
  'language' => 'es',
]);
```

#### Search for languages by code or name

```php
<?php
$holiday_api->languages([
  'search' => 'Chinese',
]);
```

### Holidays

#### Fetch holidays for a specific year

```php
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
]);
```

#### Fetch holidays for a specific month

```php
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'month' => 7,
]);
```

#### Fetch holidays for a specific day

```php
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'month' => 7,
  'day' => 4,
]);
```

#### Fetch upcoming holidays based on a specific date

```php
<?php
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
<?php
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
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'public' => true,
]);
```

#### Fetch holidays for a specific subdivision

```php
<?php
$holiday_api->holidays([
  'country' => 'GB-ENG',
  'year' => 2019,
]);
```

#### Include subdivision holidays with countrywide holidays

```php
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'subdivisions' => true,
]);
```

#### Search for a holiday by name

```php
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'search' => 'New Year',
]);
```

#### Translate holidays to another language

```php
<?php
$holiday_api->holidays([
  'country' => 'US',
  'year' => 2019,
  'language' => 'zh', // Chinese (Simplified)
]);
```

#### Fetch holidays for multiple countries

```php
<?php
$holiday_api->holidays([
  'country' => 'US,GB,NZ',
  'year' => 2019,
]);

$holiday_api->holidays([
  'country' => ['US', 'GB', 'NZ'],
  'year' => 2019,
]);
```

### Workday

#### Fetch workday 7 business days after a date

```php
<?php
$holiday_api->workday([
  'country' => 'US',
  'start' => '2019-07-01',
  'days' => 7,
]);
```
