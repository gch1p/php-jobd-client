<?php

namespace jobd\exceptions;

class JobInterruptedException extends \Exception {

    public function __construct(int $code = 1, string $message = "") {
        parent::__construct($message, $code);
    }

}