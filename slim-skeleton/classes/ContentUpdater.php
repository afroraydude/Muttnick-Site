<?php
/**
 * Created by PhpStorm.
 * User: afror
 * Date: 7/20/2017
 * Time: 9:43
 */

class ContentUpdater
{
    function UpdateContent ($page_title, $page_url, $page_content) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }

        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $page_title = mysqli_escape_string($conn, $page_title);
        $page_url = mysqli_escape_string($conn, $page_url);
        $page_content = mysqli_escape_string($conn, $page_content);


        $sql = "UPDATE `pages` SET `name`='{$page_url}',`page-title`='{$page_title}',`content`='{$page_content}',`last-modified`= current_timestamp WHERE `name` = '{$page_url}'";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function UpdatePost ($title, $content) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $title = mysqli_escape_string($conn, $title);
        $content = mysqli_escape_string($conn, $content);

        $sql = "UPDATE `blog` SET `title`='{$title}',`content`='{$content}' WHERE `title`='{$title}'";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function WriteContent ($page_title, $page_url, $page_content) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $page_title = mysqli_escape_string($conn, $page_title);
        $page_url = mysqli_escape_string($conn, $page_url);
        $page_content = mysqli_escape_string($conn, $page_content);

        $sql = "INSERT INTO `pages` (`name`, `page-title`, `content`, `is-fullwidth`) VALUES ('{$page_url}', '{$page_title}', '{$page_content}', FALSE)";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function WritePost ($title, $content) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $title = mysqli_escape_string($conn, $title);
        $content = mysqli_escape_string($conn, $content);

        $sql = "INSERT INTO `blog` (`title`, `content`) VALUES ('{$title}', '{$content}')";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function AddFile ($filename, $filetype) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $filename = mysqli_escape_string($conn, $filename);

        $sql = "INSERT INTO `files` (`filename`, `filetype`) VALUES ('{$filename}', '{$filetype}')";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function UpdateCSS($newcss) {
        $myfile = fopen("../public/css/bootstrap-theme.css", "w") or die("Unable to open file!");
        fwrite($myfile, $newcss);
        fclose($myfile);
        return "Success";
    }

    function UpdateTemplate($template, $content) {
        $myfile = fopen("../templates/{$template}", 'w') or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
        return "Success";
    }

    function DeletePage($name) {
      try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }


      $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

      $sql = "DELETE FROM `pages` WHERE `name` = '{$name}'";

      if (!mysqli_query($conn, $sql)) {
          $result = mysqli_error($conn);
      } else {
          $result = "Success";
      }

      return $result;
    }

    function DeletePost($name) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $sql = "DELETE FROM `blog` WHERE `id` = '{$name}'";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }
}
