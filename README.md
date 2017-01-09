# LDT - Debug Tools for Laravel 

A package that extends Monolog and throws debug data to an external debugger. 

### Installation

Via Composer

``` bash
$ composer require dpodsiadlo/ldt
```

### Configuration

Once installed, register Laravel service provider, in your `config/app.php`:

```php
'providers' => [
	...
    DPodsiadlo\LDT\Providers\LDTServiceProvider::class,
]
```

Also register a middleware for logging requests and responses, in your `app/Http/Kernel.php`:

```php
protected $middleware = [
    ...
    \DPodsiadlo\LDT\Middleware\LogSender::class,        
];
```


You might also want to change the default (1800) port for external debugger (in `.env`): 
```
LDT_DEBUG_PORT=1801
```


### Basic Usage

Use default Laravel Log Facade that provides access to the Monolog Writter.  

```php
use Log; //Laravel Log Facade

...

Log::emergency($error);
Log::alert($error);
Log::critical($error);
Log::error($error);
Log::warning($error);
Log::notice($error);
Log::info($error);
Log::debug($error);

```

### Custom requests

Here is an example how to log a separate custom request with response, it might be helpful for debugging third party APIs: 

```php
use DPodsiadlo\LDT\LDT;
use DPodsiadlo\LDT\Log\Request;
use DPodsiadlo\LDT\Log\Response;

...

$params = ["param1" => "someValue"];
$targetUrl = "https://third-party-service.com";

$context = stream_context_create([
        'http' => [
            'header' => "Accept: */*\r\nContent-Type: application/json\r\nUser-Agent: API-WRAPER/1.0\r\n",
            'method' => "POST",
            'content' => json_encode($params),
            'ignore_errors' => true
        ]
    ]);


$res = file_get_contents($targetUrl, false, $context);


if (!empty($http_response_header)) {

    $status = (int)explode(' ', $http_response_header[0])[1];

    LDT::log(new Request($targetUrl, "POST", $params), new Response($res, $status, $http_response_header]), true);
}

```


##### Custom storage

Also it's possible to store the custom log to a separate log file:

```php
use DPodsiadlo\LDT\LDT;
use DPodsiadlo\LDT\Log\Request;
use DPodsiadlo\LDT\Log\Response;

...

$params = ["param1" => "someValue"];
$targetUrl = "https://third-party-service.com";

$context = stream_context_create([
        'http' => [
            'header' => "Accept: */*\r\nContent-Type: application/json\r\nUser-Agent: API-WRAPER/1.0\r\n",
            'method' => "POST",
            'content' => json_encode($params),
            'ignore_errors' => true
        ]
    ]);


$res = file_get_contents($targetUrl, false, $context);


if (!empty($http_response_header)) {

    $status = (int)explode(' ', $http_response_header[0])[1];

    LDT::log(new Request($targetUrl, "POST", $params), new Response($res, $status, $http_response_header]), true, "third-party-log");
}

```

Log file name will be generated automatically based on `$storage` parameter and Laravel`app.log` config. 

### Remote debugging 

You might also want to debug a remote instance of your project. To do that please use SSH client with port forwarding enabled:

```bash
ssh me@some-server.com -R 1800:localhost:1800
```
 

### External Debugger

LDT is compatible with [LDT Console](https://chrome.google.com/webstore/detail/ldt-console/icopnphbbahdenofbalbonlkclmfaoio) - Free Chrome App.

### License

The MIT License (MIT). Please see [License File](https://github.com/dpodsiadlo/ldt/blob/master/LICENSE) for more information.
