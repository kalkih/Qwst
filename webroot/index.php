<?php
/**
 * This is a Anax pagecontroller.
 *
 */

// Get environment & autoloader and the $app-object.
require __DIR__.'/config_with_app.php';

// Create services and inject into the app.

$app->session;

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/config_mysql.php');
    $db->connect();
    return $db;
});

$di->set('flashy', function() {
    $flashy = new \Anax\Flashy\Flashy();
    return $flashy;
});

$di->set('CommentsController', function() use ($di) {
    $controller = new \Anax\Comments\CommentsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('UsersController', function () use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function () use ($di) {
    $controller = new \Anax\Tag\TagsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('QuestionsController', function() use ($di) {
    $controller = new \Anax\Question\QuestionsController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('auth', function() use ($di) {
    $module = new \Anax\Authentication\Authentication('user');
    $module->setDI($di);
    return $module;
});

$di->set('form', '\Mos\HTMLForm\CForm');
$di->set('time', '\Anax\Time\Time');

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

// Get theme
$app->theme->configure(ANAX_APP_PATH . 'config/theme-grid.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');

// Extra stylesheet
$app->theme->addStylesheet('css/flashy.css');

$app->views ->addString($app->flashy->get('icons'), 'flash');

$app->dispatcher->forward([
    'controller' => 'questions',
    'action'     => 'footer',
    'params' => ["footer-col-2"]
]);

$app->dispatcher->forward([
    'controller' => 'users',
    'action'     => 'footer',
    'params' => ["footer-col-1"]
]);

$app->dispatcher->forward([
    'controller' => 'questions',
    'action'     => 'footer2',
    'params' => ["footer-col-3"]
]);

// Routes
$app->router->add('', function() use ($app) {

    $app->theme->setTitle("Home");

    $content = $app->fileContent->get('welcome.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'popular',
        'params' => ["sidebar", 4]
    ]);

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'footer',
        'params' => ["triptych-2"]
    ]);

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'top',
        'params' => ["triptych-1"]
    ]);

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'footer2',
        'params' => ["triptych-3"]
    ]);

    $app->views->addString($content, 'main');
});

$app->router->add('about', function() use ($app) {

    $app->theme->setTitle("About");

    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'markdown, shortcode');

    $app->views->addString($content, 'main');

        $app->dispatcher->forward([
            'controller' => 'questions',
            'action'     => 'sidebar',
        ]);

});

$app->router->add('setup', function() use ($app) {

    $app->theme->setTitle("Setup");

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'setup',
    ]);

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'setup',
    ]);

    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'setup',
    ]);


});

$app->router->add('source', function() use ($app) {

    $app->theme->addStylesheet('css/source.css');
    $app->theme->setTitle("Källkod");

    if ($app->auth->isAdmin()) {
        $source = new \Mos\Source\CSource([
            'secure_dir' => '..',
            'base_dir' => '..',
            'add_ignore' => ['.htaccess'],
        ]);

        $app->views->add('default/source', [
            'content' => $source->View(),
        ]);
    } else {
        $app->flashy->add('error', 'You do not have permission to this page!');
    }

});





/*
if ($app->views->hasContent('main')) {
    if ($app->views->hasContent('sidebar') == false && $app->views->hasContent('main-center') == false) {
        $app->views->addString($lorem . $lorem, 'sidebar');
    }
}
*/


$app->router->handle();
$app->theme->render();
