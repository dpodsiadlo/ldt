<?php

namespace DPodsiadlo\LDT\Log;


use JsonSerializable;

class Request implements JsonSerializable
{
    private $method = "";
    private $url = "";
    private $httpVersion = "";
    private $cookies = [];
    private $headers = [];
    private $parameters = [];
    private $clientIps = [];

    /**
     * Request constructor.
     * @param \Illuminate\Http\Request|array|string $request
     * @param null|string $method
     * @param array $parameters
     * @param array $headers
     * @param array $cookies
     */
    public function __construct($request, $method = null, $parameters = [], $headers = [], $cookies = [])
    {

        if (is_a($request, \Illuminate\Http\Request::class)) {
            $this->method = $request->method();
            $this->url = $request->url();
            $this->httpVersion = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : "";
            $this->headers = $request->headers->all();
            $this->cookies = $request->cookies->all();
            $this->clientIps = $request->getClientIps();
            $this->parameters = $request->all();

        } elseif (is_array($request)) {

            $this->method = @$request['method'];
            $this->url = @$request['url'];
            $this->httpVersion = @$request['httpVersion'];
            $this->cookies = @$request['cookies'];
            $this->headers = @$request['headers'];
            $this->parameters = @$request['parameters'];
            $this->clientIps = @$request['clientIps'];

        } elseif (is_string($request)) {
            $this->url = $request;
            $this->method = $method;
            $this->parameters = $parameters;
            $this->headers = $headers;
            $this->cookies = $cookies;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'method' => $this->method,
            'url' => $this->url,
            'httpVersion' => $this->httpVersion,
            'cookies' => $this->cookies,
            'headers' => $this->headers,
            'parameters' => $this->parameters,
            'clientIps' => $this->clientIps
        ];
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }
}