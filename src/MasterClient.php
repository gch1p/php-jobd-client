<?php

namespace jobd;

use jobd\exceptions\JobdException;
use jobd\messages\RequestMessage;
use jobd\messages\ResponseMessage;

class MasterClient extends Client {

    public function __construct(int $port = Client::MASTER_PORT, ...$args)
    {
        parent::__construct($port, ...$args);
    }

    /**
     * @param array $targets
     * @return ResponseMessage
     * @throws JobdException
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
     * @throws JobdException
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
     * @throws JobdException
     */
    public function runManual(array $jobs): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('run-manual', ['jobs' => $jobs]))
        );
    }

    /**
     * @param int $job_id
     * @param int $signal
     * @param string $target
     * @return ResponseMessage
     * @throws JobdException
     */
    public function sendSignal(int $job_id, int $signal, string $target): ResponseMessage
    {
        return $this->sendSignals([
            [
                'id' => $job_id,
                'signal' => $signal,
                'target' => $target
            ]
        ]);
    }

    /**
     * @param array $data
     * @return ResponseMessage
     * @throws JobdException
     */
    public function sendSignals(array $data): ResponseMessage {
        return $this->recv(
            $this->sendRequest(new RequestMessage('send-signal', ['jobs' => $data]))
        );
    }

}