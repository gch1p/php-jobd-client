<?php

namespace jobd\messages;

class PongMessage extends Message {

    public function __construct()
    {
        parent::__construct(Message::PING);
    }

    protected function getContent(): array
    {
        return [];
    }

}