![alt text](https://repository-images.githubusercontent.com/68260766/7c1c3400-5c9f-11ea-9699-82997594a72b "O2System Filesystem Atom")

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/o2system/filesystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/o2system/filesystem/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/o2system/filesystem/badges/build.png?b=master)](https://scrutinizer-ci.com/g/o2system/filesystem/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/o2system/filesystem/v/stable)](https://packagist.org/packages/o2system/filesystem)
[![Total Downloads](https://poser.pugx.org/o2system/filesystem/downloads)](https://packagist.org/packages/o2system/filesystem)
[![License](https://poser.pugx.org/o2system/filesystem/license)](https://packagist.org/packages/o2system/filesystem)

# O2System Filesystem
O2System Filesystem is an Open Source PHP Convenience Library for reading, writing and appending data from and into files and directories, which is built for working more powerfully with O2System Framework, but it also can be used with other frameworks as a standalone version with limited features.

### Supported Files Processor and Generator
- CSV File
- INI File
- JSON File
- XML File
- Zip File

### Features Handlers
- File Handler and Manipulation
- Directory Handler and Manipulation
- Uploaders
- Downloaders with speed limit and resumeable support
- File Transfer Protocol (FTP)

### Composer Installation
The best way to install O2System Filesystem is to use [Composer](https://getcomposer.org)
```
composer require o2system/filesystem
```
> Packagist: [https://packagist.org/packages/o2system/filesystem](https://packagist.org/packages/o2system/filesystem)

### Usage
```php
use O2System\Filesystem\Files;

// Write a CSV file example
$csvFile = new Files\CsvFile();
$csvFile->createFile( 'path/to/files/filename.csv' );
$csvFile->store( 'foo', 'bar' );
$csvFile->writeFile();

// File download handler
$downloader = new Handlers\Downloader( 'path/to/files/downloadthis.zip' );
$downloader
    ->speedLimit( 1024 )
    ->resumeable( true );

// Send the requested download file
$downloader->download();
```

Documentation is available on this repository [wiki](https://github.com/o2system/filesystem/wiki) or visit this repository [github page](https://o2system.github.io/filesystem).

### Ideas and Suggestions
Please kindly mail us at [hello@o2system.id](mailto:hello@o2system.id])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/filesystem/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/filesystem/pulls) to this repository.

### System Requirements
- PHP 7.2+ with FileInfo (finfo) extension
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)
