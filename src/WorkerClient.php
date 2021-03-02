<?php

namespace jobd;

class WorkerClient extends Client {

    public function __construct(int $port = Client::WORKER_PORT, ...$args)
    {
        parent::__construct($port, ...$args);
    }

    /**
     * @return ResponseMessage
     * @throws \Exception
     */
    public function status(): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('status'))
        );
    }

    /**
     * @param string[] $targets
     * @return ResponseMessage
     * @throws \Exception
     */
    public function poll(array $targets = []): ResponseMessage
    {
        $data = [];
        if (!empty($targets))
            $data['targets'] = $targets;

        return $this->recv(
            $this->sendRequest(new RequestMessage('poll', $data))
        );
    }

    /**
     * @param int[] $ids
     * @return ResponseMessage
     * @throws \Exception
     */
    public function runManual(array $ids): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('run-manual', ['ids' => $ids]))
        );
    }

}