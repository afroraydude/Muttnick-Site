<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

spl_autoload_register(function ($classname) {
    require(__DIR__ . "/../classes/" . $classname . ".php");
});

use League\CommonMark\CommonMarkConverter;
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
/**
 * $container['view'] = function ($c) {
 * $view = new \Slim\Views\Twig('../templates/');
 * $view->addExtension(new \Slim\Views\TwigExtension(
 * $c['router'],
 * $c['request']->getUri()
 * ));
 * return $view;
 * };*/

// Define Template handler
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('../templates/');
};

// Custom 404 Page
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $args = array("name" => "404");
        return $container['view']->render($response->withStatus(404), '404.phtml', $args);
    };
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

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
        return $this->renderer->render($response, 'content.phtml', ['index' => $args['name']]);
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
        if (isset($data['isdocker']) !== false) {
            $data = file('../config.php');
            $data = array_map(function ($data) {
                return stristr($data, '$sqlserver = \'localhost\';') ? '$sqlserver = \'db\';' . "\n" : $data;
            }, $data);
            file_put_contents('../config.php', implode('', $data));
        }
        $tool = new ContentUpdater();
        $result = $tool->CreateAll($user, $pass);
        if ($result == "Success") {
            return $response->withStatus(302)->withHeader('Location', "/setup-site?step=2");
        } else {
            return $result;
        }

    }
});

