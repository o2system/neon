![alt text](https://repository-images.githubusercontent.com/68260983/1ed0b480-5c98-11ea-8e47-b2e41939b1c1 "O2System Session Atom")

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/o2system/session/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/o2system/session/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/o2system/session/badges/build.png?b=master)](https://scrutinizer-ci.com/g/o2system/session/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/o2system/session/v/stable)](https://packagist.org/packages/o2system/session)
[![Total Downloads](https://poser.pugx.org/o2system/session/downloads)](https://packagist.org/packages/o2system/session)
[![License](https://poser.pugx.org/o2system/session/license)](https://packagist.org/packages/o2system/session)

# O2System Session
[O2System Session](https://github.com/o2system/session) is an Open Source Native PHP Session Management Handler Library.
It allows different cache storage platform to be used.
All but file-based storage require specific server requirements, and a Fatal Exception will be thrown if server requirements are not met.

[O2System Session](https://github.com/o2system/session) is build for working more powerful with [O2System PHP Framework](https://github.com/o2system/o2system), but also can be integrated with other frameworks as standalone PHP Classes Library with limited features.

### Supported Storage Engines Handlers
| Engine | 7.2+  | &nbsp; |
| ------------- |:-----:| ----- |
| APCu | ```Yes``` | http://php.net/apcu |
| File | ```Yes``` | http://php.net/file |
| Memcache | ```Yes``` | http://php.net/memcache |
| Memcached | ```Yes``` | http://php.net/memcached |
| Redis | ```Yes``` | http://redis.io |
| Wincache | ```Yes``` | http://php.net/wincache |
> APC and XCache has been deprecated, OPCache is merged into APCu

### Composer Installation
The best way to install O2System Session is to use [Composer](https://getcomposer.org)
```
composer require o2system/session
```
> Packagist: [https://packagist.org/packages/o2system/session](https://packagist.org/packages/o2system/session)

### Usage
Documentation is available on this repository [wiki](https://github.com/o2system/session/wiki) or visit this repository [github page](https://o2system.github.io/session).

### Ideas and Suggestions
Please kindly mail us at [contact@o2system.id](mailto:contact@o2system.id])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/session/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/session/pulls) to this repository.

### System Requirements
- PHP 7.2+
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)
