<?php

namespace OpenCFP\Domain\Entity;

use Spot\Entity;

class Talk extends Entity
{
    protected static $table = 'talks';
    protected static $mapper = 'OpenCFP\Domain\Entity\Mapper\Talk';

    public static function fields()
    {
        return [
            'id' => ['type' => 'integer', 'autoincrement' => true, 'primary' => true],
            'title' => ['type' => 'string', 'length' => 100, 'required' => true],
            'description' => ['type' => 'text'],
            'type' => ['type' => 'string', 'length' => 50],
            'user_id' => ['type' => 'integer', 'required' => true],
            'level' => ['type' => 'string', 'length' => 50],
            'category' => ['type' => 'string', 'length' => 50],
            'desired' => ['type' => 'smallint', 'value' => 0],
            'slides' => ['type' => 'string', 'length' => 255],
            'other' => ['type' => 'text'],
            'allow_video' => ['type' => 'smallint', 'value' => 0],
            'favorite' => ['type' => 'smallint', 'value' => 0],
            'selected' => ['type' => 'smallint', 'value' => 0],
            'vote_average' => [ 'type' => 'float' ],
            'vote_count' => [ 'type' => 'integer' ],
            'created_at' => ['type' => 'datetime', 'value' => new \DateTime()],
            'updated_at' => ['type' => 'datetime', 'value' => new \DateTime()]
        ];
    }

    public static function relations(\Spot\MapperInterface $mapper, \Spot\EntityInterface $entity)
    {
        return [
            'speaker' => $mapper->belongsTo($entity, 'OpenCFP\Domain\Entity\User', 'user_id'),
            'favorites' => $mapper->hasMany($entity, 'OpenCFP\Domain\Entity\Favorite', 'talk_id'),
        ];
    }
}
