# php-jobd-client

This is a simple PHP client for [jobd](https://github.com/gch1p/jobd). It can send
requests and read responses to/from *jobd* and *jobd-master* instances.

## Installation

```
composer require ch1p/jobd-client
```

## Usage

The API is compact and simple, just read [`WorkerClient.php`](src/WorkerClient.php),
[`MasterClient.php`](src/MasterClient.php) and [`Client.php`](src/Client.php).

Here's a small example.

```php
try {
    $jobd = new jobd\MasterClient();
} catch (\jobd\exceptions\JobdException $e) {
    die("Failed to connect.\n");
}

try {
    // poke master to send poll requests to workers
    $response = $jobd->poke(['target_name', 'another_name']);
    
    // get status from master
    $status = $jobd->status()->getData();
} catch (\jobd\exceptions\JobdException $e) {
    die('jobd error: '.$e->getMessage()."\n");
}

$jobd->close();
```

## License

BSD-2c