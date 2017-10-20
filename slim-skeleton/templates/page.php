<?php
require '../config.php';

if ($name !== 'blog' && $name !== 'blog-content') {
  $sql = "SELECT * FROM `afroraydude-site`.`pages` WHERE `name` LIKE '{$name}'";

  $stmt = $conn->query($sql);
  $template = 'content.phtml';
  if (0 !== $stmt->rowCount()) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $template = $row['template'];
    }
  }
  include_once "themes/{$theme}/{$template}";
} else if ($name !== 'blog-content') {
  include_once "themes/{$theme}/blog.phtml";
} else {
  include_once "themes/{$theme}/blog-content.phtml";
}
?>