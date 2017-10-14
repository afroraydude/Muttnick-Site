# Creating a new route

Creating a new route is simple, in this tutorial, we will create a new route for 
pages being held in the `/simplepage` URL (ex: `/simplepage/test`)

First, we will copy the `content.phtml` file held in the templates directory to 
`sinplepage.phtml`.

We will also make a copy a new table in MySQL called simplepages
```sql
CREATE TABLE `simplepages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `page-title` varchar(32) NOT NULL,
  `is-fullwidth` tinyint(1) NOT NULL,
  `content` text NOT NULL,
  `last-modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
```

In the `simplepages.phtml` file we created, find the code that looks like this:
```php
$sql = "SELECT * FROM `afroraydude-site`.`pages` WHERE `name` LIKE '{$page_name}'";
```

and change it to this:
```php
$sql = "SELECT * FROM `afroraydude-site`.`simplepages` WHERE `name` LIKE '{$page_name}'";
```

Now go to the routing script add add this:
```php
$app->get('/simplepage/[{name}]', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
  return $this->renderer->render($response, 'simplepage.phtml', $args);
});
```

And then you are done! You can now create pages in MySQL and have them go to the
`/simplepage` directory