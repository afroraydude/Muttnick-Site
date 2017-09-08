<?php
/**
 * Created by PhpStorm.
 * User: afror
 * Date: 7/13/2017
 * Time: 20:05
 */

class Authorization
{
    function decrypt($encrypted)
    {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $key, true) , substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)) , MCRYPT_MODE_CBC, $iv) , "\0");

        return $decrypted;
    }

    function encrypt($string)
    {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC) , MCRYPT_DEV_URANDOM);
        $encrypted = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $key, true) , $string, MCRYPT_MODE_CBC, $iv));

        return $encrypted;
    }

    function login($username, $password)
    {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }

        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $return = 'Something something something error';

        $username = mysqli_escape_string($conn, $username);
        $password = mysqli_escape_string($conn, $password);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM `users` WHERE `username` = '$username'";

        $result = $conn->query($sql);

        if ($result->num_rows !== 0) {
            while ($row = $result->fetch_assoc()) {
                $encrypted = $row['password'];
                $token = $row['token'];
                $role = $row['role'];
            }
            $unencrypted = $this->decrypt($encrypted, $key);
            if ($unencrypted == $password) {
                $return = "Success";
                $sql = "UPDATE `users` SET `last_login_timestamp`=CURRENT_TIMESTAMP WHERE `username` = '$username'";
                $_SESSION['token'] = $token;
                $_SESSION['role'] = $role;
                $result = $conn->query($sql);
                return $return;
            } else {
                return "Password is incorrect";
            }
        } else {
            return "User does not exist";
        }
    }

    function CheckUser ($username, $token) {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }

        $conn = new mysqli($sqlserver, $sqluser, $sqlpass, $sqldb);

        $return = 'Something something something error';

        $username = mysqli_escape_string($conn, $username);
        $token = mysqli_escape_string($conn, $token);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM `users` WHERE `username` = '$username'";

        $result = $conn->query($sql);

        if ($result->num_rows !== 0) {
            while ($row = $result->fetch_assoc()) {
                $servertoken = $row['token'];
            }
            if ($servertoken == $token) {
                $return = "Success";

                return $return;
            }
        } else {
            return "User does not exist or token is incorrect";
        }
    }
}