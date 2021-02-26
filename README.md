# php-jobd-client

This is a simple PHP client for [jobd](https://github.com/gch1p/jobd). It can send
requests and read responses to/from *jobd* and *jobd-master* instances.

## Installation

```
composer require ch1p/jobd-client
```

## Usage

The API is compact and simple, just read the code of `Client.php`.

Here's a small example.

```php
try {
    $jobd = new jobd\Client(jobd\Client::MASTER_PORT, '127.0.0.1');
} catch (Exception $e) {
    die("Failed to connect.\n");
}

try {
    // poke master to send poll requests to workers
    $response = $jobd->poke(['target_name', 'another_name']);
    
    // get status from master
    $status = $jobd->status()->getData();
} catch (Exception $e) {
    die('jobd error: '.$e->getMessage()."\n");
}

$jobd->close();
```

## License

BSD-2c