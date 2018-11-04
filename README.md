This package will generate a valid Swedish social security number.
It looks something like this: **790631-2629**
## Installation
```
composer require robbens\sssn
```

## Usage
Generate a random date and identifier.
```php
$validSsn = Sssn::make(); // 3512206537
```
Specify a date and identifier manually.
```php
$validSsn = Sssn::make('890525', '12'); // 890525-1206
```
**Note:** The identifier is 4 numbers. However, only the first two can actually be 0-99.

Specify a gender.
```php
$validMaleSsn = Sssn::make('890525')->male(); // 890525-4473

$validFemaleSsn = Sssn::make('890525')->female(); // 8905257948
```

## How the ssn is generated
TODO
