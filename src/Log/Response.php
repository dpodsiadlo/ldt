<?php

namespace DPodsiadlo\LDT\Log;


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
    public function __construct($response, $status = 200, $headers = [])
    {

        if (is_a($response, \Symfony\Component\HttpFoundation\Response::class)) {
            $this->body = $response->getContent();
            $this->headers = $response->headers->all();
            $this->status = $response->getStatusCode();
        } elseif (is_array($response)) {
            $this->headers = @$response['headers'];
            $this->body = @$response['body'];
            $this->status = @$response['status'];
        } else {
            $this->body = $response;
            $this->status = $status;

            if (is_array($headers)) {
                foreach ($headers as $key => $header) {
                    if (is_numeric($key)) {
                        $header = explode(' ', $header);
                        $id = strtolower($header[0]);

                        array_shift($header);
                        switch ($id) {
                            case "http/1.0":
                            case "http/1.1":
                            case "http/2.0":

                                break;
                            default:
                                $this->headers[$id] = $header;
                        }


                    } else {
                        $this->headers[$key] = $header;
                    }
                }
            }

        }


        if ($this->isJSON() && is_string($this->body)) {
            try {
                $this->body = json_decode($this->body, true);
            } catch (\Exception $ex) {

            }
        }

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

    public function isJSON()
    {
        return is_array($this->headers["content-type"]) && in_array("application/json", $this->headers["content-type"]);
    }
}