<?php

namespace App\Helpers;

class ResponseHelper
{ 
 public static function success($data= null, $message = null, $statusCode=200)
{
    $response= [
        'status' => true,
        'messageType' => 'SU',
        'content' => $data,
        'message' => $message,
    ];
    return response()->json($response, $statusCode);
}

public static function error ($message = null, $subtitle = null, $statusCode=400)
{
    $response = [
        'status' => false,
        'messageType' => 'EM',
        'message' => $message,
        'subtitle' => $subtitle,
    ];
    return response ()->json($response, $statusCode);
}

public static function validacionesFail($message = null, $subtitle = null, $statusCode = 400, $validator = null)
{
    $response =[
        'status' => false,
        'messageType' => 'EV',
        'message' => $message,
        'subtitle' => $subtitle,
        'validations'=> $validator
    ];

    return response() ->json($response, $statusCode);
}


}