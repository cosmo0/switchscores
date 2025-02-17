<?php


namespace App\Services;

use App\ReviewLink;
use App\Partner;

use Illuminate\Support\Facades\DB;

class ReviewLinkService
{
    /**
     * @param $gameId
     * @param $siteId
     * @param $url
     * @param $ratingOriginal
     * @param $ratingNormalised
     * @param $reviewDate
     * @param $reviewType
     * @param $desc
     * @param null $userId
     * @return ReviewLink
     */
    public function create(
        $gameId, $siteId, $url, $ratingOriginal, $ratingNormalised, $reviewDate, $reviewType, $desc = null, $userId = null
    )
    {
        return ReviewLink::create([
            'game_id' => $gameId,
            'site_id' => $siteId,
            'url' => $url,
            'rating_original' => $ratingOriginal,
            'rating_normalised' => $ratingNormalised,
            'review_date' => $reviewDate,
            'review_type' => $reviewType,
            'description' => $desc,
            'user_id' => $userId,
        ]);
    }

    /**
     * @param ReviewLink $reviewLinkData
     * @param $gameId
     * @param $siteId
     * @param $url
     * @param $ratingOriginal
     * @param $ratingNormalised
     * @param $reviewDate
     * @param $desc
     * @return void
     */
    public function edit(
        ReviewLink $reviewLinkData,
        $gameId, $siteId, $url, $ratingOriginal, $ratingNormalised, $reviewDate, $desc = null
    )
    {
        $values = [
            'game_id' => $gameId,
            'site_id' => $siteId,
            'url' => $url,
            'rating_original' => $ratingOriginal,
            'rating_normalised' => $ratingNormalised,
            'review_date' => $reviewDate,
            'description' => $desc,
        ];

        $reviewLinkData->fill($values);
        $reviewLinkData->save();
    }

    public function delete($linkId)
    {
        ReviewLink::where('id', $linkId)->delete();
    }

    /**
     * @param $id
     * @return ReviewLink
     */
    public function find($id)
    {
        return ReviewLink::find($id);
    }

    public function getByUrl($url)
    {
        return ReviewLink::where('url', $url)->first();
    }

    public function getAll($limit = null)
    {
        $reviewLinks = ReviewLink::orderBy('id', 'desc');
        if ($limit) {
            $reviewLinks = $reviewLinks->limit($limit);
        }
        $reviewLinks = $reviewLinks->get();
        return $reviewLinks;
    }

