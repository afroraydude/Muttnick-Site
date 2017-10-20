<?php
require '../config.php';

if ($name !== 'blog' && $name !== 'blog-content') {
  include_once "themes/{$theme}/content.phtml";
} else if ($name !== 'blog-content') {
  include_once "themes/{$theme}/blog.phtml";
} else {
  include_once "themes/{$theme}/blog-content.phtml";
}
?>