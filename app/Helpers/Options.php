<?php
/**
 * This file is part of the WhereIndonesia Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// if ( ! function_exists('members_categories')) {
// 	function members_categories(){
// 		$data = models(\App\Api\Modules\Members\Models\Members\Categories::class)->all();
// 		if (count($data)) {
// 			return $data;
// 		}
// 		return false;
// 	}
// }
use O2System\Framework\DataStructures\Commons\Money;
use App\Api\Modules\System\Models\Modules\Settings;

if ( ! function_exists('members_number')) {
	function members_number(){
		$data = O2System\Security\Generators\Uid::generate();
		return $data;
	}
}

if ( ! function_exists('cities')) {
	function cities(){
		$data = models(\App\Api\Modules\Master\Models\Geodirectories::class)->cities();
		if (count($data)) {
			return $data;
		}
		return false;
	}
}

if ( ! function_exists('geodirectory_types')) {
	function geodirectory_types(){
		$data = [
			'CONTINENT' => language('CONTINENT'),
			'SUBCONTINENT'    => language('SUBCONTINENT'),
			'COUNTRY' => language('COUNTRY'),
			'STATE' => language('STATE'),
			'PROVINCE' => language('PROVINCE'),
			'CITY' => language('CITY'),
			'DISTRICT' => language('DISTRICT'),
			'SUBDISTRICT' => language('SUBDISTRICT'),
			'VILLAGE' => language('VILLAGE'),
		];
		return $data;
	}
}

if ( ! function_exists('visibilityOptions')) {
	function visibilityOptions()
    {
        return [
            'PUBLIC' => language('PUBLIC'),
            'READONLY' => language('READONLY'),
            'PROTECTED' => language('PROTECTED'),
            'PRIVATE' => language('PRIVATE')
        ];
    }
}

if ( ! function_exists('posts_status')) {
	function posts_status()
    {
        return [
            'PUBLISH' => language('PUBLISH'),
            'DRAFT' => language('DRAFT'),
        ];
    }
}

if ( ! function_exists('masterdata_status')) {
	function masterdata_status()
    {
        return [
            'PUBLISH' => language('OPEN'),
            'UNPUBLISH' => language('LOCKED'),
        ];
    }
}

if ( ! function_exists('world_languages')) {
	function world_languages()
    {
        return [
            'ENGLISH' => language('ENGLISH'),
            'INDONESIA' => language('INDONESIA'),
        ];
    }
}

if ( ! function_exists('companies_categories')) {
	function companies_categories()
	{
		$data = models(App\Api\Modules\Companies\Models\Categories::class)->all();
		if ($data) {
			return $data;
		}
		return false;
	}
}

if ( ! function_exists('site_name_page_title')) {
	function site_name_page_title()
    {
        return [
            'AFTER' => language('AFTER'),
            'BEFORE' => language('BEFORE'),
            'NO' => language('NO')
        ];
    }
}

if ( ! function_exists('site_timezone')) {
	function site_timezone()
    {
        return [
            'JAKARTA' => language('JAKARTA'),
            'BANDUNG' => language('BANDUNG')
        ];
    }
}

if ( ! function_exists('site_offline_message')) {
	function site_offline_message()
    {
        return [
            'HIDE' => language('HIDE'),
            'MAINTENANCE' => language('MAINTENANCE'),
            'CUSTOM_MESSAGE' => language('CUSTOM_MESSAGE'),
            'DEFAULT' => language('USE_DEFAULT_SYSTEM'),
        ];
    }
}

if ( ! function_exists('meta_robot')) {
	function meta_robot()
    {
        return [
            'INDEX_FOLLOW' => language('Index, Follow'),
            'NOINDEX_FOLLOW' => language('No Index, Follow'),
            'INDEX_NOFOLLOW' => language('Index, No Follow'),
            'NOINDEX_NOFOLLOW' => language('No Index, No Follow'),
        ];
    }
}

if ( ! function_exists('compose_post_format')) {
    function compose_post_format()
    {
        return [
            'STANDARD' => language('STANDARD'),
        ];
    }
}

if ( ! function_exists('compose_time_format')) {
    function compose_time_format()
    {
        return [
            'yyyy-mm-dd' => "2017-01-11",
        ];
    }
}

if ( ! function_exists('emails_mailer')) {
    function emails_mailer()
    {
        return [
            'PHPMAIL' => "PHP Mail",
            'SMTP' => "SMTP",
            'SENDMAIL' => "Send Email",
            'MANDRILL' => "Mandrill",
            'SPARKPOST' => "Sparkpost",
            'MAILGUN' => "Mailgun",
        ];
    }
}

if ( ! function_exists('help_server')) {
    function help_server()
    {
        return [
            'EN' => language('EN_ENGLISH'),
            'ID' => language('ID_INDONESIA'),
        ];
    }
}

if ( ! function_exists('cache_handler')) {
    function cache_handler()
    {
        return [
            'FILE' => language('FILE'),
            'MEMCACHED' => language('MEMCACHED'),
        ];
    }
}

if ( ! function_exists('system_cache')) {
    function system_cache()
    {
        return [
            0 => language('OFF'),
            1 => language('ON_CONSERVATIVE_CACHING'),
            2 => language('ON_PROGRESSIVE_CACHING'),
        ];
    }
}

if ( ! function_exists('session_handler')) {
    function session_handler()
    {
        return [
            'MEMCACHED' => language('MEMCACHED'),
            'PHP' => language('PHP'),
            'DATABASE' => language('DATABASE'),
        ];
    }
}

if ( ! function_exists('error_reporting_systems')) {
    function error_reporting_systems()
    {
        return [
            'DEFAULT' => language('SYSTEM_DEFAULT'),
            'NONE' => language('NONE'),
            'SIMPLE' => language('SIMPLE'),
            'MAXIMUM' => language('MAXIMUM'),
            'DEVELOPMENT' => language('DEVELOPMENT'),
        ];
    }
}

if ( ! function_exists('force_https')) {
    function force_https()
    {
        return [
            0 => language('NONE'),
            1 => language('ADMINISTRATOR_ONLY'),
            2 => language('ENTIRE_SITE'),
        ];
    }
}

if ( ! function_exists('database_type')) {
    function database_type()
    {
        return [
            'MYSQLI' => language('MYSQLI'),
            'PDOMYSQL' => language('MYSQL_PDO'),
        ];
    }
}

if ( ! function_exists('target_gender')) {
    function target_gender()
    {
        return [
            'ALL' => language('ALL'),
            'MALE' => language('MALE'),
            'FEMALE' => language('FEMALE'),
        ];
    }
}

if ( ! function_exists('target_age')) {
    function target_age()
    {
        return [
            'ALL' => language('ALL'),
            'ADULT' => language('ADULT'),
            'CHILD' => language('CHILD'),
        ];
    }
}

if ( ! function_exists('weight')) {
    function weight()
    {
        return [
            'GRAM' => language('GRAM_G'),
            'KG' => language('KILOGRAM_KG'),
        ];
    }
}

if ( ! function_exists('conditions')) {
    function conditions()
    {
        return [
            'NEW' => language('NEW'),
            'SECOND' => language('SECOND'),
        ];
    }
}

if ( ! function_exists('length_width_height')) {
    function length_width_height()
    {
        return [
            'MILLIMETERS' => language('MILLIMETERS'),
            'CENTIMETERS' => language('CENTIMETERS'),
            'METERS' => language('METERS'),
        ];
    }
}

if ( ! function_exists('insurance')) {
    function insurance()
    {
        return [
            'OPTIONAL' => language('OPTIONAL'),
            'YES' => language('YES'),
        ];
    }
}

if ( ! function_exists('variant_name')) {
    function variant_name()
    {
        return [
            'COLOR' => language('COLOR'),
            'SIZE' => language('SIZE'),
            'SHAPE' => language('SHAPE'),
            'MATERIAL' => language('MATERIAL'),
        ];
    }
}

if ( ! function_exists('contact_email')) {
    function contact_email()
    {
        if ($email = models(Settings::class)->fetch('contact_email')) {
            if ($email->contact_email) {
                return $email->contact_email;
            }
        }
        return false;
    }
}

if ( ! function_exists('subject_user_registration')) {
    function subject_user_registration()
    {
        if ($subject = models(Settings::class)->fetch('user_registration_subject')) {
            if ($subject->user_registration_subject) {
                return $subject->user_registration_subject;
            }
        }
        return 'Kredit Impian Activation';
    }
}

if ( ! function_exists('from_email')) {
    function from_email()
    {
        if ($email = models(Settings::class)->fetch('from_email')) {
            if ($email->from_email) {
                return $email->from_email;
            }
        }
        return 'noreply@kreditimpian.id';
    }
}

if ( ! function_exists('system_limit_pagination_posts')) {
    function system_limit_pagination_posts()
    {
        if ($pagination = models(Settings::class)->fetch('display_posts_per_page')) {
            if ($pagination->display_posts_per_page) {
                return $pagination->display_posts_per_page;
            }
        }
        return null;
    }
}

if ( ! function_exists('system_limit_pagination_testimonials')) {
    function system_limit_pagination_testimonials()
    {
        if ($radio_box = models(Settings::class)->fetch('display_testimonial')) {
            if ($radio_box->display_testimonial) {
                if ($radio_box->display_testimonial == 'on') {
                    if ($pagination = models(Settings::class)->fetch('display_testimonials_per_page')) {
                        if ($pagination->display_testimonials_per_page) {
                            return $pagination->display_testimonials_per_page;
                        }
                    }
                }
            }
        }
        return null;
    }
}

if ( ! function_exists('system_limit_pagination_portfolio')) {
    function system_limit_pagination_portfolio()
    {
        if ($radio_box = models(Settings::class)->fetch('display_portfolio')) {
            if ($radio_box->display_portfolio) {
                if ($radio_box->display_portfolio == 'on') {
                    if ($pagination = models(Settings::class)->fetch('display_portfolios_per_page')) {
                        if ($pagination->display_portfolios_per_page) {
                            return $pagination->display_portfolios_per_page;
                        }
                    }
                }
            }
        }
        return null;
    }
}

if ( ! function_exists('choose_yes_no')) {
    function choose_yes_no()
    {
        return [
            'YES' => language('YES'),
            'NO' => language('NO'),
        ];
    }
}

if ( ! function_exists('residence_status')) {
    function residence_status()
    {
        return [
            'KONTRAK' => language('KONTRAK'),
            'RUMAH_SENDIRI' => language('RUMAH_SENDIRI'),
            'KOS' => language('KOS'),
        ];
    }
}

// if ( ! function_exists('money_product')) {
//     function money_product($amount, $currency)
//     {
//         $money = new Money($amount);
//         $money->currencyFormat(null, $currency);
//         return $money;
//     }
// }
