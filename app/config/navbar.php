<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'expand'  => [
            'text'  => '<i class="fa fa-bars"></i>',
            'url'   => 'unclickable',
            'title' => 'Expand menu'
        ],

        // This is a menu item
        'Home'  => [
            'text'  => 'Home',
            'url'   => '',
            'title' => 'Home'
        ],
 
        // This is a menu item
        'Questions' => [
            'text'  =>'Questions?',
            'url'   =>'questions',
            'title' => 'Questions'
        ],

        // This is a menu item
        'Tags' => [
            'text'  =>'Tags', 
            'url'   =>'tags',
            'title' => 'Tags'
        ],

        // This is a menu item
        'Users' => [
            'text'  =>'Users', 
            'url'   =>'users',
            'title' => 'Users'
        ],
    ],
 
    // Callback tracing the current selected menu item base on scriptname
    'callback' => function($url) {
        if ($url == $this->di->get('request')->getRoute()) {
            return true;
        }
    },

    // Callback to create the urls
    'create_url' => function($url) {
        return $this->di->get('url')->create($url);
    },
];
