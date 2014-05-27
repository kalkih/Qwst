<?php 
/**
 * This is a Anax pagecontroller.
 *
 */

// Get environment & autoloader and the $app-object.
require __DIR__.'/config_with_app.php'; 

// Create services and inject into the app.

$di->set('flashy', function() {
    $flashy = new \Anax\Flashy\Flashy();
    return $flashy;
});

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

// Get theme
$app->theme->configure(ANAX_APP_PATH . 'config/theme-grid.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar_me.php');

// Extra stylesheet
$app->theme->addStylesheet('css/flashy.css');

// Routes
$app->router->add('', function() use ($app) {

    $app->theme->setTitle("Flash test");

    $app->session;

    $app->flashy->add('warning', 'THIS IS A WARNING');
    $app->flashy->add('info', 'THIS IS A INFO');
    $app->flashy->add('lol', 'THIS IS A LOL');

    $app->views->addString($app->flashy->get('icons'), 'main-center');
 
});

$app->router->handle();
$app->theme->render();