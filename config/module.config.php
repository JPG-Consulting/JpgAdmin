<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'JpgAdmin\Controller\DashboardController' => 'JpgAdmin\Controller\DashboardController',
            'JpgAdmin\Controller\UsersController' => 'JpgAdmin\Controller\UsersController',
        ),
    ),
   
    'navigation' => array(
        'admin'     => array(
    	    'users' => array(
        	    'label' => 'Users',
    	        'route' => 'jpgadmin/users'
            )
        ),
        'admin_top' => array(),
    ),

    'router' => array(
        'routes' => array(
            'jpgadmin' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'JpgAdmin\Controller\DashboardController',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'users' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/users',
                            'defaults' => array(
                                'controller'    => 'JpgAdmin\Controller\UsersController',
                                'action'        => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                    )
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'layout/admin' => __DIR__ . '/../view/layout/admin.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
    ),
);