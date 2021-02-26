<?php

namespace jobd;


class Client {

    const WORKER_PORT = 13596;
    const MASTER_PORT = 13597;

    const EOT = "\4";

    protected $host;
    protected $port;
    protected $password;
    protected $sock;

    /**
     * JobdClient constructor.
     * @param int $port
     * @param string $host
     * @param string $password
     */
    public function __construct(int $port, string $host = '127.0.0.1', string $password = '') {
        $this->port = $port;
        $this->host = $host;
        $this->password = $password;

        $this->sock = fsockopen($this->host, $this->port);
        if (!$this->sock)
            throw new \Exception("Failed to connect to {$this->host}:{$this->port}");
    }

    /**
     * @return mixed
     */
    public function ping() {
        $this->send(new RequestMessage('ping'));
        return $this->recv();
    }

    /**
     * @param array $targets
     * @return mixed
     */
    public function poke(array $targets) {
        $this->send(new RequestMessage('poke', ['targets' => $targets]));
        return $this->recv();
    }

    /**
     * @return mixed
     */
    public function status() {
        $this->send(new RequestMessage('status'));
        return $this->recv();
    }

    public function poll(array $targets) {
        $this->send(new RequestMessage('poll', ['targets' => $targets]));
        return $this->recv();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function runManual(int $id) {
        $this->send(new RequestMessage('run-manual', ['id' => $id]));
        return $this->recv();
    }

    /**
     * @param RequestMessage $request
     */
    public function send(RequestMessage $request) {
        if ($this->password)
            $request->setPassword($this->password);

        $serialized = $request->serialize();

        fwrite($this->sock, $serialized . self::EOT);
    }

    /**
     * @return mixed
     */
    public function recv() {
        $messages = [];
        $buf = '';
        while (!feof($this->sock)) {
            $buf .= fread($this->sock, 1024);
            $buflen = strlen($buf);
            if ($buflen > 0 && $buf[$buflen-1] == self::EOT)
                break;
        }

        $offset = 0;
        $eot_pos = 0;
        do {
            $eot_pos = strpos($buf, self::EOT, $offset);
            if ($eot_pos !== false) {
                $message = substr($buf, $offset, $eot_pos);
                $messages[] = $message;

                $offset = $eot_pos + 1;
            }
        } while ($eot_pos !== false && $offset < $buflen-1);

        if (empty($message))
            throw new \Exception("Malformed response: no messages found. Response: {$buf}");

        if (count($messages) > 1)
            trigger_error(__METHOD__.": received more than one message");

        return self::parseMessage($messages[0]);
    }

    protected static function parseMessage(string $raw_string) {
        $raw = json_decode($raw_string, true);
        if (!is_array($raw) || count($raw) != 2)
            throw new \Exception("Malformed response: {$raw_string}");

        list($type, $data) = $raw;

        switch ($type) {
            case Message::REQUEST:
                if (!$data || !is_array($data) || !isset($data['type']) || !is_string($data['type']))
                    throw new \Exception('Malformed REQUEST message');

                $message = new RequestMessage($data['type'], $data['data'] ?? null);
                if (isset($data['password']))
                    $message->setPassword($data['password']);

                return $message;

            case Message::RESPONSE:
                if (!is_array($data) || count($data) < 2)
                    throw new \Exception('Malformed RESPONSE message');

                $message = new ResponseMessage(...$data);
                return $message;
        }
    }

    /**
     * @return bool
     */
    public function close() {
        return fclose($this->sock);
    }

}