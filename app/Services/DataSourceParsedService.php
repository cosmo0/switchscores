<?php


namespace App\Services;

use App\DataSourceParsed;
use App\DataSource;

use Illuminate\Support\Facades\DB;

class DataSourceParsedService
{
    // ********** Generic ********** //

    /**
     * @param $itemId
     * @return DataSourceParsed
     */
    public function find($itemId)
    {
        return DataSourceParsed::find($itemId);
    }

    public function getAllByGame($gameId)
    {
        return DataSourceParsed::where('game_id', $gameId)->get();
    }

    public function getAllBySource($sourceId)
    {
        return DataSourceParsed::where('source_id', $sourceId)->orderBy('title', 'asc')->get();
    }

    public function getAllBySourceWithGameId($sourceId)
    {
        return DataSourceParsed::where('source_id', $sourceId)->whereNotNull('game_id')->orderBy('game_id', 'asc')->get();
    }

    public function getAllBySourceWithNoGameId($sourceId, $excludeLinkIdList = null, $excludeTitleList = null)
    {
        $dsItemList = DataSourceParsed::where('source_id', $sourceId)->whereNull('game_id');
        if ($excludeLinkIdList) {
            $dsItemList = $dsItemList->whereNotIn('link_id', $excludeLinkIdList);
        }
        if ($excludeTitleList) {
            $dsItemList = $dsItemList->whereNotIn('title', $excludeTitleList);
        }
        $dsItemList = $dsItemList->orderBy('title', 'asc')->get();
        return $dsItemList;
    }

    public function getBySourceNoGameIdWithEUDate($sourceId, $excludeLinkIdList = null, $excludeTitleList = null)
    {
        $dsItemList = DataSourceParsed
            ::where('source_id', $sourceId)
            ->whereNull('game_id')
            ->whereNotNull('release_date_eu');
        if ($excludeLinkIdList) {
            $dsItemList = $dsItemList->whereNotIn('link_id', $excludeLinkIdList);
        }
        if ($excludeTitleList) {
            $dsItemList = $dsItemList->whereNotIn('title', $excludeTitleList);
        }
        $dsItemList = $dsItemList->orderBy('title', 'asc')->get();
        return $dsItemList;
    }

    public function getBySourceNoGameIdNoEUDate($sourceId, $excludeLinkIdList = null, $excludeTitleList = null)
    {
        $dsItemList = DataSourceParsed
            ::where('source_id', $sourceId)
            ->whereNull('game_id')
            ->whereNull('release_date_eu');
        if ($excludeLinkIdList) {
            $dsItemList = $dsItemList->whereNotIn('link_id', $excludeLinkIdList);
        }
        if ($excludeTitleList) {
            $dsItemList = $dsItemList->whereNotIn('title', $excludeTitleList);
        }
        $dsItemList = $dsItemList->orderBy('title', 'asc')->get();
        return $dsItemList;
    }

    public function getBySourceAndGame($sourceId, $gameId)
    {
        return DataSourceParsed::where('source_id', $sourceId)->where('game_id', $gameId)->first();
    }

    public function getBySourceAndLinkId($sourceId, $linkId)
    {
        return DataSourceParsed::where('source_id', $sourceId)->where('link_id', $linkId)->first();
    }

    public function deleteBySourceId($sourceId)
    {
        DataSourceParsed::where('source_id', $sourceId)->delete();
    }

    public function removeSwitchEshopItems($gameId)
    {
        $sourceId = DataSource::DSID_SWITCH_ESHOP_UK;
        DataSourceParsed::where('source_id', $sourceId)->where('game_id', $gameId)->delete();
    }

    // ********** Nintendo.co.uk ********** //

