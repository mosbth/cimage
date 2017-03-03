<?php

namespace Mos\CImage;

/**
 * Anax class for wrapping sessions.
 *
 */
class Exception extends \Exception
{
    /**
     * Construct.
     *
     * @param string $message the Exception message to throw.
     * @param int $code the Exception code.
     * @param Exception previous the previous exception used for the exception chaining.
     */
    public function __construct($message = "", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
