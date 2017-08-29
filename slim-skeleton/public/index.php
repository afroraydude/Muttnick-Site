<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

spl_autoload_register(function ($classname) {
    require (__DIR__ . "/../classes/" . $classname . ".php");
});

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . "/uploads";

// app stuff
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $content = new BasicContent();
    $response_content = $content->PostHelloContent($name);
    $response->getBody()->write($response_content);

    return $response;
});

/**
 * Begin Dashboard POST requests
 */

// https://docs.sentry.io/clients/php/usage/

# default page
$app->get('', function ($request, $response, $args) {
  include_once '../config.php';
  if ($setupcomplete) {
    return $this->renderer->render($response, 'content.phtml', ['e540cdd1328b2b' => $args['name']]);
  } else {
    return $this->renderer->render($response, 'setupsite.phtml', $args);
  }
});

$app->post('/createall', function (Request $request, Response $response) {
  include_once '../config.php';
  if (!$setupcomplete) {
    $data = $request->getParsedBody();
    $user = filter_var($data['user']);
    $pass = filter_var($data['pass']);
    $tool = new ContentUpdater();
    $result = $tool->CreateAll($user, $pass);
    if($result == "Success") {
      echo "<html><head><meta http-equiv='refresh' content='0; url=/setup-site?step=2'></head><body></body></html>";
    } else {
      return $result;
    }
  }
});

# From editpage route, update page
$app->post('/updatepage', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $page_title = filter_var($data['name']);
    $page_url = filter_var($data['url']);
    $page_data = filter_var($data['content']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->UpdateContent($page_title, $page_url, $page_data);
        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/editpage?page={$page_url}'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }
    } else {

        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# From edit post route, updates the data of a post
$app->post('/updatepost', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $page_title = filter_var($data['name']);
    $page_data = filter_var($data['content']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->UpdatePost($page_title, $page_data);
        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/blog'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }
    } else {

        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# From createpage route, place new page data into database
$app->post('/writepage', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $page_title = filter_var($data['name']);
    $page_url = filter_var($data['url']);
    $page_data = filter_var($data['content']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->WriteContent($page_title, $page_url, $page_data);

        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/dashboard/editpage?page={$page_url}'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# from createpost route, add new blog post data into database
$app->post('/writeblog', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $title = filter_var($data['name']);
    $content = filter_var($data['content']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->WritePost($title, $content);

        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/blog'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# from editcss route, update the css file
$app->post('/updatecss', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $content = filter_var($data['content']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->UpdateCSS($content);

        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# delete a page from the database
$app->get('/delpage', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $name = $_GET['page'];
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->DeletePage($name);

        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/dashboard'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

$app->get('/delpost', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $name = $_GET['post'];
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->DeletePost($name);

        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/dashboard'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# Updates a template on the filesystem, given the template name and content
$app->post('/updatetemplate', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $name = $_GET['template'];
    $content = filter_var($data['content']);
    $auth = new Authorization();
    $update = new ContentUpdater();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->UpdateTemplate($name, $content);

        if ($return == "Success") {
            $response->getBody()->write("<html><head><meta http-equiv='refresh' content='0; url=/'></head><body></body></html>");
        } else {
            $response->getBody()->write($return);
        }


    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# upload file to filesystem
$app->post('/upload', function (Request $request, Response $response) {
    $data = $request->getUploadedFiles();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $directory = $this->get('upload_directory');

        $uploadedFiles = $request->getUploadedFiles();
        
        // handle single input with multiple file uploads
        foreach ($uploadedFiles as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = moveUploadedFile($directory, $uploadedFile);
                $utils = new MyUtils();
                $mimetype = $utils->GetFileType($uploadedFile);
                $contentUpdater = new ContentUpdater();
                $created = $contentUpdater->AddFile($filename, $mimetype);
                $response->write('Uploaded ' . $filename . '<br/>' . $created);
            }
        }
    } else {

        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# Redirect index to homepage
$app->get('/index', function (Request $request, Response $response) {
   $response->getBody()->write('<html><head><meta http-equiv="refresh" content="0; url=/"></head><body></body></html>');
});

# Login script
$app->post('/auth', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $u = filter_var($data['username']);
    $p = filter_var($data['password']);
    $u = htmlspecialchars($u);
    $p = htmlspecialchars($p);
    $auth = new Authorization();
    $return = $auth->login($u, $p);
    if ($return == "Success") {
        $_SESSION['username'] = $u;
        # Not setting token, see Authorization class
        $response->getBody()->write('<html><head><meta http-equiv="refresh" content="0; url=/"></head><body></body></html>');
    } else {
        try { include '../config.php'; } catch (Exception $e) { include '../ex-config.php'; }

        /** sentry is a logging program, currently disabled */
        /**
        $sentryClient = new Raven_Client($raven_key);
        $sentryClient->install();
        $sentryClient->captureMessage('Failed Login Attempt', array(
            'level' => 'warning'
        ));
        */
        $response->getBody()->write($response->getBody()->write('<html><head><meta http-equiv="refresh" content="5; url=/login"></head><body><h1>Login Incorrect</h1><a href="/login">Click here if you are not redirected in 5 seconds</a></body></html>'));
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# get token
$app->get('/mahtoken', function (Request $request, Response $response) {
    $response->getBody()->write($_SESSION['token']);
});

# see if the username and token match each other
$app->get('/amiwhoisayiam', function (Request $request, Response $response) {
    $auth = new Authorization();
    $return = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    $response->getBody()->write($return);
});

# logout the user, remove all user data from client
$app->get('/logout', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");
    unset($_SESSION['username']);
    unset($_SESSION['token']);

    $response->getBody()->write('<html><head><meta http-equiv="refresh" content="0; url=/"></head><body></body></html>');
});

$app->get('/setup-site', function ($request, $response, $args) {
  include_once '../config.php';
  if (!$setupcomplete) {
    return $this->renderer->render($response, 'setupsite.phtml', $args);
  }
});

# If not already defined, get page content from database through content.phtml
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");
    if (!file_exists('../config.php')) {
        copy('../ex-config.php', '../config.php');
    }
    // Render index view
  include '../config.php';
  if ($setupcomplete) {
    return $this->renderer->render($response, 'content.phtml', $args);
  } else {
    return $this->renderer->render($response, 'setupsite.phtml', $args);
  }
});

# Move file to uploads page
function moveUploadedFile($directory, UploadedFile $uploadedFile)
{

    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    if(function_exists('random_bytes')) {
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    } else {
        $basename = 'jfaisjdfi';
    }
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}

// Run app
$app->run();
