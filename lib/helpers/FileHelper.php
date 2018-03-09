<?php

namespace app\lib\helpers;

use yii\helpers\BaseFileHelper;

/**
 * Class StringHelper
 * @package app\lib\helpers
 * @author Angelo <angelo@sportspass.com.au>
 */
class FileHelper extends BaseFileHelper
{
    /**
     * Valid File Types For Images
     *
     * @var array
     */
    public static $validTypes = [
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
    ];

    /**
     * Appends [0-9] onto the filename if current one exists
     * @param string $filePath full path to file
     * @return string
     */
    public static function getUniqueFilename($filePath)
    {
        $dir = dirname($filePath);
        $filename = pathinfo($filePath, PATHINFO_BASENAME);
        $filenameNoExt = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $num = 0;
        while ($num < 1000)
        {
            // break loop if we managed to find a unique filename
            if (!file_exists("$dir/$filename"))
            {
                break;
            }
            else
            {
                // increment num with
                $num++;

                // try again but prefix filename with -num
                $filename = preg_replace('/(.+?)(-\d+)?(\..*)?$/', "$1-$num$3", $filenameNoExt) . ".$ext";
            }
        }

        return "$dir/$filename";
    }

    /**
     * Retrieve the file type from the file name.
     *
     * You can optionally define the mime array, if needed.
     *
     * @since 2.0.4
     *
     * @param string $filename File name or path.
     * @param array  $mimes    Optional. Key is the file extension with value as the mime type.
     * @return array Values with extension first and mime type.
     */
    public static function getFileType($filename, $mimes = null)
    {
        if (empty($mime))
            $mimes = static::$validTypes;

        $type = false;
        $ext  = false;

        foreach ( $mimes as $ext_preg => $mime_match ) {
            $ext_preg = '!\.(' . $ext_preg . ')$!i';
            if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
                $type = $mime_match;
                $ext = $ext_matches[1];
                break;
            }
        }

        return compact( 'ext', 'type' );
    }

    /**
     * Convert bytes into human readable size
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    public static function humanFilesize($bytes, $decimals = 2)
    {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}