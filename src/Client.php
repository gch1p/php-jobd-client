<?php

namespace jobd;


class Client {

    const WORKER_PORT = 7080;
    const MASTER_PORT = 7081;

    const EOT = "\4";
    const REQUEST_NO_LIMIT = 999999;

    protected $host;
    protected $port;
    protected $password;
    protected $sock;
    protected $passwordSent = false;
    protected $lastOutgoingRequestNo = null;

    /**
     * JobdClient constructor.
     * @param int $port
     * @param string $host
     * @param string $password
     * @throws \Exception
     */
    public function __construct(int $port, string $host = '127.0.0.1', string $password = '') {
        $this->port = $port;
        $this->host = $host;
        $this->password = $password;

        $this->sock = fsockopen($this->host, $this->port);
        if (!$this->sock)
            throw new \Exception("Failed to connect to {$this->host}:{$this->port}");

        // 0 is reserved
        $this->lastOutgoingRequestNo = mt_rand(1, self::REQUEST_NO_LIMIT);
    }

    /**
     * JobdClient destructor.
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * @return ResponseMessage
     * @throws \Exception
     */
    public function ping() {
        $this->send(new PingMessage());
        return $this->recv();
    }

    /**
     * @param RequestMessage $request
     * @return int
     */
    public function sendRequest(RequestMessage $request) {
        if ($this->password && !$this->passwordSent) {
            $request->setPassword($this->password);
            $this->passwordSent = true;
        }

        $no = $this->getNextOutgoingRequestNo();
        $request->setRequestNo($no);

        $this->send($request);

        return $no;
    }

    /**
     * @param Message $message
     */
    public function send(Message $message) {
        $serialized = $message->serialize();
        fwrite($this->sock, $serialized . self::EOT);
    }

    /**
     * @param int $request_no
     * @return ResponseMessage
     * @throws \Exception
     */
    public function recv(int $request_no = -1) {
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

        if (empty($messages))
            throw new \Exception("Malformed response: no messages found. Response: {$buf}");

        if (count($messages) > 1)
            trigger_error(__METHOD__.": received more than one message");

        $response = null;
        $messages = array_map('self::parseMessage', $messages);
        if ($request_no != -1) {
            /**
             * @var ResponseMessage[] $messages
             */
            $messages = array_filter(
                $messages,

                /**
                 * @param ResponseMessage|RequestMessage $message
                 */
                function(Message $message) use ($request_no) {
                    return $message instanceof ResponseMessage
                        && ($message->getRequestNo() === $request_no || $message->getRequestNo() === 0);
                }
            );

            if (empty($messages))
                throw new \Exception("Malformed response: response for {$request_no} not found.");


            if (count($messages) == 2) {
                // weird, we caught response for our $request_no AND a message with reserved zero no
                // but anyway

                for ($i = 0; $i < count($messages); $i++) {
                    $message = $messages[$i];

                    if ($message->getRequestNo() == $request_no)
                        $response = $message;

                    else if ($message->getRequestNo() == 0)
                        trigger_error(__METHOD__.': received an error with reqno=0: '.($message->getError() ?? null));
                }
            }
        }

        if (is_null($response))
            $response = $messages[0];

        if ($response instanceof ResponseMessage) {
            if ($error = $response->getError())
                throw new \Exception($response->getError());
        }

        return $response;
    }

    /**
     * @return int
     */
    protected function getNextOutgoingRequestNo() {
        $this->lastOutgoingRequestNo++;

        if ($this->lastOutgoingRequestNo >= self::REQUEST_NO_LIMIT)
            $this->lastOutgoingRequestNo = 1; // 0 is reserved

        return $this->lastOutgoingRequestNo;
    }

    /**
     * @param string $raw_string
     * @return RequestMessage|ResponseMessage
     * @throws \Exception
     */
    protected static function parseMessage(string $raw_string) {
        $raw = json_decode($raw_string, true);
        if (!is_array($raw) || count($raw) < 1)
            throw new \Exception("Malformed message: {$raw_string}");

        list($type) = $raw;

        switch ($type) {
            case Message::REQUEST:
                $data = $raw[1];
                try {
                    self::validateData($data, [
                        // name      type     required
                        ['type',     's',     true],
                        ['no',       'i',     true],
                        ['password', 's',     false],
                        ['data',     'aifs',  false]
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception("Malformed REQUEST message: {$e->getMessage()}");
                }

                $message = new RequestMessage($data['type'], $data['data'] ?? null);
                $message->setRequestNo($data['no']);
                if (isset($data['password']))
                    $message->setPassword($data['password']);

                return $message;

            case Message::RESPONSE:
                $data = $raw[1];
                try {
                    self::validateData($data, [
                        // name   type     required
                        ['no',    'i',     true],
                        ['data',  'aifs',  false],
                        ['error', 's',     false],
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception("Malformed RESPONSE message: {$e->getMessage()}");
                }

                return new ResponseMessage($data['no'], $data['error'] ?? null, $data['data'] ?? null);

            case Message::PING:
                return new PingMessage();
                break;

            case Message::PONG:
                return new PongMessage();
                break;

            default:
                throw new \Exception("Malformed message: unexpected type {$type}");
        }
    }

    /**
     * @param mixed $data
     * @param array $schema
     * @return bool
     */
    protected static function validateData($data, array $schema) {
        if (!$data || !is_array($data))
            throw new \Exception('data must be array');

        foreach ($schema as $schema_item) {
            list ($key_name, $key_types, $key_required) = $schema_item;
            if (!isset($data[$key_name])) {
                if ($key_required)
                    throw new \Exception("'{$key_name}' is missing");

                continue;
            }

            $passed = false;
            for ($i = 0; $i < strlen($key_types); $i++) {
                $type = $key_types[$i];

                switch ($type) {
                    case 'i':
                        if (is_int($data[$key_name]))
                            $passed = true;
                        break;

                    case 'f':
                        if (is_float($data[$key_name]))
                            $passed = true;
                        break;

                    case 's':
                        if (is_string($data[$key_name]))
                            $passed = true;
                        break;

                    case 'a':
                        if (is_array($data[$key_name]))
                            $passed = true;
                        break;

                    default:
                        trigger_error(__METHOD__.': unexpected type '.$type);
                        break;
                }

                if ($passed)
                    break;
            }

            if (!$passed)
                throw new \Exception("{$key_name}: required type is '{$key_types}'");
        }
    }

    /**
     * @return bool
     */
    public function close() {
        if (!$this->sock)
            return;

        fclose($this->sock);
        $this->sock = null;
    }

}