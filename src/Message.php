<?php

namespace jobd;


abstract class Message {

    const REQUEST = 0;
    const RESPONSE = 1;
    const PING = 2;
    const PONG = 3;

    protected $type;

    /**
     * Message constructor.
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    abstract protected function getContent(): array;

    /**
     * @return string
     */
    public function serialize(): string
    {
        $data = [$this->type];
        $content = $this->getContent();

        if (!empty($content))
            $data[] = $content;

        return json_encode($data);
    }

}