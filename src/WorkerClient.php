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

    /**
     * @param string $target
     * @param int $concurrency
     * @return ResponseMessage
     * @throws Exception
     */
    public function addTarget(string $target, int $concurrency): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('add-target', [
                'target' => $target,
                'concurrency' => $concurrency
            ]))
        );
    }

    /**
     * @param string $target
     * @return ResponseMessage
     * @throws Exception
     */
    public function removeTarget(string $target): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('remove-target', [
                'target' => $target
            ]))
        );
    }

    /**
     * @param string $target
     * @param int $concurrency
     * @return ResponseMessage
     * @throws Exception
     */
    public function setTargetConcurrency(string $target, int $concurrency): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('set-target-concurrency', [
                'target' => $target,
                'concurrency' => $concurrency
            ]))
        );
    }

}