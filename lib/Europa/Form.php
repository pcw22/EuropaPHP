<?php

namespace Europa;
use Europa\Form\ElementList;

/**
 * The main form class which is also an element list.
 * 
 * @category Forms
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Form extends ElementList
{
    /**
     * Form attributes.
     * 
     * @var array
     */
    protected $attributes = array('method' => 'post');
}