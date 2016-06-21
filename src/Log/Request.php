<?php

namespace DPodsiadlo\Log;


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
     */
    public function __construct(\Illuminate\Http\Request $request)
    {


        $this->method = $request->method();
        $this->url = $request->url();
        $this->httpVersion = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : "";


        $this->headers = $request->headers->all();
        $this->cookies = $request->cookies->all();
        $this->clientIps = $request->getClientIps();
        $this->parameters = $request->all();

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