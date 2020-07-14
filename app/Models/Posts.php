<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\DataObjects\Result\Row;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\System\Calendars;
use O2System\Framework\Models\Sql\System\Media;
use O2System\Framework\Models\Sql\System\Relationships;
use O2System\Framework\Models\Sql\System\Users;
use O2System\Framework\Models\Sql\Traits\MetadataTrait;
use O2System\Framework\Models\Sql\Traits\SettingsTrait;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Posts
 * @package App\Models
 */
class Posts extends Model
{
    use MetadataTrait;
    use SettingsTrait;

    /**
     * Posts::$table
     *
     * @var string
     */
    public $table = 'posts';

    /**
     * Posts::$fillableColumns
     *
     * @var array
     */
    public $fillableColumns = [
        'id',
        'title',
        'slug',
        'content',
        'excerpt',
        'record_status',
        'record_language',
        'record_type',
        'record_visibility',
        'record_create_user',
        'record_create_timestamp',
        'record_update_user',
        'record_update_timestamp'
    ];

    /**
     * Posts::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'tags',
        'images',
        'metadata',
        'settings',
        'record'
    ];

    /**
     * Posts::$primaryKeys
     *
     * @var array
     */
    public $primaryKeys =  ['id', 'record_language'];

    /**
     * Posts::$createValidationRules
     *
     * @var array
     */
    public $insertValidationRules = [
        'title' => 'required',
        'slug' => 'required',
        'content' => 'required',
        'excerpt' => 'optional',
        'record_language' => 'optional',
        'record_type' => 'optional|listed[ARTICLE, VIDEO, AUDIO]',
        'record_status' => 'optional|listed[DELETED,ARCHIVED,DRAFT,UNPUBLISH,PUBLISH]',
        'record_visibility' => 'optional|listed[PUBLIC,READONLY,PROTECTED,PRIVATE]',
        'publish_start' => 'optional|date[Y-m-d H:i:s]',
        'publish_end' => 'optional|date[Y-m-d H:i:s]',
        'metadata.description' => 'optional',
        'metadata.keywords' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Posts::$createValidationCustomErrors
     *
     * @var array
     */
    public $insertValidationCustomErrors = [
        'title' => [
            'required' => 'Title cannot be empty!',
            'integer' => 'Title data must be an integer'
        ],
        'slug' => [
            'required' => 'slug cannot be empty!'
        ],
        'content' => [
            'required' => 'Content cannot be empty!'
        ],
        'record_type' => [
            'listed' => 'Record type be listed: ARTICLE, AUDIO or VIDEO'
        ],
        'record_status' => [
            'listed' => 'Record status must be listed: DELETED, ARCHIVED, DRAFT, UNPUBLISH, or PUBLISH'
        ],
        'record_visibility' => [
            'listed' => 'Record visibility must be listed: PUBLIC, READONLY, PROTECTED, or PRIVATE'
        ],

    ];

    // ------------------------------------------------------------------------

    /**
     * Posts::$updateValidationRules
     *
     * @var array
     */
    public $updateValidationRules = [
        'id' => 'required|integer',
        'title' => 'required',
        'slug' => 'required',
        'content' => 'required',
        'record_language' => 'optional',
        'record_type' => 'optional|listed[ARTICLE, VIDEO, AUDIO]',
        'record_status' => 'optional|listed[DELETED,ARCHIVED,DRAFT,UNPUBLISH,PUBLISH]',
        'record_visibility' => 'optional|listed[PUBLIC,READONLY,PROTECTED,PRIVATE]',
        'publish_start' => 'optional|date[Y-m-d H:i:s]',
        'publish_end' => 'optional|date[Y-m-d H:i:s]',
        'metadata.description' => 'optional',
        'metadata.keywords' => 'optional'
    ];

    // ------------------------------------------------------------------------

    /**
     * Posts::$updateValidationCustomErrors
     *
     */
    public $updateValidationCustomErrors = [
        'id' => [
            'required' => 'Post id cannot be empty!',
            'integer' => 'Post id data must be an integer'
        ],
        'title' => [
            'required' => 'Title cannot be empty!',
            'integer' => 'Title data must be an integer'
        ],
        'slug' => [
            'required' => 'slug cannot be empty!'
        ],
        'content' => [
            'required' => 'Content cannot be empty!'
        ],
        'record_type' => [
            'listed' => 'Record type be listed: ARTICLE, AUDIO or VIDEO'
        ],
        'record_status' => [
            'listed' => 'Record status must be listed: DELETED, ARCHIVED, DRAFT, UNPUBLISH, or PUBLISH'
        ],
        'record_visibility' => [
            'listed' => 'Record visibility must be listed: PUBLIC, READONLY, PROTECTED, or PRIVATE'
        ],
    ];

    // ------------------------------------------------------------------------

    /**
     * Posts::beforeInsertOrUpdate
     *
     * @param SplArrayStorage $data
     */
    public function beforeInsertOrUpdate(SplArrayStorage &$data)
    {
        $data->slug = dash($data->slug);

        /** Clean up media relation first */
        // models(Relationships::class)->deleteManyBy([
        //     'ownership_id' => implode('-', [$data->id, $data->record_language]),
        //     'ownership_model' => Posts::class
        // ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::afterInsertOrUpdate
     *
     * @param Row $data
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function afterInsertOrUpdate(&$data)
    {
        /** Categories handler */
        if (!empty($data['categories'])) {
        }

        /** Media handler */
        if (!empty($data['media'])) {

            /** Insert new relations */
            foreach($data['media'] as $key => $value) {
                $relationshipData = new SplArrayStorage();
                
                $param = [
                    'ownership_id' => implode('-', [$data->id, $data->record_language]),
                    'ownership_model' => Posts::class,
                    'relation_id' => $value,
                    'relation_model' => Media::class,
                    'relation_role' => 'GALLERY'
                ];

                $relationshipData->append($param);

                models(Relationships::class)->insertIfNotExists($relationshipData);
           }
        }

        /** Tags handler */
        if (!empty($data['tags'])) {
            $tags = explode(',', $data['tags']);
            $tags = array_map('trim', $tags);
            
            foreach($tags as $tag) {
                if($result = models(Tags::class)->findWhere(['title' => $tag])) {
                    if($result->count() == 0) {
                        $tagData = new SplArrayStorage();
                        $tagData->append([
                            'id_space' => globals()->space->id,
                            'title' => $tag
                        ]);

                        if(models(Tags::class)->insert($tagData)) {
                            $relationshipData = new SplArrayStorage();
                            $relationshipData->append([
                                'ownership_id' => implode('-', [$data->id, $data->record_language]),
                                'ownership_model' => Posts::class,
                                'relation_id' => $tagData->id,
                                'relation_model' => Tags::class,
                                'relation_role' => 'TAG'
                            ]);

                            models(Relationships::class)->insert($relationshipData);
                        } else {
                            $this->addError(models(Tags::class)->getLastErrorCode(), models(Tags::class)->getLastErrorMessage());

                            return false;
                        }
                    } else {
                        $relationshipData = new SplArrayStorage();
                        $relationshipData->append([
                            'ownership_id' => implode('-', [$data->id, $data->record_language]),
                            'ownership_model' => Posts::class,
                            'relation_id' => $result->first()->id,
                            'relation_model' => Tags::class,
                            'relation_role' => 'TAG'
                        ]);

                        models(Relationships::class)->insertIfNotExists($relationshipData);
                    }
                }
            }
        }

        /** Calendar Handler */
        if(!empty($data->publish_start)) {
            $calendar = new SplArrayStorage();
            $calendar->append([
                'ownership_id' => implode('-', [$data->id, $data->record_language]),
                'ownership_model' => Posts::class,
                'start_date' => date('Y-m-d', strtotime($data->publish_start)),
                'start_time' => date('H:i:s', strtotime($data->publish_start))
            ]);

            if( ! models(Calendars::class)->insertOrUpdate($calendar, [
                'ownership_id' => implode('-', [$data->id, $data->record_language]),
                'ownership_model' => Posts::class,
            ])) {
                return false;
            } else {
                $this->addError(models(Calendars::class)->getLastErrorCode(), models(Calendars::class)->getLastErrorMessage());

                return false;
            }
        }

        if(!empty($data->publish_end)) {
            $calendar = new SplArrayStorage();
            $calendar->append([
                'ownership_id' => implode('-', [$data->id, $data->record_language]),
                'ownership_model' => Posts::class,
                'end_date' => date('Y-m-d', strtotime($data->publish_end)),
                'end_time' => date('H:i:s', strtotime($data->publish_end))
            ]);

            if( ! models(Calendars::class)->insertOrUpdate($calendar, [
                'ownership_id' => implode('-', [$data->id, $data->record_language]),
                'ownership_model' => Posts::class,
            ])) {
                return false;
            } else {
                $this->addError(models(Calendars::class)->getLastErrorCode(), models(Calendars::class)->getLastErrorMessage());

                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::getPublishStart
     *
     * @param $date
     * @return false|string
     */
    public function getPublishStart($date)
    {
        return date('Y-m-d', strtotime($date)).'T'.date('H:i', strtotime($date));
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::getPublishEnd
     *
     * @param $date
     * @return false|string
     */
    public function getPublishEnd($date)
    {
        return date('Y-m-d', strtotime($date)).'T'.date('H:i', strtotime($date));
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::tags
     *
     * @return array
     */
    public function tags()
    {
        $tags = [];

        if ($result = $this->morphToManyThrough(Tags::class, Relationships::class, 'relation')) {
            if ($result->count()) {
                foreach ($result as $row) {
                    $tags[] = $row->title;
                }
            }
        }

        return $tags;
    }
    // ------------------------------------------------------------------------

    /**
     * Posts::images
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function images()
    {
        return $this->morphToManyThrough(Media::class, Relationships::class, 'relation');
    }
    
    // ------------------------------------------------------------------------

    /**
     * Posts::author
     *
     * @return bool|mixed|\O2System\Framework\Models\Sql\DataObjects\Result||\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function author()
    {
        return models(Users::class)->find($this->row->record->create->user);
    }

    // ------------------------------------------------------------------------

    /**
     * Posts::comments
     *
     * @return array|bool|\O2System\Database\DataObjects\Result|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function comments()
    {
        return $this->morphToMany(Users\Comments::class, 'reference');
    }
}
