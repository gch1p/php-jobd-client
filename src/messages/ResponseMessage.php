<?php

namespace jobd\messages;

class ResponseMessage extends Message {

    protected $requestNo;
    protected $error;
    protected $data;

    /**
     * Response constructor.
     *
     * @param int $request_no
     * @param null $error
     * @param null $data
     */
    public function __construct(int $request_no, $error = null, $data = null)
    {
        parent::__construct(Message::RESPONSE);

        $this->requestNo = $request_no;
        $this->error = $error;
        $this->data = $data;
    }

    /**
     * @return array
     */
    protected function getContent(): array
    {
        $response = [
            'no' => $this->requestNo
        ];

        if (!is_null($this->error))
            $response['error'] = $this->error;

        if (!is_null(!$this->data))
            $response['data'] = $this->data;

        return $response;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getRequestNo(): int
    {
        return $this->requestNo;
    }

}