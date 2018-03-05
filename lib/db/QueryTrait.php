<?php
namespace app\lib\db;

/**
 * Class Query
 * @package api\lib\db
 *
 * @author Angelo Gabisan <angelo@sportspass.com.au>
 */
trait QueryTrait
{
    /**
     * @var array @see self::cache()
     */
    protected $_cacheResult;

    /**
     * @param bool $skipCache
     * @param int $cacheDuration
     * @param null|\yii\caching\Dependency $dependency
     * @return $this
     */
    public function cache($skipCache = false, $cacheDuration = 0, $dependency = null)
    {
        $this->_cacheResult = [
            'enable' => !$skipCache,
            'duration' => $cacheDuration,
            'dependency' => $dependency
        ];

        return $this;
    }

    /**
     * Executes query and returns a single row of result.
     * @param \yii\db\Connection $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return ActiveRecord|array|null a single row of query result. Depending on the setting of [[asArray]],
     * the query result may be either an array or an ActiveRecord object. Null will be returned
     * if the query results in nothing.
     */
    public function one($db = null)
    {
        // if cached
        if (!empty($this->_cacheResult['enable']))
        {
            // cached response
            return $this->db()->cache(function () use ($db)
            {
                return parent::one($db);

            }, $this->_cacheResult['duration'], $this->_cacheResult['dependency']);
        }

        // non cached response
        return parent::one($db);
    }

    /**
     * @param \yii\db\Connection $db
     * @return null|\yii\db\Connection
     */
    public function db($db = null)
    {
        $useDb = $db;
        if (!$useDb)
        {
            if ($this instanceof ActiveQuery)
            {
                /** @var ActiveRecord $model */
                $model = $this->modelClass;
                $useDb = $model::getDb();
            }

            if (!$useDb)
            {
                $useDb = app()->db;
            }
        }

        return $useDb;
    }

    /**
     * Executes query and returns all results as an array.
     * @param \yii\db\Connection $db the DB connection used to create the DB command.
     * If null, the DB connection returned by [[modelClass]] will be used.
     * @return array|ActiveRecord[] the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null)
    {
        // if cached
        if (!empty($this->_cacheResult['enable']))
        {
            // cached response
            return $this->db()->cache(function () use ($db)
            {
                return parent::all($db);

            }, $this->_cacheResult['duration'], $this->_cacheResult['dependency']);
        }

        // non cached response
        return parent::all($db);
    }

    /**
     * Returns the number of records.
     * @param string $q the COUNT expression. Defaults to '*'.
     * Make sure you properly quote column names in the expression.
     * @param \yii\db\Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given (or null), the `db` application component will be used.
     * @return integer|string number of records. The result may be a string depending on the
     * underlying database engine and to support integer values higher than a 32bit PHP integer can handle.
     */
    public function count($q = '*', $db = null)
    {
        // if cached
        if (!empty($this->_cacheResult['enable']))
        {
            // cached response
            return $this->db()->cache(function () use ($q, $db)
            {
                return parent::count($q, $db);

            }, $this->_cacheResult['duration'], $this->_cacheResult['dependency']);
        }

        // non cached response
        return parent::count($q, $db);
    }
    /**
     * Executes the query and returns the first column of the result.
     * @param \yii\db\Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return array the first column of the query result. An empty array is returned if the query results in nothing.
     */
    public function column($db = null)
    {
        // if cached
        if (!empty($this->_cacheResult['enable']))
        {
            // cached response
            return $this->db()->cache(function () use ($db)
            {
                return parent::column($db);

            }, $this->_cacheResult['duration'], $this->_cacheResult['dependency']);
        }

        // non cached response
        return parent::column($db);
    }
}