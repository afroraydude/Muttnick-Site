<?php
// Routes

# check to see if slim is installed correctly
$app->get('/slim-installed', function ($request, $response, $args) {
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

# login page
$app->get('/login', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'login.phtml', $args);
});

# dashboard
$app->get('/dashboard', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'dash.phtml', $args);
});

# page creator
$app->get('/dashboard/createpage', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'createpage.phtml', $args);
});

# Edit the CSS of the site
$app->get('/dashboard/editcss', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'editcss.phtml', $args);
});

# Create a blog post
$app->get('/dashboard/createpost', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'createpost.phtml', $args);
});

$app->get('/dashboard/edittemplate', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'edittemplate.phtml', $args);
});

# Edit a page
$app->get('/dashboard/editpage', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'editpage.phtml', $args);
});

# Upload a file
$app->get('/upload', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'upload.phtml', $args);
});

# See Blog
$app->get('/blog', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages, 'name'=>'blog');
    return $this->renderer->render($response, "page.php", $args);
});

# See specific blog post
$app->get('/blog/[{postid}]', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $postid = $args['postid'];
    $args = array('messages'=>$messages, 'name'=>'blog-content', 'postid'=>$postid);
  include_once '../config.php';
  return $this->renderer->render($response, "page.php", $args);
});

# Edit blog post
$app->get('/dashboard/editpost', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'editpost.phtml', $args);
});

# Create User
$app->get('/dashboard/createuser', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'createuser.phtml', $args);
});

# Edit user
$app->get('/dashboard/edituser', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'edituser.phtml', $args);
});

# Change password
$app->get('/password', function ($request, $response, $args) {
    $messages = $this->flash->getMessages();
    $args = array('messages'=>$messages);
    return $this->renderer->render($response, 'changepassword.phtml', $args);
});


