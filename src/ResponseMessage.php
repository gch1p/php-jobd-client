<?php

namespace jobd;

class ResponseMessage extends Message {

    protected $error;
    protected $data;

    /**
     * Response constructor.
     * @param null $error
     * @param null $data
     */
    public function __construct($error = null, $data = null) {
        parent::__construct(Message::RESPONSE);

        $this->error = $error;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getContent(): array {
        return [$this->error, $this->data];
    }

    /**
     * @return mixed
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

}