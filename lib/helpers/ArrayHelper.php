<?php
namespace app\lib\helpers;

use yii\helpers\BaseArrayHelper;

/**
 * Class ArrayHelper
 * @package app\lib\helpers
 * @author Angelo <angelo@sportspass.com.au>
 */
class ArrayHelper extends BaseArrayHelper
{
    /**
    * Combine two arrays using the keys from $array1 and matching key=>values from $array2
    * @param array $keys
    * @param array $combineWith
    * @return array
    */
    public static function combineMatchingKeys(array $keys, array $combineWith)
    {
        $combined = [];
        foreach ($keys as $key)
        {
            if (array_key_exists($key, $combineWith))
            {
                $combined[$key] = $combineWith[$key];
            }
        }

        return $combined;
    }

    /**
     * Get all permutations of an array
     * @param $items
     * @param array $perms
     * @return array
     */
    public static function getPermutations($items, $perms = [])
    {
        if (empty($items))
        {
            $return = [$perms];
        }
        else
        {
            $return = [];
            for ($i = count($items) - 1; $i >= 0; --$i)
            {
                $newItems = $items;
                $newPerms = $perms;
                list($foo) = array_splice($newItems, $i, 1);
                array_unshift($newPerms, $foo);
                $return = array_merge($return, self::getPermutations($newItems, $newPerms));
            }
        }

        return $return;
    }
}