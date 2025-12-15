<?php
namespace App\Repositories\Site;

use App\Repositories\AbstractRepo;
use App\Models\Site\Site;
use App\Models\Frm\FrmField;
use App\Models\Frm\FrmEmailLog;
use App\Models\Frm\FrmEntryHistory;


class SiteRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new Site();

    }

    public function getByToken(string $token)
    {
        $tokenRepo = new SiteTokenRepo();
        
        $token = $tokenRepo->getByField('token', $token);
        return $this->getById( $token['site_id'] );
    }

    public function getSiteToken( int $site_id )
    {
        $tokenRepo = new SiteTokenRepo();
        
        $token = $tokenRepo->model
            ->where('site_id', $site_id)
            ->first();

        return $token ? $token->token : null;
    }

    public function addSiteToken( int $site_id )
    {

        $tokenRepo = new SiteTokenRepo();

        // Remove existing token if any
        $tokenRepo->model
            ->where('site_id', $site_id)
            ->delete();
        
        $token = bin2hex(random_bytes(16));

        $tokenData = [
            'site_id' => $site_id,
            'token' => $token
        ];

        $tokenRepo->create( $tokenData );

        return $token;
    }

    public function addSite( array $data )
    {
        $site = $this->create( $data );

        // Create token
        $this->addSiteToken( $site['id'] );

        return $this->getById( $site['id'] );
    }

    public function deleteSite( int $site_id )
    {
        $tokenRepo = new SiteTokenRepo();

        // Delete token
        $tokenRepo->model
            ->where('site_id', $site_id)
            ->delete();

        // Delete site
        return $this->delete( $site_id );

    }

    public function getSiteStats( int $site_id )
    {
        $stats = [];

        // Frm stats
        $stats['frm'] = [
            'fields_count' => FrmField::where('site_id', $site_id)->count(),
            'emails_log_count' => FrmEmailLog::where('site_id', $site_id)->count(),
            'entry_history_count' => FrmEntryHistory::where('site_id', $site_id)->count(),
        ];

        return $stats;
    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'name' => $item->name,
            'url' => $item->url,
            'token' => $this->getSiteToken( $item->id ),
            'stats' => $this->getSiteStats( $item->id ),
            'Model' => $item
        ];
        return $res;
    }

}