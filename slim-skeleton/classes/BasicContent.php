<?php
/**
 *
 */
class BasicContent
{
  function PostHelloContent ($content) {
      $header = '<html><head><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script></head><body><div class="container">';
      $content = "<h1>Hello, <action>".$content."</action></h1>";
      $footer = "</div></body></html>";
      $return = $header.$content.$footer;
    return $return;
  }
  function PostTestContent () {
      $header = '<html><head><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script></head><body><div class="container">';
      $content = $this->GetContent('test-page');
      $footer = "</div></body></html>";
      return $header.$content.$footer;
  }

  function GetContent($page_name) {
      try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }

      $return = 'Something something something error';

      $conn = new mysqli($sqlserver,$sqluser,$sqlpass,$sqldb);

      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT * FROM `afroraydude-site`.`pages` WHERE `name` LIKE '%{$page_name}%'";

      $result = $conn->query($sql) or die($conn->error);

      if ($result->num_rows !== 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
              $lm_notif = "Page was last modified on ".$row['last-modified'];
              $return = $row['content'].$lm_notif;
          }
      } else {
          $return = "0 results";
      }
      $conn->close();
      return $return;
  }
}
