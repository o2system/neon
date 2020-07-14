![alt text](https://repository-images.githubusercontent.com/99890241/19c33380-5c9f-11ea-92ff-3fd63a3cf7ea "O2System Email Atom")

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/o2system/email/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/o2system/email/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/o2system/email/badges/build.png?b=master)](https://scrutinizer-ci.com/g/o2system/email/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/o2system/email/v/stable)](https://packagist.org/packages/o2system/email)
[![Total Downloads](https://poser.pugx.org/o2system/email/downloads)](https://packagist.org/packages/o2system/email)
[![License](https://poser.pugx.org/o2system/email/license)](https://packagist.org/packages/o2system/email)

# O2System Email
O2System Email is a PHP Email Protocol Sender Library which is build for working more powerful with O2System Framework, but also can be used for integrated with others as standalone version with limited features.

### Composer Installation
The best way to install O2System Email is to use [Composer](https://getcomposer.org)
```
composer require o2system/email
```
> Packagist: [https://packagist.org/packages/o2system/email](https://packagist.org/packages/o2system/email)

### Usage
```php
use O2System\Email;

// Create new email message
$message = new Email\Message();
$message->from( 'o2system.framework@gmail.com', 'O2System Framework' );
$message->subject( 'Testing email message' );
$message->body('This is testing email message body content.');
$message->to('mail@steevenz.com', 'Steeven Andrian Salim');
$message->priority( Email\Message::PRIORITY_HIGHEST );

// Create new email spool
$spool = new Email\Spool([
    'protocol' => 'mail'
]);

$spool->send( $message );
```

Documentation is available on this repository [wiki](https://github.com/o2system/email/wiki) or visit this repository [github page](https://o2system.github.io/email).

### Ideas and Suggestions
Please kindly mail us at [contact@o2system.id](mailto:contact@o2system.id])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/email/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/email/pulls) to this repository.

### System Requirements
- PHP 7.2+
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)
- [PHPMailer](https://github.com/PHPMailer/PHPMailer)
- [CSS To Inline Styles](https://github.com/tijsverkoyen/CssToInlineStyles)
