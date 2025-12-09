<?php 

namespace App\Http\Actions\Api;

abstract class ActionsApiAbstract {

    public function __construct()
    {

    }

    public function returnSuccess( $message, $data = [] ) {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];
    }

    public function returnError( $messages, $data = [] ) {
        return [
            'success' => false,
            'messages' => $messages,
            'data'    => $data,
        ];
    }

    public function returnData( $data, $message = '', $extra = [] ) {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'extra'   => $extra,
        ];
    }

}