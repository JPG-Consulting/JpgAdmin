<?php
/**
 * Copyright (c)2013-2014 Juan Pedro Gonzalez Gutierrez
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
 * @package   JpgAdmin
 * @author    Juan Pedro Gonzalez Gutierrez
 * @copyright 2013-2014 Juan Pedro Gonzalez Gutierrez
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://github.com/jpg-consulting
 */

namespace JpgAdmin\Mvc;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Application;

class ModuleListener implements ListenerAggregateInterface
{

    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @return void
    */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "route" event and check for an authenticated user
     *
     * If ZfcUser is not present triggers "dispatch.error" in order to
     * create a 404 response.
     *
     * If the user is not authenticated it redirects to the login page.
     *
     * @param  MvcEvent $e
     * @return void|mixed
     */
    public function onRoute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();

        if (!$routeMatch instanceof RouteMatch)
            return null;

        // Get the base route
        $baseRoute = explode('/', $routeMatch->getMatchedRouteName(), 2);
        $baseRoute = $baseRoute[0];

        if (strcmp($baseRoute, 'jpgadmin') !== 0)
            return;

        $sm  = $e->getApplication()->getServiceManager();

        // Throw an error if ZfcUser is not present
        if (!$sm->has('zfcuser_auth_service')) {
            $e->setError(Application::ERROR_ROUTER_NO_MATCH);

            $results = $e->getTarget()->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
            if (count($results)) {
                $return  = $results->last();
            } else {
                $return = $e->getParams();
            }
            return $return;
        }

        // Check if the user is authentified
        $auth = $sm->get('zfcuser_auth_service');
        if ($auth->hasIdentity())
            return;

        $request_uri = $e->getRequest()->getUriString();

        // Redirect to the user login page
        $router   = $e->getRouter();
        $url      = $router->assemble(array(), array(
            'name' => 'zfcuser/login',
            'query' => array('redirect' => urlencode($request_uri))
        ));

        $response = $e->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }

    /**
     * Set the admin layout for the admin route
     *
     * @param MvcEvent $e
     * @return void|NULL
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();

        if (!$routeMatch instanceof RouteMatch)
            return null;

        // Get the base route
        $baseRoute = explode('/', $routeMatch->getMatchedRouteName(), 2);
        $baseRoute = $baseRoute[0];

        if (strcmp($baseRoute, 'jpgadmin') !== 0)
            return;

        $controller = $e->getTarget();
        if ($controller->getEvent()->getResult()->terminate())
            return;

        $controller->layout('layout/admin');
    }
}