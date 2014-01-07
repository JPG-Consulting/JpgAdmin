<?php
/**
 * Copyright (c) 2014 Juan Pedro Gonzalez
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     JpgAdmin
 * @author      Juan Pedro Gonzalez
 * @copyright   2014 Juan Pedro Gonzalez
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://github.com/jpg-consulting
 */
namespace JpgAdmin;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use ZfcUser\Mapper\UserHydrator;

class Module implements 
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'admin_navigation'     => 'JpgAdmin\Navigation\Service\AdminNavigationFactory',
                'admin_top_navigation' => 'JpgAdmin\Navigation\Service\AdminTopNavigationFactory',
                'zfcuser_user_mapper'      => function ($sm) {
                    if ($sm->has('zfcuser_doctrine_em')) {
                        // Doctrine
                        return new \JpgAdmin\ZfcUser\Mapper\Doctrine\User(
                            $sm->get('zfcuser_doctrine_em'),
                            $sm->get('zfcuser_module_options')	
                        );
                    } else {
                        // ZendDB
                    	$zfcUserOptions = $sm->get('zfcuser_module_options');

                        /** @var $mapper \ZfcUserAdmin\Mapper\UserZendDb */
                        $mapper = new \JpgAdmin\ZfcUser\Mapper\Zend\User();
                        $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                        $entityClass = $zfcUserOptions->getUserEntityClass();
                        $mapper->setEntityPrototype(new $entityClass);
                        $mapper->setHydrator(new UserHydrator());

                        return $mapper;
                    }
                }
            ),
            'invokables' => array(
            	'AdminListener'    => 'JpgAdmin\Mvc\AdminListener'
            )
        );
    }
    
    public function onBootstrap(EventInterface $e)
    {
        $app  = $e->getParam('application');
        $em   = $app->getEventManager();
        $sm   = $app->getServiceManager();
        
        $em->attachAggregate($sm->get('AdminListener'));
    }
    
}