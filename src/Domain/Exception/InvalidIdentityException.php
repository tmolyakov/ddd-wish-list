<?php

namespace App\Domain\Exception;

use Throwable;

class InvalidIdentityException extends \Exception
{
    /**
     * @param string $id
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $id, $message = "", $code = 0, Throwable $previous = null)
    {
        $message = sprintf('%s, ID: %s', $message, $id);

        parent::__construct($message, $code, $previous);
    }
}
