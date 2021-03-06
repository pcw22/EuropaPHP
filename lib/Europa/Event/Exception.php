<?php

namespace Europa\Event;
use Europa\Exception as BaseException;

/**
 * The main event exception class.
 * 
 * @category Exceptions
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Exception extends BaseException
{
    /**
     * Thrown when an invalid callback is bound to an event.
     * 
     * @var int
     */
    const INVALID_CALLBACK = 1;

    /**
     * Thrown when an invalid event is bound to a stack.
     * 
     * @var int
     */
    const INVALID_EVENT = 2;
}