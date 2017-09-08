<?php
/**
 * Created by PhpStorm.
 * User: afror
 * Date: 7/20/2017
 * Time: 9:43
 */

class ContentUpdater
{
    function encrypt($string)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        $encrypted = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), $string, MCRYPT_MODE_CBC, $iv));

        return $encrypted;
    }

    function decrypt($encrypted)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv), "\0");

        return $decrypted;
    }

    function UpdateContent($page_id, $page_title, $page_url, $page_content)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }

        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }
        $page_id = mysqli_real_escape_string($conn, $page_id);
        $page_title = mysqli_escape_string($conn, $page_title);
        $page_url = mysqli_escape_string($conn, $page_url);
        $page_content = mysqli_escape_string($conn, $page_content);


        $sql = "UPDATE `pages` SET `name`='{$page_url}',`page-title`='{$page_title}',`content`='{$page_content}',`last-modified`= current_timestamp WHERE `id` = '{$page_id}'";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function UpdatePost($title, $content, $id)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

        $id = mysqli_real_escape_string($conn, $id);
        $title = mysqli_escape_string($conn, $title);
        $content = mysqli_escape_string($conn, $content);

        $sql = "UPDATE `blog` SET `title`='{$title}',`content`='{$content}' WHERE `id`='{$id}'";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function WriteContent($page_title, $page_url, $page_content)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

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

    function WritePost($title, $content)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

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

    function AddFile($originalname, $filename, $filetype)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

        $originalname = mysqli_real_escape_string($conn, $originalname);
        $filetype = mysqli_real_escape_string($conn, $filetype);
        $filename = mysqli_escape_string($conn, $filename);

        $sql = "INSERT INTO `files` (`filename`,`fullurl`, `filetype`) VALUES ('{$originalname}','{$filename}', 'null')";

        $return = "SOMETHING SOMETHING SOMETHING ERROR";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function UpdateCSS($newcss)
    {
        $myfile = fopen("../public/css/bootstrap-theme.css", "w") or die("Unable to open file!");
        fwrite($myfile, $newcss);
        fclose($myfile);
        return "Success";
    }

    function UpdateTemplate($template, $content)
    {
        $myfile = fopen("../templates/{$template}", 'w') or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
        return "Success";
    }

    function DeletePage($name)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }

        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

        $name = mysqli_real_escape_string($conn, $name);

        $sql = "DELETE FROM `pages` WHERE `id` = {$name}";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function DeletePost($name)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }

        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $name = mysqli_real_escape_string($conn, $name);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }
        $sql = "DELETE FROM `blog` WHERE `id` = '{$name}'";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function DeleteFile($name)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

        $name = mysqli_real_escape_string($conn, $name);

        $sql = "DELETE FROM `files` WHERE `fullurl` = '{$name}'";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        unlink($name);

        return $result;
    }

    function DeleteUser($name)
    {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }


        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }

        $name = mysqli_real_escape_string($conn, $name);

        $sql = "DELETE FROM `users` WHERE `id` = {$name}";

        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        return $result;
    }

    function CreateUser($username, $password, $role)
    {
        include '../config.php';
        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        $role = mysqli_real_escape_string($conn, $role);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }
        $password = $this->encrypt($password);
        $untoken = bin2hex(random_bytes(8)) . $username . bin2hex(random_bytes(8));
        $token = $this->encrypt($untoken);
        $sql = "INSERT INTO `users`(`username`, `password`, `token`, `role`) VALUES ('{$username}','{$password}','{$token}',{$role})";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }
        return $result;
    }

    function EditUser($username, $password, $role, $id)
    {
        include '../config.php';
        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        $role = mysqli_real_escape_string($conn, $role);
        $id = mysqli_real_escape_string($conn, $id);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }
        $password = $this->encrypt($password);
        $untoken = bin2hex(random_bytes(8)) . $username . bin2hex(random_bytes(8));
        $token = $this->encrypt($untoken);
        $sql = "UPDATE `users` SET `username`='{$username}', `password`='{$password}', `token`='{$token}', `role`='{$role}' WHERE `id`={$id}";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }
        return $result;
    }

    function ChangePassword($username, $password)
    {
        include '../config.php';
        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }
        $password = $this->encrypt($password);
        $untoken = bin2hex(random_bytes(8)) . $username . bin2hex(random_bytes(8));
        $token = $this->encrypt($untoken);
        $sql = "UPDATE `users` SET `password`='{$password}',`token`='{$token}' WHERE `username` = '{$username}'";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }
        return $result;
    }

    function CreateAll($user, $pass)
    {
        include '../config.php';
        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);
        if ($conn->connect_errno) {
            $return = "Connect failed: %s\n" . $conn->connect_error;
            return $return;
        }
        $sql = "CREATE TABLE `afroraydude-site`.`users` ( `id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(32) NOT NULL UNIQUE , `role` int(3) NOT NULL , `password` VARCHAR(256) NOT NULL , `token` VARCHAR(1024) NOT NULL , `joined_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `last_login_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }
        $sql = "CREATE TABLE `afroraydude-site`.`blog` ( `id` INT NOT NULL AUTO_INCREMENT , `title` TINYTEXT NOT NULL , `content` TEXT NOT NULL , `created_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }
        $sql = "CREATE TABLE `afroraydude-site`.`files` ( `id` INT NOT NULL AUTO_INCREMENT , `filename` VARCHAR(32) NOT NULL , `fullurl` TINYTEXT NOT NULL , `filetype` VARCHAR(10) NOT NULL , `upload_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }
        $sql = "CREATE TABLE `afroraydude-site`.`pages` ( `id` INT NOT NULL AUTO_INCREMENT , `name` TINYTEXT NOT NULL UNIQUE , `page-title` VARCHAR(32) NOT NULL , `is-fullwidth` TINYINT(1) NOT NULL , `content` TEXT NOT NULL , `last-modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
        }

        # INSERTS
        $pass = $this->encrypt($pass);
        $untoken = bin2hex(random_bytes(8)) . $user . bin2hex(random_bytes(8));
        $token = $this->encrypt($untoken);
        $sql = "INSERT INTO `users`(`username`, `password`, `token`, `role`) VALUES ('{$user}','{$pass}','{$token}',1)";
        if (!mysqli_query($conn, $sql)) {
            $result = mysqli_error($conn);
        } else {
            $result = "Success";
            $_SESSION['token'] = $token;
            $_SESSION['username'] = $user;
            $_SESSION['role'] = 1;
        }
        return $result;
    }
}
