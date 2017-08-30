<?php
/**
 * Created by PhpStorm.
 * User: afror
 * Date: 7/23/2017
 * Time: 14:26
 */

class MyUtils
{
    function GetFileType(\Slim\Http\UploadedFile $uploadedFile) {
      $filetype = pathinfo($uploadedFile->getClientMediaType());
      return $filetype;
    }
    function GetOriginialName(\Slim\Http\UploadedFile $uploadedFile) {
      $originalFileaname = pathinfo($uploadedFile->getClientFilename(), PATHINFO_BASENAME);
      return $originalFileaname;
    }
}