<?php
namespace handler\Listener;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Mvc\Application;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;

class Listener extends injectable
{
    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dis)
    {
        $acl = new Memory();
        $acl->addRole('guest');
        $acl->addRole('user');
        $acl->addRole('manager');
        $acl->addRole('admin');

        $acl->addComponent(
            'index',
            ['index']
        );
        $acl->addComponent(
            'a',
            ['index']
        );

        $acl->addComponent(
            'b',
            ['index']
        );

        $acl->addComponent(
            'c',
            ['index']
        );
        $acl->addComponent(
            'd',
            ['index']
        );

        $acl->allow('*', 'index', '*');
        $acl->allow('guest', 'a', '*');
        $acl->allow('user', 'a', '*');
        $acl->allow('user', 'b', '*');
        $acl->allow('manager', 'a', '*');
        $acl->allow('manager', 'b', '*');
        $acl->allow('manager', 'c', '*');
        $acl->allow('admin', '*', '*');

        $role = "guest";
        $controller = "index";
        $action = "index";
        if (!empty($dis->getControllerName())) {
            $controller = $dis->getControllerName();
        }
        if (!empty($dis->getActionName())) {
            $action = $dis->getActionName();
        }
        if (!empty($this->request->get('role'))) {
            $role = $this->request->get('role');
        }
        if (false === $acl->isAllowed($role, $controller, $action)) {
            echo '<h3>Access denied !</h3>';
            die;
        }
    }
}
