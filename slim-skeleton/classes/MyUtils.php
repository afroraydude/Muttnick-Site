<?php
/**
 * Created by PhpStorm.
 * User: afror
 * Date: 7/23/2017
 * Time: 14:26
 */

class MyUtils
{
    function GetFileType($file) {
        $mimetype = false;
        /**
        if(function_exists('finfo_fopen')) {
            // open with FileInfo
        } elseif(function_exists('getimagesize')) {
            // open with GD
        } elseif(function_exists('exif_imagetype')) {
           // open with EXIF
        } elseif(function_exists('mime_content_type')) {
           $mimetype = mime_content_type($file);
        }
        */
        $mimetype = mime_content_type($file);
        return $mimetype;
    }
}