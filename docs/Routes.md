# Routes

Routes are the core of the this project. They integrate the user requests with the 
MySQL database and the PHP templates

## The scripts

Routes are held within 2 different files:

1. slim-skeleton/public/index.php (we will refer to this as the index script)
2. slim-skeleton/src/routes.php (we will refer to this as the routes or routing 
script)

### The Index Script

The index script is mainly used for processing any post request and for processing 
a GET request to any URL that hasn't already been pre-defined.

### The Routes Script

The routes script handles any GET request to a page that that requires its own 
template. An example would be the Dashboard page. The Dashboard page requires extra
PHP scripts, those of which cannot be added to the simple processing of the 
`content.phtml` template. 

## Creating a new route

Creating a new route is simple, in this tutorial, we will create a new route for 
pages being held in the `/simplepage` URL (ex: `/simplepage/test`)

First, we will copy the `content.phtml` file held in the templates directory to 
`sinplepage.phtml`.

We will also make a copy a new table in MySQL called simplepages
```mysql
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