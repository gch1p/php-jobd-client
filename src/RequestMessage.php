<?php

namespace jobd;

class RequestMessage extends Message {

    protected $requestNo;
    protected $requestType;
    protected $requestData;
    protected $password;

    /**
     * Request constructor.
     * @param string $request_type
     * @param null|array $request_data
     */
    public function __construct(string $request_type, $request_data = null)
    {
        parent::__construct(Message::REQUEST);

        $this->requestData = $request_data;
        $this->requestType = $request_type;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @param int $no
     */
    public function setRequestNo(int $no)
    {
        $this->requestNo = $no;
    }

    /**
     * @return string[]
     */
    protected function getContent(): array
    {
        $request = [
            'type' => $this->requestType,
            'no' => $this->requestNo,
        ];

        if (!is_null($this->requestData))
            $request['data'] = $this->requestData;

        if (!is_null($this->password))
            $request['password'] = $this->password;

        return $request;
    }

}