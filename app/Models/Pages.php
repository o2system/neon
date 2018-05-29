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
 * Class Pages
 * @package App\Models
 */
class Pages extends \Site\Models\Pages
{
    use ModifierTrait;

    public function insert(array $sets, $table = null)
    {
        $insertSitePages = $this->qb
            ->table($this->table)
            ->insert([
                'title'                   => $sets[ 'title' ],
                'content'                 => $sets[ 'content' ],
                'segments'                => $sets[ 'segments' ],
                'language'                => isset($sets[ 'language' ]) ? $sets[ 'language' ] : language()->getDefault(),
                'image'                   => $sets[ 'image' ],
                'visibility'              => $sets[ 'visibility' ],
                'record_status'           => $sets[ 'status' ],
                'record_create_user'      => services()->user->getAccount()->id,
                'record_create_timestamp' => date('Y-m-d H:i:s'),
                'record_update_user'      => services()->user->getAccount()->id,
            ]);

        if ($insertSitePages) {
            $table = $this->table . '_meta';
            $sets[ 'id' ] = $this->db->getLastInsertId();

            foreach ($sets[ 'meta' ] as $name => $content) {
                $this->qb
                    ->table($table)
                    ->insert([
                        'id_page'                 => $sets[ 'id' ],
                        'name'                    => $name,
                        'content'                 => $content,
                        'record_create_user'      => services()->user->getAccount()->id,
                        'record_create_timestamp' => date('Y-m-d H:i:s'),
                    ]);
            }
        }
    }

    public function update(array $sets, $where = [], $table = null)
    {
        $updateSitePages = $this->qb
            ->table($this->table)
            ->update([
                'title'              => $sets[ 'title' ],
                'content'            => $sets[ 'content' ],
                'segments'           => $sets[ 'segments' ],
                'language'           => isset($sets[ 'language' ]) ? $sets[ 'language' ] : language()->getDefault(),
                'image'              => $sets[ 'image' ],
                'visibility'         => $sets[ 'visibility' ],
                'record_status'      => $sets[ 'status' ],
                'record_update_user' => services()->user->getAccount()->id,
            ], $where);

        if ($updateSitePages) {
            if (isset($sets[ 'meta' ])) {
                $table = $this->table . '_meta';

                // Get existing meta
                $result = $this->qb
                    ->select([
                        'id',
                        'name',
                    ])
                    ->from($table)
                    ->where('id_page', $sets[ 'id' ])
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
                                'id_page' => $sets[ 'id' ],
                                'name'    => $name,
                            ]);
                    } else {
                        $this->qb
                            ->table($table)
                            ->insert([
                                'id_page'                 => $sets[ 'id' ],
                                'name'                    => $name,
                                'content'                 => $content,
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