# From editpage route, update page
$app->post('/dashboard/editpage', function (Request $request, Response $response) {
    include_once "../config.php";
    $data = $request->getParsedBody();
    $id = $_GET['page'];
    $page_title = filter_var($data['name']);
    $page_url = filter_var($data['url']);
    $page_data = filter_var($data['content']);
    $converter = new CommonMarkConverter();
    if ($markdownpages == true)
        $page_data = $converter->convertToHtml($page_data);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->UpdateContent($id, $page_title, $page_url, $page_data);
        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', "/dashboard/editpage?page={$id}");
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/editpage?page={$id}");
        }
    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# From edit post route, updates the data of a post
$app->post('/updatepost', function (Request $request, Response $response) {
    include_once "../config.php";
    $data = $request->getParsedBody();
    $page_id = $_GET['post'];
    $page_title = filter_var($data['name']);
    $page_data = filter_var($data['content']);
    $converter = new CommonMarkConverter();
    if ($markdownposts == true)
        $page_data = $converter->convertToHtml($page_data);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] <= 2) {
        $return = $update->UpdatePost($page_title, $page_data, $page_id);
        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', "/blog");
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard");
        }
    } else {

        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# From createpage route, place new page data into database
$app->post('/writepage', function (Request $request, Response $response) {
    include_once "../config.php";
    $data = $request->getParsedBody();
    $page_title = filter_var($data['name']);
    $page_url = filter_var($data['url']);
    $page_data = filter_var($data['content']);
    $converter = new CommonMarkConverter();
    if ($markdownpages == true)
        $page_data = $converter->convertToHtml($page_data);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->WriteContent($page_title, $page_url, $page_data);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', "/{$page_url}");
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/createpage?url={$page_url}");
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

# from createpost route, add new blog post data into database
$app->post('/writeblog', function (Request $request, Response $response) {
    include_once "../config.php";
    $data = $request->getParsedBody();
    $title = filter_var($data['name']);
    $content = filter_var($data['content']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $converter = new CommonMarkConverter();
    if ($markdownposts == true)
        $content = $converter->convertToHtml($content);
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] <= 2) {
        $return = $update->WritePost($title, $content);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/blog');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/createpost");
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

$app->post('/createuser', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $username = filter_var($data['username']);
    $password = filter_var($data['password']);
    $userrole = filter_var($data['userrole']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->CreateUser($username, $password, $userrole);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/createuser");
        }

        return $userrole;
    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

$app->post('/edituser', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $username = filter_var($data['username']);
    $password = filter_var($data['password']);
    $userrole = filter_var($data['userrole']);
    $id = $_GET['user'];
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->EditUser($username, $password, $userrole, $id);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/edituser?user={$id}");
        }

        return $userrole;
    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

$app->get('/deluser', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $id = $_GET['user'];
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->DeleteUser($id);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/dashboard');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard");
        }

        return $userrole;
    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

$app->post('/password', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $username = $_SESSION['username'];
    $password = filter_var($data['password']);
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success") {
        $return = $update->ChangePassword($username, $password);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/logout');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/password");
        }

        return $userrole;
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
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->UpdateCSS($content);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/editcss");
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
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->DeletePage($name);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/dashboard');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard");
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
    if ($authreturn == "Success" && $_SESSION['role'] <= 2) {
        $return = $update->DeletePost($name);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/dashboard');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard");
        }

    } else {
        $response->getBody()->write("Authorization failed");
        $newresponse = $response->withStatus(401);
        return $newresponse;
    }
});

$app->get('/delfile', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $name = $_GET['file'];
    $update = new ContentUpdater();
    $auth = new Authorization();
    $authreturn = $auth->CheckUser($_SESSION['username'], $_SESSION['token']);
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->DeleteFile($name);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/dashboard');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard");
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
    if ($authreturn == "Success" && $_SESSION['role'] == 1) {
        $return = $update->UpdateTemplate($name, $content);

        if ($return == "Success") {
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            $this->flash->addMessage('Error', 'Error while processing: ' . $return);
            return $response->withStatus(302)->withHeader('Location', "/dashboard/edittemplate?template={$name}");
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
    if ($authreturn == "Success" && $_SESSION['role'] <= 2) {
        $directory = $this->get('upload_directory');

        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with multiple file uploads
        foreach ($uploadedFiles as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = moveUploadedFile($directory, $uploadedFile);
                $utils = new MyUtils();
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
    return $response->withStatus(302)->withHeader('Location', '/');
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
        return $response->withStatus(302)->withHeader('Location', '/');
    } else {
        try {
            include '../config.php';
        } catch (Exception $e) {
            include '../ex-config.php';
        }

        /** sentry is a logging program, currently disabled */
        /**
         * $sentryClient = new Raven_Client($raven_key);
         * $sentryClient->install();
         * $sentryClient->captureMessage('Failed Login Attempt', array(
         * 'level' => 'warning'
         * ));
         */

        $this->flash->addMessage('Error', 'Login incorrect: ' . $return);

        return $response->withStatus(302)->withHeader('Location', '/login');
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
    unset($_SESSION['role']);

    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->get('/setup-site', function ($request, $response, $args) {
    include_once '../config.php';
    if (!$setupcomplete) {
        return $this->renderer->render($response, 'setupsite.phtml', $args);
    }
});

$app->get('/flashsend', function ($req, $res, $args) {
    // Set flash message for next request
    $this->flash->addMessage('Test', 'This is a message');

    // Redirect
    return $res->withStatus(302)->withHeader('Location', '/flashrecieve');
});

$app->get('/flashrecieve', function ($req, $res, $args) {
    // Get flash messages from previous request
    $messages = $this->flash->getMessages();
    print_r($messages);
});


# If not already defined, get page content from database through content.phtml
$app->get('/[{name}]', function ($request, $response, $args) {
    $name = $request->getAttribute('name');

    $messages = $this->flash->getMessages();
    $args = array('name' => $name, 'messages' => $messages);

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

# Move file to uploads page and upload data to database
function moveUploadedFile($directory, UploadedFile $uploadedFile)
{


    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    if (function_exists('random_bytes')) {
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    } else {
        $basename = 'jfaisjdfi';
    }
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    $filetype = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $originalName = pathinfo($uploadedFile->getClientFilename(), PATHINFO_BASENAME);

    $contentUpdater = new ContentUpdater();
    $created = $contentUpdater->AddFile($originalName, $filename, $filetype);


    return $filename;
}

// Run app
$app->run();
