<?php

namespace App\Http\Controllers\Staff\Stats;

use Illuminate\Routing\Controller as Controller;

use App\Traits\SwitchServices;
use App\Traits\StaffView;

class ReviewSiteController extends Controller
{
    use SwitchServices;
    use StaffView;

    public function show()
    {
        $bindings = $this->getBindingsStatsSubpage('Review site stats');

        $serviceReviewLinks = $this->getServiceReviewLink();
        $servicePartner = $this->getServicePartner();
        $serviceGameReleaseDate = $this->getServiceGameReleaseDate();
        $serviceGame = $this->getServiceGame();
        $serviceTopRated = $this->getServiceTopRated();
        $serviceReviewStats = $this->getServiceReviewStats();

        $bindings['RankedGameCount'] = $serviceGame->countRanked();
        $bindings['UnrankedGameCount'] = $serviceTopRated->getUnrankedCount();

        $releasedGameCount = $serviceGameReleaseDate->countReleased();
        $reviewLinkCount = $serviceReviewLinks->countActive();

        $bindings['ReleasedGameCount'] = $releasedGameCount;
        $bindings['ReviewLinkCount'] = $reviewLinkCount;

        $reviewSitesActive = $servicePartner->getActiveReviewSites();
        $reviewSitesRender = [];

        foreach ($reviewSitesActive as $reviewSite) {

            $id = $reviewSite->id;
            $name = $reviewSite->name;
            $linkTitle = $reviewSite->link_title;
            $reviewCount = $reviewSite->review_count;
            $latestReviewDate = $reviewSite->last_review_date;

            $reviewLinkContribTotal = $serviceReviewStats->calculateContributionPercentage($reviewCount, $reviewLinkCount);
            $reviewGameCompletionTotal = $serviceReviewStats->calculateContributionPercentage($reviewCount, $releasedGameCount);

            $reviewSitesRender[] = [
                'id' => $id,
                'name' => $name,
                'link_title' => $linkTitle,
                'review_count' => $reviewCount,
                'review_link_contrib_total' => $reviewLinkContribTotal,
                'review_game_completion_total' => $reviewGameCompletionTotal,
                'latest_review_date' => $latestReviewDate,
            ];

        }

        $bindings['ReviewSitesArray'] = $reviewSitesRender;

        return view('staff.stats.reviewSites', $bindings);
    }
}
