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
'providers' => array(
	...
    DPodsiadlo\Providers\LDTServiceProvider::class,
)
```

Also register a middleware for logging requests and responses, in your `app/Http/Kernel.php`:

```php
protected $middleware = [
    ...
    \DPodsiadlo\Middleware\LogSender::class,        
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


### External Debugger

LDT is compatible with [LDT Console](https://chrome.google.com/webstore/detail/ldt-console/icopnphbbahdenofbalbonlkclmfaoio) - Free Chrome App.

### License

The MIT License (MIT). Please see [License File](https://github.com/dpodsiadlo/ldt/blob/master/LICENSE) for more information.
