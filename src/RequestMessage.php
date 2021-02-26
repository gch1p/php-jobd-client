<?php

namespace jobd;

class RequestMessage extends Message {

    protected $requestType;
    protected $requestData;
    protected $password;

    /**
     * Request constructor.
     * @param string $request_type
     * @param null $request_data
     */
    public function __construct(string $request_type, $request_data = null) {
        parent::__construct(Message::REQUEST);

        $this->requestData = $request_data;
        $this->requestType = $request_type;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) {
        $this->password = $password;
    }

    /**
     * @return string[]
     */
    public function getContent(): array {
        $request = ['type' => $this->requestType];
        if (!is_null($this->requestData))
            $request['data'] = $this->requestData;
        return $request;
    }

}