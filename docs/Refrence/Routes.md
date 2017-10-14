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