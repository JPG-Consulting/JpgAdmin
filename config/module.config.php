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
            'dashboard' => array(
                'icon'  => 'fa fa-home',
                'label' => 'Dashboard',
                'route' => 'jpgadmin'
            ),
    	    'users' => array(
                'label' => 'Users',
                'icon'  => 'fa fa-users',
                'route' => 'jpgadmin/users',
                'pages' => array(
                    array(
                        'label' => 'Create',
                        'route' => 'jpgadmin/users/create'
                    )
                )
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
                        'child_routes' => array(
                            'create' => array(
                                'type'    => 'Literal',
                                'options' => array(
                                    'route'    => '/create',
                                    'defaults' => array(
                                        'controller'    => 'JpgAdmin\Controller\UsersController',
                                        'action'        => 'create',
                                    ),
                                ),
                                'may_terminate' => true,
                             ),
                        )
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