    public function getAllBySite($siteId)
    {
        $reviewLinks = ReviewLink::where('site_id', $siteId)
            ->orderBy('review_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return $reviewLinks;
    }

    public function getGameIdsReviewedBySite($siteId)
    {
        $reviewList = ReviewLink::where('site_id', $siteId)
            ->orderBy('id', 'desc')
            ->pluck('game_id');

        return $reviewList;
    }

    /**
     * @deprecated
     * @param $siteId
     * @return mixed
     */
    public function getAllGameIdsReviewedBySite($siteId)
    {
        $gameIds = ReviewLink::select('review_links.game_id')
            ->where('site_id', $siteId);
        return $gameIds;
    }

    public function countAllGameIdsReviewedBySite($siteId)
    {
        $gameIds = ReviewLink::select('review_links.game_id')
            ->where('site_id', $siteId)->count();
        return $gameIds;
    }

    public function countBySite($siteId)
    {
        return ReviewLink::where('site_id', $siteId)->count();
    }

    public function countActive()
    {
        $reviewCount = ReviewLink::select('review_links.*', 'review_sites.name')
            ->join('partners', 'review_links.site_id', '=', 'partners.id')
            ->where('partners.status', '=', Partner::STATUS_ACTIVE)
            ->count();
        return $reviewCount;
    }

    public function countActiveByYearMonth($year, $month)
    {
        $reviewCount = ReviewLink::select('review_links.*', 'review_sites.name')
            ->join('partners', 'review_links.site_id', '=', 'partners.id')
            ->where('partners.status', '=', Partner::STATUS_ACTIVE)
            ->whereYear('review_links.review_date', $year)
            ->whereMonth('review_links.review_date', $month)
            ->count();
        return $reviewCount;
    }

    public function getHighlightsFullList($dayInterval = 7)
    {
        $reviewLinks = DB::select('
            SELECT g.*, count(rl.id) AS recent_review_count
            FROM review_links rl
            JOIN games g ON rl.game_id = g.id
            JOIN partners p ON rl.site_id = p.id
            WHERE rl.review_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY rl.game_id
            ORDER BY g.eu_release_date DESC, g.id ASC
        ', [$dayInterval]);

        return $reviewLinks;
    }

    public function getHighlightsRecentlyRanked($dayInterval = 7)
    {
        $reviewLinks = DB::select('
            SELECT g.*, count(rl.id) AS recent_review_count
            FROM review_links rl
            JOIN games g ON rl.game_id = g.id
            JOIN partners p ON rl.site_id = p.id
            WHERE rl.review_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND g.review_count > 2
            GROUP BY rl.game_id
            HAVING g.review_count - count(rl.id) < 3
            ORDER BY g.rating_avg DESC, g.eu_release_date DESC, g.id ASC
        ', [$dayInterval]);

        return $reviewLinks;
    }

    public function getHighlightsStillUnranked($dayInterval = 7)
    {
        $reviewLinks = DB::select('
            SELECT g.*, count(rl.id) AS recent_review_count
            FROM review_links rl
            JOIN games g ON rl.game_id = g.id
            JOIN partners p ON rl.site_id = p.id
            WHERE rl.review_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND g.review_count < 3
            GROUP BY rl.game_id
            ORDER BY g.rating_avg DESC, g.eu_release_date DESC, g.id ASC
        ', [$dayInterval]);

        return $reviewLinks;
    }

    public function getHighlightsAlreadyRanked($dayInterval = 7)
    {
        $reviewLinks = DB::select('
            SELECT g.*, count(rl.id) AS recent_review_count
            FROM review_links rl
            JOIN games g ON rl.game_id = g.id
            JOIN partners p ON rl.site_id = p.id
            WHERE rl.review_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND g.review_count > 2
            GROUP BY rl.game_id
            HAVING g.review_count - count(rl.id) > 2
            ORDER BY g.rating_avg DESC, g.eu_release_date DESC, g.id ASC
        ', [$dayInterval]);

        return $reviewLinks;
    }

    public function getLatestNaturalOrder($limit = 10)
    {
        $reviewLinks = ReviewLink::orderBy('review_date', 'desc')
            ->limit($limit)
            ->get();
        return $reviewLinks;
    }

    public function getLatestBySite($siteId, $limit = 20)
    {
        $reviewLinks = ReviewLink::where('site_id', $siteId)
            ->orderBy('review_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
        return $reviewLinks;
    }

    public function getAllWithoutDate()
    {
        $reviewLinks = ReviewLink::whereNull('review_date')
            ->orderBy('id', 'desc')
            ->get();
        return $reviewLinks;
    }

    public function getByGame($gameId)
    {
        $gameReviews = ReviewLink::select('review_links.*', 'partners.name')
            ->join('partners', 'review_links.site_id', '=', 'partners.id')
            ->where('game_id', $gameId)
            ->where('partners.status', '=', Partner::STATUS_ACTIVE)
            ->orderBy('review_links.rating_normalised', 'asc')
            //->orderBy('review_links.review_date', 'desc')
            //->orderBy('partners.name', 'asc')
            ->get();
        return $gameReviews;
    }

    public function getByGameAndSite($gameId, $siteId)
    {
        $gameReview = ReviewLink::where('game_id', $gameId)
            ->where('site_id', $siteId)
            ->first();
        return $gameReview;
    }

    public function getByUser($userId)
    {
        $reviewLinks = ReviewLink::where('user_id', $userId)
            ->orderBy('review_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return $reviewLinks;
    }

    public function getNormalisedRating($ratingOriginal, Partner $reviewSite)
    {
        $normalisedScaleLimit = 10;

        if ($reviewSite->rating_scale != $normalisedScaleLimit) {
            $scaleMultiple = $normalisedScaleLimit / $reviewSite->rating_scale;
            $ratingNormalised = round($ratingOriginal * $scaleMultiple, 2);
        } else {
            $ratingNormalised = $ratingOriginal;
        }

        return $ratingNormalised;
    }

    public function getSiteReviewStats($siteId)
    {
        $reviewAverage = DB::select("
            SELECT
            count(*) AS ReviewCount,
            sum(rl.rating_normalised) AS ReviewSum,
            avg(rl.rating_normalised) AS ReviewAvg
            FROM review_links rl
            LEFT JOIN partners p ON rl.site_id = p.id
            WHERE p.id = ?
        ", array($siteId));

        return $reviewAverage;
    }

    public function getSiteScoreDistribution($siteId)
    {
        $reviewScores = DB::select("
            SELECT round(rating_normalised, 0) AS RatingValue, count(*) AS RatingCount
            FROM review_links
            WHERE site_id = ?
            GROUP BY round(rating_normalised, 0);
        ", [$siteId]);

        if (!$reviewScores) return null;

        $scoresArray = [
            '1' => '0',
            '2' => '0',
            '3' => '0',
            '4' => '0',
            '5' => '0',
            '6' => '0',
            '7' => '0',
            '8' => '0',
            '9' => '0',
            '10' => '0',
        ];

        foreach ($reviewScores as $score) {
            $scoreValue = $score->RatingValue;
            $scoreCount = $score->RatingCount;
            $scoresArray[$scoreValue] = $scoreCount;
        }

        return $scoresArray;
    }

    public function getFullScoreDistributionByYear($year)
    {
        $reviewScores = DB::select("
            SELECT round(rating_normalised, 0) AS RatingValue, count(*) AS RatingCount
            FROM review_links
            WHERE YEAR(review_date) = ?
            GROUP BY round(rating_normalised, 0);
        ", [$year]);

        if (!$reviewScores) return null;

        $scoresArray = [
            '0' => '0',
            '1' => '0',
            '2' => '0',
            '3' => '0',
            '4' => '0',
            '5' => '0',
            '6' => '0',
            '7' => '0',
            '8' => '0',
            '9' => '0',
            '10' => '0',
        ];

        foreach ($reviewScores as $score) {
            $scoreValue = $score->RatingValue;
            $scoreCount = $score->RatingCount;
            $scoresArray[$scoreValue] = $scoreCount;
        }

        return $scoresArray;
    }

    public function getReviewCountStatsByYear($year)
    {
        $reviewCountStats = DB::select('
            SELECT review_count, count(*) AS count
            FROM games
            WHERE release_year = ?
            AND review_count != 0
            GROUP BY review_count
            ORDER BY review_count DESC
        ', [$year]);

        return $reviewCountStats;
    }

    public function getBySiteScore($siteId, $rating)
    {
        $reviewScores = ReviewLink::where('site_id', $siteId)
            ->where(DB::raw('round(rating_normalised, 0)'), $rating)
            ->get();

        return $reviewScores;
    }
}