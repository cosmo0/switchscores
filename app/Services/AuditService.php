<?php


namespace App\Services;

use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit;


class AuditService
{
    public function getAll($limit = 25)
    {
        $auditList = Audit::orderBy('id', 'desc');
        if ($limit) {
            $auditList = $auditList->limit($limit);
        }
        return $auditList->get();
    }

    public function getAggregatedGameAudits($gameId, $limit = 25)
    {
        /*
         * For reference, here's how we get a game's audits
        $gameAuditsCore = $game->audits()->orderBy('id', 'desc')->get();
        */

        $aggAudits = new Collection();

        // App\Game
        $audits = Audit::where('auditable_type', 'App\Game')->where('auditable_id', $gameId)->orderBy('id', 'desc')->get();
        foreach ($audits as $audit) {
            $aggAudits->push($audit);
        }

        // App\GameReleaseDate
        /*
        $audits = Audit::join('game_release_dates', 'game_release_dates.id', '=', 'audits.auditable_id')
            ->where('game_release_dates.game_id', $gameId)
            ->orderBy('id', 'desc')->get('audits.*');
        foreach ($audits as $audit) {
            $aggAudits->push($audit);
        }
        */

        $aggAudits = $aggAudits->sortByDesc('id');

        if ($aggAudits->count() > $limit) {
            $aggAudits = $aggAudits->take($limit);
        }

        return $aggAudits;
    }
}