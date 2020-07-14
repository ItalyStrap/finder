# ItalyStrap Finder

[![Build Status](https://travis-ci.org/ItalyStrap/finder.svg?branch=master)](https://travis-ci.org/ItalyStrap/finder)
[![Latest Stable Version](https://img.shields.io/packagist/v/italystrap/finder.svg)](https://packagist.org/packages/italystrap/finder)
[![Total Downloads](https://img.shields.io/packagist/dt/italystrap/finder.svg)](https://packagist.org/packages/italystrap/finder)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/italystrap/finder.svg)](https://packagist.org/packages/italystrap/finder)
[![License](https://img.shields.io/packagist/l/italystrap/finder.svg)](https://packagist.org/packages/italystrap/finder)
![PHP from Packagist](https://img.shields.io/packagist/php-v/italystrap/finder)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FItalyStrap%2Ffinder%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/ItalyStrap/finder/master)

Some description the OOP way

## Table Of Contents

* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Advanced Usage](#advanced-usage)
* [Contributing](#contributing)
* [License](#license)

## Installation

The best way to use this package is through Composer:

```CMD
composer require italystrap/finder
```
This package adheres to the [SemVer](http://semver.org/) specification and will be fully backward compatible between minor versions.

## Basic Usage

### Feature: search the specialized file inside directories

    Given a list of file name
        And a list of directories to search on
    When I search a file
    Then the highest specialized file is returned

**Basic example**

_files_

```php
    $list_of_file = [
        'file-specialized.php',
        'file.php',
    ];
```
_directories_
```php
    $dirs = [
        'my/theme/child/template', // First dir to search the file
        'my/theme/parent/template', // Second dir to search the file
    ];
```

If `file-specialized.php` exists in one of the given directories it will return the name and full path of the file.
`my/theme/child/template/file-specialized.php`
or
`my/theme/parent/template/file-specialized.php`

If the `file-specialized.php` is not found then will search for `file.php` and return full path if exists
`my/theme/child/template/file.php`
or
`my/theme/parent/template/file.php`

If no `file.php` is founded it will throw an error message.

### Feature: search the asset file with priority

    Given a list of file name
        And a list of directories to search on
    When I search an asset file
    Then the file with highest priority file is returned

**Basic example**

_files_

```php
    $min = \defined( 'WP_DEBUG' ) && WP_DEBUG ? '.min' : '';

    $list_of_file = [
        'style' . $min . '.css',
    ];
```
_directories_
```php
    $dirs = [
        'my/theme/child/asset/css', // First dir to search the file
        'my/theme/parent/asset/css', // Second dir to search the file
    ];
```

If `style` exists in one of the given directories it will return the name and full path of the file from the directory with highest priority
`my/theme/child/asset/css/style.css`

If the `style.css` is not found in the child directory then will search in parent directory and return full path if exists
`my/theme/parent/asset/css/style.css`

If no `style.css` is founded it will throw an error message.

### Feature: search the config files

    Given a name of a config file
        And a list of directories to search on
    When I search the config files
    Then The list of file founded are returned sorted by priority of directory sort

**Basic example**

_files_

```php

    $list_of_file = [
        'config.php',
    ];
```
_directories_
```php
    $dirs = [
        'my/theme/child/config', // First dir to search the file
        'my/theme/parent/config', // Second dir to search the file
    ];
```

If `config.php` exists in one or all of the given directories it will return the name and full path of the files from the directory
`my/theme/child/config/config.php`
`my/theme/parent/config/config.php`

If no `config.php` is founded it will throw an error message.


## Advanced Usage

## Contributing

All feedback / bug reports / pull requests are welcome.

## License

Copyright (c) 2019 Enea Overclokk, ItalyStrap

This code is licensed under the [MIT](LICENSE).

## Credits
