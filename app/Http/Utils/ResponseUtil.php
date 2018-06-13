<?php

namespace App\Http\Utils;

use App\EmptyData;

class ResponseUtil
{
    /**
     * @param string $message
     * @param mixed  $data
     *
     * @return array
     */
    public static function makeResponse($message, $data = [])
    {
        return [
            'success' => true,
            'code' => 200,
            'message' => $message,
            'data'    => empty($data) ? new EmptyData() : $data,
        ];
    }

    /**
     * @param string $message
     * @param array  $data
     * @param string  $returnCode
     *
     * @return array
     */
    public static function makeError($message, $data = [], $returnCode)
    {
        $res = [
            'success' => false,
            'code' => $returnCode,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }else{
            $res['data'] = new EmptyData();
        }

        return $res;
    }

}