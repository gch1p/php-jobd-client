<?php

namespace jobd;

class WorkerClient extends Client {

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
    public function poll(array $targets): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('poll', ['targets' => $targets]))
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