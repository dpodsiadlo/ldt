<?php

namespace DPodsiadlo\Log;


use JsonSerializable;

class Response implements JsonSerializable
{
    private $headers = [];
    private $body = "";
    private $status = 200;


    /**
     * Response constructor.
     * @param mixed $body
     * @param mixed $headers
     */
    public function __construct(\Illuminate\Http\Response $response)
    {
        $this->body = $response->content();
        $this->headers = $response->headers->all();
        $this->status = $response->getStatusCode();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $res = [
            'status' => $this->status,
            'body' => $this->body
        ];


        if (isset($this->headers))
            $res['headers'] = $this->headers;


        return $res;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }
}