<?php

namespace App\Http\Utils;

trait ApiResponse
{
    /**
     * @var int
     */
    protected $code = 200;

    protected $data = [];

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function withCode($code)
    {

        $this->code = $code;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function withData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function response($message = '', $code = '', array $data = [])
    {
        if (empty($message)) {
            $message = ($code ?: $this->code) === 200 ? 'request was successful' : 'attempt failed';
        }

        return response([
            'code'    => $code ?: $this->code,
            'data'    => $data ?: $this->data,
            'message' => $message,
        ], $this->code);
    }
}