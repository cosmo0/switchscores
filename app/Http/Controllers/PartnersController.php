<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as Controller;

use App\Partner;

use App\Traits\SwitchServices;

class PartnersController extends Controller
{
    use SwitchServices;

    public function landing()
    {
        $bindings = [];

        $servicePartner = $this->getServicePartner();
        $reviewPartnerList = $servicePartner->getReviewSitesWithRecentReviews();
        $bindings['ReviewPartnerList'] = $reviewPartnerList;

        $bindings['TopTitle'] = 'Partners';
        $bindings['PageTitle'] = 'Partners';

        return view('partners.landing', $bindings);
    }

    public function reviewSites()
    {
        $bindings = [];

        $bindings['TopTitle'] = 'Review partners';
        $bindings['PageTitle'] = 'Review partners';

        return view('partners.reviewSites', $bindings);
    }

    public function developersPublishers()
    {
        $bindings = [];

        $bindings['TopTitle'] = 'Developers and Publishers';
        $bindings['PageTitle'] = 'Developers and Publishers';

        return view('partners.developersPublishers', $bindings);
    }

    public function showGamesCompany($linkTitle)
    {
        $servicePartner = $this->getServicePartner();

        $serviceGameDeveloper = $this->getServiceGameDeveloper();
        $serviceGamePublisher = $this->getServiceGamePublisher();

        $partnerData = $servicePartner->getByLinkTitle($linkTitle);
        if (!$partnerData) abort(404);
        if ($partnerData->type_id != Partner::TYPE_GAMES_COMPANY) abort(404);

        $partnerId = $partnerData->id;

        $gameDevList = $serviceGameDeveloper->getGamesByDeveloper($partnerId, true);
        $gamePubList = $serviceGamePublisher->getGamesByPublisher($partnerId, true);

        $mergedGameList = [];
        $usedGameIds = [];

        if ($gameDevList && $gamePubList) {

            foreach ($gameDevList as $item) {
                $gameId = $item->id;
                $item->PartnerType = 'developer';
                $mergedGameList[$gameId] = $item;
                $usedGameIds[] = $gameId;
            }
            foreach ($gamePubList as $item) {
                $gameId = $item->id;
                if (in_array($gameId, $usedGameIds)) {
                    $mergedGameList[$gameId]->PartnerType = 'dev/pub';
                } else {
                    $item->PartnerType = 'publisher';
                    $mergedGameList[] = $item;
                }
            }

        } elseif ($gameDevList) {

            $mergedGameList = $gameDevList;
            foreach ($gameDevList as $item) {
                $item->PartnerType = 'developer';
                $mergedGameList[] = $item;
            }

        } elseif ($gamePubList) {

            $mergedGameList = $gamePubList;
            foreach ($gamePubList as $item) {
                $item->PartnerType = 'publisher';
                $mergedGameList[] = $item;
            }

        }

        $bindings = [];

        $bindings['TopTitle'] = $partnerData->name;
        $bindings['PageTitle'] = $partnerData->name;

        $bindings['PartnerData'] = $partnerData;
        $bindings['PartnerId'] = $partnerId;

        //$bindings['GameDevList'] = $gameDevList;
        //$bindings['GamePubList'] = $gamePubList;
        $bindings['MergedGameList'] = $mergedGameList;

        return view('partners.detail.gamesCompany', $bindings);
    }
}
