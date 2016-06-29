<?php
namespace DPodsiadlo\LDT\Middleware;

use Closure;
use DPodsiadlo\LDT\Facades\LDT;


class LogSender
{

    public function handle($request, Closure $next)
    {
        LDT::request($request);

        return $next($request);
    }

    public function terminate($request, $response)
    {

        LDT::response($response);
        LDT::send();

    }

}