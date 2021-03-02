<?php

namespace jobd;

class MasterClient extends Client {

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
     * @return ResponseMessage
     * @throws \Exception
     */
    public function status(): ResponseMessage
    {
        return $this->recv(
            $this->sendRequest(new RequestMessage('status'))
        );
    }

}