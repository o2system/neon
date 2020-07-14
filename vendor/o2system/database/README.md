![alt text](https://repository-images.githubusercontent.com/68260497/a3bccd80-5c9a-11ea-9c86-1a12a79529be "O2System Database Atom")

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/o2system/database/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/o2system/database/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/o2system/database/badges/build.png?b=master)](https://scrutinizer-ci.com/g/o2system/database/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/o2system/database/v/stable)](https://packagist.org/packages/o2system/database)
[![Total Downloads](https://poser.pugx.org/o2system/database/downloads)](https://packagist.org/packages/o2system/database)
[![License](https://poser.pugx.org/o2system/database/license)](https://packagist.org/packages/o2system/database)

# O2System Database
O2System Database is an Open Source PHP Database Drivers Library. The database drivers in the O2System Database are grouped into two main categories: SQL and NoSQL. O2System Database is built for working more powerfully within <the> O2System Framework, but it can also be used within other frameworks as standalone version with limited features.

### Supported Remote Storage SQL Database Engines Drivers
| Engine | 7.2+  | &nbsp; |
| ------------- |:-----:| ----- |
| MySQL / MariaDB | ```Yes``` | http://php.net/manual/en/book.mysqli.php |
| PostgreSQL | ```Yes``` | http://php.net/manual/en/book.pgsql.php |
| Microsoft SQL Server | ```Yes``` | http://php.net/manual/en/book.mssql.php |
| Oracle OCI8 | ```Yes``` | http://php.net/manual/en/book.oci8.php |
> Currently we only support for MySQL / MariaDB only.

### Supported Local Storage SQL Database Engines Drivers
| Engine | 7.2+  | &nbsp; |
| ------------- |:-----:| ----- |
| SQLite3 | ```Yes``` | http://php.net/manual/en/book.sqlite3.php |
| Microsoft Access | ```Yes``` | - |
> Currently we only support for SQLite3 only.

### Supported NoSQL Database Engines Drivers
| Engine | 7.2+  | &nbsp; |
| ------------- |:-----:| ----- |
| MongoDB | ```Yes``` | http://php.net/manual/en/set.mongodb.php |

### Composer Installation
The best way to install O2System Database is to use [Composer](https://getcomposer.org)
```
composer require o2system/database
```
> Packagist: [https://packagist.org/packages/o2system/database](https://packagist.org/packages/o2system/database)

### Usage
```php
use O2System\Database;
```

Documentation is available on this repository [wiki](https://github.com/o2system/database/wiki) or visit this repository [github page](https://o2system.github.io/database).

### Ideas and Suggestions
Please kindly mail us at [contact@o2system.id](mailto:contact@o2system.id])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/database/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/database/pulls) to this repository.

### System Requirements
- PHP 7.2+ with MySQLi or MongoDB Extension
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)
