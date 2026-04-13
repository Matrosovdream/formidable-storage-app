<?php 

namespace App\Http\Actions\Rest;

use App\Services\Site\SiteService;

abstract class ActionsRestAbstract {

    protected $siteService;

    public function __construct()
    {
        $this->siteService = new SiteService();
    }

    public function prepareRequestData( $request ) {

        $token = $this->getBearerToken( $request );
        $site = $this->siteService->getSiteByToken( $token );

        return [
            'token' => $token,
            'site'  => $site ?? null,
            'data'  => $request->all(),
        ];
    }

    public function getBearerToken( $request ) {
        return $request->bearerToken();
    }

    public function returnSuccess( $message, $data = [], bool $cache = false ) {
        return [
            'success' => true,
            'cache'   => $cache,
            'message' => $message,
            'data'    => $data,
        ];
    }

    public function returnError( $message, $data = [] ) {
        return [
            'success' => false,
            'cache'   => false,
            'message' => $message,
            'data'    => $data,
        ];
    }

    public function returnData( $data, $message = '', $extra = [], bool $cache = false ) {
        return [
            'success' => true,
            'cache'   => $cache,
            'message' => $message,
            'data'    => $data,
            'extra'   => $extra,
        ];
    }

}