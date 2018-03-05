<?php
namespace app\lib\db;

use app\lib\helpers\ArrayHelper;
use yii\db\ActiveRecordInterface;
use yii\helpers\Json;

/**
 * Class ActiveRecord
 * @package api\lib\db
 *
 * @author Angelo <angelo@sportspass.com.au>
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @param mixed $condition
     * @return static
     */
    public static function findOneCached($condition)
    {
        $cacheId = Json::encode([__METHOD__, $condition]);
        if (($cache = app()->cache->get($cacheId)))
        {
            return $cache;
        }

        $model = static::findOne($condition);
        app()->cache->set($cacheId, $model,300);

        return $model;
    }

    /**
     * @param int|array $ids
     * @return static|static[]
     */
    public static function getById($ids)
    {
        if (is_array($ids))
        {
            return static::findAll($ids);
        }
        else
        {
            return static::findOne($ids);
        }
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }

    /**
     * @inheritdoc
     *
     * @param string $findMethod the find method to use
     * @param null|array $findMethodParams params to pass into find method
     * @return ActiveQuery
     */
    public function hasOne($class, $link, $findMethod = 'find', $findMethodParams = [])
    {
        /* @var $class ActiveRecordInterface */
        /* @var $query ActiveQuery */
        $query = call_user_func_array("$class::$findMethod", $findMethodParams);
        $query->primaryModel = $this;
        $query->link = $link;
        $query->multiple = false;

        return $query;
    }

    /**
     * @inheritdoc
     *
     * @param string $findMethod the find method to use
     * @param null|array $findMethodParams params to pass into find method
     * @return ActiveQuery
     */
    public function hasMany($class, $link, $findMethod = 'find', $findMethodParams = [])
    {
        /* @var $class ActiveRecordInterface */
        /* @var $query ActiveQuery */
        $query = call_user_func_array("$class::$findMethod", $findMethodParams);
        $query->primaryModel = $this;
        $query->link = $link;
        $query->multiple = true;

        return $query;
    }

    /**
     * Ascend from child id all the way to the parent id
     * Used for Subjects/Themes etc. anything with a parent
     * @param array $childIds
     * @param null|string $orderBy
     * @param string $parentColumn
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTopmostIdsFromChildren($childIds, $orderBy = null, $parentColumn = 'Parent')
    {
        if (!is_array($childIds))
        {
            $childIds = [$childIds];
        }

        // get table name
        $tableName = static::tableName();

        // returns primary key as array (might be more thna one so we grab first one
        $primaryKey = reset(static::getTableSchema()->primaryKey);

        $ids = [];
        foreach ($childIds as $pkId)
        {
            $parentId = $pkId;
            $orderLabel = null;
            while ($parentId)
            {
                $select = [$primaryKey, $parentColumn];
                if ($orderBy)
                {
                    $select[] = $orderBy;
                }

                $item = (new Query())
                    ->select($select)
                    ->from($tableName)
                    ->where([$primaryKey => $parentId])
                    ->one();

                if (!$item)
                {
                    break;
                }

                $pkId = $item[$primaryKey];
                $parentId = $item[$parentColumn];
                $orderLabel = ($orderBy) ? $item[$orderBy] : null;
            }

            $ids[$pkId] = $orderLabel;
        }

        if ($orderBy)
        {
            asort($ids);
        }

        $ids = array_keys($ids);

        return $ids;
    }

    /**
     * Get parents of specific id
     * @param int $pkId id of row to find parents of
     * @param string $parentColumn
     * @param string $findMethod
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAscendingParents($pkId, $parentColumn = 'Parent', $findMethod = 'find')
    {
        // get table name
        $tableName = static::tableName();

        // returns primary key as array (might be more thna one so we grab first one
        $primaryKey = reset(static::getTableSchema()->primaryKey);

        $items = [];
        $parentId = $pkId;
        while ($parentId)
        {
            /** @var ActiveQuery $q */
            $q = call_user_func(get_called_class() . "::$findMethod");
            $item = $q->from($tableName)
                ->where([$primaryKey => $parentId])
                ->one();

            if (!$item)
            {
                break;
            }

            // add new item to items
            $items[] = $item;
            $parentId = $item[$parentColumn];
        }

        // reverse order so parent > child > child
        $items = array_reverse($items);

        return $items;
    }

    /**
     * @param $parentId
     * @param null $orderBy
     * @param string $parentColumn
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getChildIds($parentId, $orderBy = null, $parentColumn = 'Parent')
    {
        return static::find()
            ->select(static::getTableSchema()->primaryKey)
            ->andWhere([$parentColumn => $parentId])
            ->orderBy($orderBy)
            ->column();
    }
}