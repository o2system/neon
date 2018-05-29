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

namespace App\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Traits\ModifierTrait;

/**
 * Class Posts
 * @package App\Models
 */
class Posts extends \Site\Models\Posts
{
    use ModifierTrait;

    public function insert(array $sets, $table = null)
    {
        $insertSitePosts = $this->qb
            ->table($this->table)
            ->insert([
                'title'                   => $sets[ 'title' ],
                'content'                 => $sets[ 'content' ],
                'excerpt'                 => $sets[ 'excerpt' ],
                'segments'                => $sets[ 'segments' ],
                'language'                => isset($sets[ 'language' ]) ? $sets[ 'language' ] : language()->getDefault(),
                'visibility'              => $sets[ 'visibility' ],
                'featured'                => isset($sets[ 'featured' ]) ? 'YES' : 'NO',
                'format'                  => $sets[ 'format' ],
                'publish_start'           => isset($sets[ 'publish' ][ 'start' ]) ? $sets[ 'publish' ][ 'start' ] : null,
                'publish_end'             => isset($sets[ 'publish' ][ 'end' ]) ? $sets[ 'publish' ][ 'end' ] : null,
                'record_status'           => $sets[ 'status' ],
                'record_create_user'      => services()->user->getAccount()->id,
                'record_create_timestamp' => date('Y-m-d H:i:s'),
                'record_update_user'      => services()->user->getAccount()->id,
            ]);

        if ($insertSitePosts) {
            $table = $this->table . '_meta';
            $sets[ 'id' ] = $this->db->getLastInsertId();

            foreach ($sets[ 'meta' ] as $name => $content) {
                $this->qb
                    ->table($table)
                    ->insert([
                        'id_section_post'         => $sets[ 'id' ],
                        'name'                    => $name,
                        'content'                 => $content,
                        'record_create_user'      => services()->user->getAccount()->id,
                        'record_create_timestamp' => date('Y-m-d H:i:s'),
                    ]);
            }

            foreach ($sets[ 'settings' ] as $key => $value) {
                $this->qb
                    ->table($table)
                    ->insert([
                        'id_section_post'         => $sets[ 'id' ],
                        'key'                     => $key,
                        'value'                   => $value,
                        'record_create_user'      => services()->user->getAccount()->id,
                        'record_create_timestamp' => date('Y-m-d H:i:s'),
                    ]);
            }
        }
    }

    public function update(array $sets, $where = [], $table = null)
    {
        $updateSitePosts = $this->qb
            ->table($this->table)
            ->update([
                'title'              => $sets[ 'title' ],
                'content'            => $sets[ 'content' ],
                'excerpt'            => $sets[ 'excerpt' ],
                'segments'           => $sets[ 'segments' ],
                'language'           => isset($sets[ 'language' ]) ? $sets[ 'language' ] : language()->getDefault(),
                'visibility'         => $sets[ 'visibility' ],
                'featured'           => isset($sets[ 'featured' ]) ? 'YES' : 'NO',
                'format'             => $sets[ 'format' ],
                'publish_start'      => isset($sets[ 'publish' ][ 'start' ]) ? $sets[ 'publish' ][ 'start' ] : null,
                'publish_end'        => isset($sets[ 'publish' ][ 'end' ]) ? $sets[ 'publish' ][ 'end' ] : null,
                'record_status'      => $sets[ 'status' ],
                'record_update_user' => services()->user->getAccount()->id,
            ], $where);

        if ($updateSitePosts) {
            if (isset($sets[ 'meta' ])) {
                $table = $this->table . '_meta';

                // Get existing meta
                $result = $this->qb
                    ->select([
                        'id',
                        'name',
                    ])
                    ->from($table)
                    ->where('id_section_post', $sets[ 'id' ])
                    ->get();

                $existingMeta = [];
                if ($result) {
                    foreach ($result as $row) {
                        if ( ! isset($sets[ 'meta' ][ $row->name ])) {
                            $this->qb
                                ->table($table)
                                ->update([
                                    'record_status'      => 'TRASH',
                                    'record_update_user' => services()->user->getAccount()->id,
                                    'record_delete_user' => services()->user->getAccount()->id,
                                ], ['id' => $row->id]);
                        } else {
                            $existingMeta[] = $row->name;
                        }
                    }
                }

                foreach ($sets[ 'meta' ] as $name => $content) {
                    if (in_array($name, $existingMeta)) {
                        $this->qb
                            ->table($table)
                            ->update([
                                'content'            => $content,
                                'record_status'      => 'PUBLISH',
                                'record_update_user' => services()->user->getAccount()->id,
                            ], [
                                'id_section_post' => $sets[ 'id' ],
                                'name'            => $name,
                            ]);
                    } else {
                        $this->qb
                            ->table($table)
                            ->insert([
                                'id_section_post'         => $sets[ 'id' ],
                                'name'                    => $name,
                                'content'                 => $content,
                                'record_create_user'      => services()->user->getAccount()->id,
                                'record_create_timestamp' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }

            if (isset($sets[ 'settings' ])) {
                $table = $this->table . '_settings';

                // Get existing settings
                $result = $this->qb
                    ->select([
                        'id',
                        'key',
                        'value',
                    ])
                    ->from($table)
                    ->where('id_section_post', $sets[ 'id' ])
                    ->get();

                $existingSettings = [];
                if ($result) {
                    foreach ($result as $row) {
                        if ( ! isset($sets[ 'settings' ][ $row->key ])) {
                            $this->qb
                                ->table($table)
                                ->update([
                                    'value'              => ($row->value === 'on' ? 'off' : 'on'),
                                    'record_update_user' => services()->user->getAccount()->id,
                                    'record_delete_user' => services()->user->getAccount()->id,
                                ], ['id' => $row->id]);
                        } else {
                            $existingSettings[] = $row->key;
                        }
                    }
                }

                foreach ($sets[ 'settings' ] as $key => $value) {
                    if (in_array($key, $existingSettings)) {
                        $this->qb
                            ->table($table)
                            ->update([
                                'value'              => $value,
                                'record_status'      => 'PUBLISH',
                                'record_update_user' => services()->user->getAccount()->id,
                            ], [
                                'id_section_post' => $sets[ 'id' ],
                                'key'             => $key,
                            ]);
                    } else {
                        $this->qb
                            ->table($table)
                            ->insert([
                                'id_section_post'         => $sets[ 'id' ],
                                'key'                     => $key,
                                'value'                   => $value,
                                'record_create_user'      => services()->user->getAccount()->id,
                                'record_create_timestamp' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }

            return true;
        }

        return false;
    }
}