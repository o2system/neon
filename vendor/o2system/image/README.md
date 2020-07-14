![alt text](https://repository-images.githubusercontent.com/86801117/bf76a280-5c9f-11ea-9878-bb48b9d6f3bb "O2System Image Atom")

[![Build Status](https://scrutinizer-ci.com/g/o2system/image/badges/build.png?b=master)](https://scrutinizer-ci.com/g/o2system/image/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/o2system/image/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/o2system/image/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/o2system/image/v/stable)](https://packagist.org/packages/o2system/image)
[![Total Downloads](https://poser.pugx.org/o2system/image/downloads)](https://packagist.org/packages/o2system/image)
[![License](https://poser.pugx.org/o2system/image/license)](https://packagist.org/packages/o2system/image)

# O2System Image
O2System Image is a PHP image handling and manipulation library for O2System Framework which provides an easier and expressive way to manipulate an image. It allows different PHP Image Processor and Generator to be used.

### Supported PHP Image Processors and Generator Drivers
| Processors | Support | Tested  | &nbsp; |
| ------------- |:-------------:|:-----:| ----- |
| GD2 | ```Yes``` | ```Yes``` | http://php.net/image |
| GMagick | ```Yes``` | ```Yes``` | http://php.net/gmagick |
| ImageMagick | ```Yes``` | ```Yes``` | http://php.net/imagemagick |

### Composer Installation
The best way to install O2System Image is to use [Composer](https://getcomposer.org)
```
composer require o2system/image
```
> Packagist: [https://packagist.org/packages/o2system/image](https://packagist.org/packages/o2system/image)

### Usage
```php
use O2System\Image;

// Manipulate Image
$manipulation = new Image\Manipulation();
$manipulation->setImageFile( 'path/to/images/kawah-putih.jpg' );
$manipulation->scaleImage( 15 );

// Watermark Image
$manipulation->watermarkImage( ( new Text() )
            ->setPosition( 'MIDDLE_BOTTOM' )
            ->setPadding( 10 )
            ->signature( 'Braunberrie Timeless Portraiture' )
            ->copyright( 'Copyright © ' . date( 'Y' ) . ' Poniman Mulijadi' . PHP_EOL . 'Braunberrie Timeless Portraiture' )
        );

// Send to browser
$manipulation->displayImage();

// Save Image
$manipulation->saveImage( 'path/to/save/images/kawah-putih.jpg' );
```

Documentation is available on this repository [wiki](https://github.com/o2system/image/wiki) or visit this repository [github page](https://o2system.github.io/image).

### Ideas and Suggestions
Please kindly mail us at [contact@o2system.id](mailto:contact@o2system.id])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/image/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/image/pulls) to this repository.

### System Requirements
- PHP 7.2+
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)
- [Image Optimizer](https://github.com/psliwa/image-optimizer) by [Piotr Śliwa](https://github.com/psliwa)

### Fonts Credits
* Jellyka Saint Andrew's Queen by [Jellyka Neveran](http://www.cuttyfruty.com/enhtml/jellyka.php) used as default signature font.
* Express Way Regular - Truetype Font by [Typodermic Fonts](http://typodermicfonts.com) used as default copyright font.

### Photographs Example Credits
* Kawah Putih by Poniman Mulijadi - Braunberrie Timeless Portraiture
> All photographs above is used as examples in the script O2System Framework.