    public function getAllNintendoCoUk()
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getAllBySource($sourceId);
    }

    public function getAllNintendoCoUkWithGameId()
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getAllBySourceWithGameId($sourceId);
    }

    public function getAllNintendoCoUkWithNoGameId($excludeLinkIdList = null)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getAllBySourceWithNoGameId($sourceId, $excludeLinkIdList);
    }

    public function getNintendoCoUkUnlinkedWithEUDate($excludeLinkIdList = null)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getBySourceNoGameIdWithEUDate($sourceId, $excludeLinkIdList);
    }

    public function getNintendoCoUkUnlinkedNoEUDate($excludeLinkIdList = null)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getBySourceNoGameIdNoEUDate($sourceId, $excludeLinkIdList);
    }

    public function getAllNintendoCoUkInLinkIdList($linkIdList)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;

        $dsItemList = DataSourceParsed::where('source_id', $sourceId)
            ->whereIn('link_id', $linkIdList)
            ->orderBy('id', 'desc')
            ->get();

        return $dsItemList;
    }

    public function getSourceNintendoCoUkForGame($gameId)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getBySourceAndGame($sourceId, $gameId);
    }

    public function getNintendoCoUkByLinkId($linkId)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        return $this->getBySourceAndLinkId($sourceId, $linkId);
    }

    public function clearGameIdFromNintendoCoUkItems($gameId)
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        $dsParsedItem = DataSourceParsed::where('source_id', $sourceId)->where('game_id', $gameId)->get();
        if ($dsParsedItem->count() > 0) {
            foreach ($dsParsedItem as $item) {
                $item->game_id = null;
                $item->save();
            }
        }
    }

    public function updateNintendoCoUkGameIds()
    {
        $sourceId = DataSource::DSID_NINTENDO_CO_UK;
        DB::update('
            UPDATE data_source_parsed dsp, games g
            SET dsp.game_id = g.id
            WHERE dsp.link_id = g.eshop_europe_fs_id
            AND dsp.source_id = ?
        ', [$sourceId]);
    }

    // ********** Wikipedia ********** //

    public function getAllWikipedia()
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getAllBySource($sourceId);
    }

    public function getAllWikipediaWithGameId()
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getAllBySourceWithGameId($sourceId);
    }

    public function getAllWikipediaWithNoGameId($excludeTitleList = null)
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getAllBySourceWithNoGameId($sourceId, null, $excludeTitleList);
    }

    public function getWikipediaNoGameIdWithEUDate($excludeTitleList = null)
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getBySourceNoGameIdWithEUDate($sourceId, null, $excludeTitleList);
    }

    public function getWikipediaNoGameIdNoEUDate($excludeTitleList = null)
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getBySourceNoGameIdNoEUDate($sourceId, null, $excludeTitleList);
    }

    public function getAllWikipediaInTitleList($titleList)
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;

        $dsItemList = DataSourceParsed::where('source_id', $sourceId)
            ->whereIn('title', $titleList)
            ->orderBy('id', 'desc')
            ->get();

        return $dsItemList;
    }

    public function getSourceWikipediaForGame($gameId)
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getBySourceAndGame($sourceId, $gameId);
    }

    public function getWikipediaByLinkId($linkId)
    {
        $sourceId = DataSource::DSID_WIKIPEDIA;
        return $this->getBySourceAndLinkId($sourceId, $linkId);
    }

    // ********* NINTENDO.CO.UK API - Games on sale ************** //

    /**
     * Gets the highest available discounts.
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getGamesOnSaleHighestDiscounts($limit = 50)
    {
        $games = DB::table('games')
            ->join('data_source_parsed', 'data_source_parsed.game_id', '=', 'games.id')
            ->leftJoin('categories', 'games.category_id', '=', 'categories.id')
            ->select('games.*',
                'data_source_parsed.price_standard',
                'data_source_parsed.price_discounted',
                'data_source_parsed.price_discount_pc',
                'categories.name AS category_name')
            ->where('data_source_parsed.source_id', '=', DataSource::DSID_NINTENDO_CO_UK)
            ->whereNotNull('games.game_rank')
            ->whereNotNull('data_source_parsed.price_discounted')
            ->where('data_source_parsed.price_discount_pc', '>=', '50')
            ->orderBy('games.game_rank', 'asc')
            ->orderBy('data_source_parsed.price_discount_pc', 'desc');
        if ($limit) {
            $games = $games->limit($limit);
        }
        $games = $games->get();

        return $games;
    }

    /**
     * Gets good discounts for green rated games
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getGamesOnSaleGoodRanks($limit = 50)
    {
        $games = DB::table('games')
            ->join('data_source_parsed', 'data_source_parsed.game_id', '=', 'games.id')
            ->leftJoin('categories', 'games.category_id', '=', 'categories.id')
            ->select('games.*',
                'data_source_parsed.price_standard',
                'data_source_parsed.price_discounted',
                'data_source_parsed.price_discount_pc',
                'categories.name AS category_name')
            ->where('data_source_parsed.source_id', '=', DataSource::DSID_NINTENDO_CO_UK)
            ->whereNotNull('games.game_rank')
            ->where('games.rating_avg', '>', '7.9')
            ->whereNotNull('data_source_parsed.price_discounted')
            ->orderBy('games.rating_avg', 'desc');
        if ($limit) {
            $games = $games->limit($limit);
        }
        $games = $games->get();

        return $games;
    }

    /**
     * Gets unranked games that are on sale
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getGamesOnSaleUnranked($limit = 50)
    {
        $games = DB::table('games')
            ->join('data_source_parsed', 'data_source_parsed.game_id', '=', 'games.id')
            ->leftJoin('categories', 'games.category_id', '=', 'categories.id')
            ->select('games.*',
                'data_source_parsed.price_standard',
                'data_source_parsed.price_discounted',
                'data_source_parsed.price_discount_pc',
                'categories.name AS category_name')
            ->where('data_source_parsed.source_id', '=', DataSource::DSID_NINTENDO_CO_UK)
            ->whereNull('games.game_rank')
            ->whereNotNull('data_source_parsed.price_discounted')
            ->orderBy('data_source_parsed.price_discount_pc', 'desc');
        if ($limit) {
            $games = $games->limit($limit);
        }
        $games = $games->get();

        return $games;
    }

}