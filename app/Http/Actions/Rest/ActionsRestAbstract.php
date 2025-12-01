<?php 

namespace App\Http\Actions\Rest;

class ActionsRestAbstract {

    public function returnSuccess( $message, $data = [] ) {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];
    }

    public function returnError( $message, $data = [] ) {
        return [
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ];
    }

}