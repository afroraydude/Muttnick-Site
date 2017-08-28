<?php
// Routes

# default page
$app->get('', function ($request, $response, $args) {
    return $this->renderer->render($response, 'content.phtml', ['e540cdd1328b2b' => $args['name']]);
});

# check to see if slim is installed correctly
$app->get('/slim-installed', function ($request, $response, $args) {
    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

# login page
$app->get('/login', function ($request, $response, $args) {
    return $this->renderer->render($response, 'login.phtml', $args);
});

# dashboard
$app->get('/dashboard', function ($request, $response, $args) {
    return $this->renderer->render($response, 'dash.phtml', $args);
});

# page creator
$app->get('/dashboard/createpage', function ($request, $response, $args) {
    return $this->renderer->render($response, 'createpage.phtml', $args);
});

# Edit the CSS of the site
$app->get('/dashboard/editcss', function ($request, $response, $args) {
    return $this->renderer->render($response, 'editcss.phtml', $args);
});

# Create a blog post
$app->get('/dashboard/createpost', function ($request, $response, $args) {
    return $this->renderer->render($response, 'createpost.phtml', $args);
});

$app->get('/dashboard/edittemplate', function ($request, $response, $args) {
    return $this->renderer->render($response, 'edittemplate.phtml', $args);
});

# Edit a page
$app->get('/dashboard/editpage', function ($request, $response, $args) {
    return $this->renderer->render($response, 'editpage.phtml', $args);
});

# Upload a file
$app->get('/upload', function ($request, $response, $args) {
    return $this->renderer->render($response, 'upload.phtml', $args);
});

# See Blog
$app->get('/blog', function ($request, $response, $args) {
    return $this->renderer->render($response, 'blog.phtml', $args);
});

# See specific blog post
$app->get('/blog/[{postid}]', function ($request, $response, $args) {
  return $this->renderer->render($response, 'blog-content.phtml', $args);
});

# Edit blog post
$app->get('/dashboard/editpost', function ($request, $response, $args) {
    return $this->renderer->render($response, 'editpost.phtml', $args);
});

$app->get('/setup-site', function ($request, $response, $args) {
    return $this->renderer->render($response, 'setupsite.phtml', $args);
});
