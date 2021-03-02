<?php

namespace jobd;

class MasterClient extends Client {

    public function __construct(int $port = Client::MASTER_PORT, ...$args) {
        parent::__construct($port, ...$args);
    }

    /**
     * @param array $targets
     * @return ResponseMessage
     * @throws \Exception
     */
    public function poke(array $targets): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('poke', ['targets' => $targets]))
        );
    }

    /**
     * @param bool $poll_workers
     * @return ResponseMessage
     * @throws \Exception
     */
    public function status(bool $poll_workers = false): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('status', ['poll_workers' => $poll_workers]))
        );
    }

    /**
     * @param array[] $jobs
     * @return ResponseMessage
     * @throws \Exception
     */
    public function runManual(array $jobs): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('run-manual', ['jobs' => $jobs]))
        );
    }

}