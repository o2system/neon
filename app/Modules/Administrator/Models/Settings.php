<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */

// ------------------------------------------------------------------------

namespace Administrator\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Settings
 * @package Administrator\Models
 */
class Settings extends Model
{
    const DEFAULT_SITE_META = [
        'meta_title'       => '',
        'meta_description' => '',
        'meta_robot'       => '',
        'meta_keywords'    => '',
    ];

    const DEFAULT_SITE = [
        'site_title'    => '',
        'site_tagline'  => '',
        'site_language' => '',
        'site_timezone' => '',
        'site_address'  => '',
        'site_logo'     => null,
        'site_offline'  => 'off',
    ];

    const DEFAULT_SITE_PRIVACY = [
        'site_privacy' => 0,
    ];

    const DEFAULT_WRITE = [
        'write_pages'             => 10,
        'write_portfolio'         => false,
        'write_testimonial'       => false,
        'write_testimonial_pages' => 10,
        'write_portfolio_pages'   => 10,
    ];

    const DEFAULT_COMPOSING = [
        'write_privacy'   => false,
        'write_language'  => 'ENGLISH',
        'write_timestamp' => 'yyyy-mm-dd',
    ];

    const DEFAULT_DISCUSS = [
        'discuss_author_email'       => false,
        'discuss_must_login'         => false,
        'discuss_close_comment'      => false,
        'discuss_close_comment_days' => 7,
        'discuss_break_comments'     => false,
        'discuss_breaks'             => 50,
        'discuss_per_page'           => 10,
        'discuss_comment_top'        => false,
        'discuss_notify_comment'     => false,
        'discuss_notify_link'        => false,
        'discuss_notify_like'        => false,
        'discuss_notify_reblog'      => false,
        'discuss_notify_follow'      => false,
        'discuss_moderation'         => false,
        'discuss_approved'           => false,
        'comment_links_moderation'   => 2,
        'discuss_words'              => '',
        'enable_thread_comment'      => true,
        'thread_comment'             => 10,
        'discuss_comment_held_mod'   => false,
        'comment_blacklist'          => '',
    ];

    const DEFAULT_ARTICLES = [
        'discuss_notification'   => false,
        'discuss_comments'       => false,
        'discuss_allow_comments' => false,
    ];

    const DEFAULT_TRAFFIC_RELATED = [
        'traffic_show_related_post'   => false,
        'traffic_show_related_header' => false,
        'traffic_striking_layout'     => false,
    ];

    const DEFAULT_TRAFFIC_MOBILE = [
        'traffic_improve_mobile' => false,
    ];

    const DEFAULT_TRAFFIC_SEO = [

    ];

    const DEFAULT_TRAFFIC_GOOGLE_ANALYTIC = [

    ];

    const DEFAULT_TRAFFIC_SITE_VERIFICATION = [
        'traffic_google'    => '',
        'traffic_bing'      => '',
        'traffic_pinterest' => '',
        'traffic_yandex'    => '',
    ];

    public $table = 'sys_modules_settings';

    public function saveSettings($settings)
    {
        $exists = $this->db->table($this->table)->where([
            'id_sys_module' => 2,
        ])->get();

        if ($exists->count() > 0) {
            foreach ($settings as $key => $value) {
                $keyExists = $this->db->table($this->table)->where([
                    'id_sys_module' => 2,
                ])->where([
                    'key' => $key,
                ])->get(1);

                if ($keyExists->count() > 0) {
                    if ($key !== 'site_logo') {
                        $this->db->table($this->table)->where([
                            'key' => $key,
                        ])->update([
                            'value' => $value,
                        ]);
                    }

                    if ($key === 'site_logo' && strlen($value) > 0) {
                        $this->db->table($this->table)->where([
                            'key' => $key,
                        ])->update([
                            'value' => $value,
                        ]);
                    }
                } else {
                    if ( ! preg_match('/save*/', $key)) {
                        $this->db->table($this->table)->insert([
                            'key'           => $key,
                            'value'         => $value,
                            'id_sys_module' => 2,
                        ]);
                    }
                }
            }
        } else {
            $this->insertKeys($settings);
        }
    }

    public function getSetting($keys)
    {
        $dbKey = [];

        switch ($keys) {
            case 'general':
                $dbKey = array_merge(static::DEFAULT_SITE, static::DEFAULT_SITE_META, static::DEFAULT_SITE_PRIVACY);
                break;
            case 'writing':
                $dbKey = array_merge(static::DEFAULT_COMPOSING, static::DEFAULT_WRITE);
                break;
            case 'discussion':
                $dbKey = array_merge(static::DEFAULT_ARTICLES, static::DEFAULT_DISCUSS);
                break;
            case 'traffic':
                $dbKey = array_merge(static::DEFAULT_TRAFFIC_GOOGLE_ANALYTIC, static::DEFAULT_TRAFFIC_MOBILE,
                    static::DEFAULT_TRAFFIC_RELATED, static::DEFAULT_TRAFFIC_SEO,
                    static::DEFAULT_TRAFFIC_SITE_VERIFICATION);
                break;
        }

        if (count($dbKey) > 0) {
            $exists = $this->db->table($this->table)->where([
                'id_sys_module' => 2,
            ])->whereIn('key', array_keys($dbKey))->get();

            if ($exists->count() > 0) {
                $result = $dbKey;

                foreach ($exists as $key => $value) {
                    $result[ $value->key ] = $value->value;
                }

                return $result;
            }
        }

        return $dbKey;
    }

    private function insertKeys($settings)
    {
        foreach ($settings as $key => $value) {
            if ($key !== 'save') {
                $this->db->table($this->table)->insert([
                    'id_sys_module' => 2,
                    'key'           => $key,
                    'value'         => $value,
                ]);
            }
        }
    }
}