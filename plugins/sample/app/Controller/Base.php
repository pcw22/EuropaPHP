<?php

namespace Controller;
use Europa\Controller\ControllerAbstract;
use Europa\Di\Container;

/**
 * An example of controller abstraction that sets up a default view scheme.
 * 
 * @category Controllers
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
abstract class Base extends ControllerAbstract
{
    /**
     * Sets up the a default view scheme.
     * 
     * @return void
     */
    public function init()
    {
        $view = Container::get()->phpView->get();
        $view->setScript(get_class($this));
        $this->setView($view);
    }
}