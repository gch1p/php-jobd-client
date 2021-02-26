<?php

namespace jobd;


abstract class Message {

    const REQUEST = 0;
    const RESPONSE = 1;

    protected $type;

    /**
     * Message constructor.
     * @param int $type
     */
    public function __construct(int $type) {
        $this->type = $type;
    }

    /**
     * @return array
     */
    abstract protected function getContent(): array;

    /**
     * @return string
     */
    public function serialize(): string {
        return json_encode([
            $this->type,
            $this->getContent()
        ]);
    }

}