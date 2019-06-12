<?php

namespace App\Http\Controllers\Admin;

use Auth;

use Illuminate\Routing\Controller as Controller;
use App\Services\ServiceContainer;

use App\Factories\GameDirectorFactory;
use App\Factories\GameChangeHistoryFactory;

use App\Construction\GameReleaseDate\Director as GameReleaseDateDirector;
use App\Construction\GameReleaseDate\Builder as GameReleaseDateBuilder;

use App\Services\UrlService;

use App\Events\GameCreated;

class FeedItemEshopEuropeController extends Controller
{
    public function showList($report = null)
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $bindings = [];

        $serviceEshopGame = $serviceContainer->getEshopEuropeGameService();

        $jsInitialSort = "[ 3, 'desc']";
        if ($report == null) {
            $bindings['ActiveNav'] = 'all';
            $feedItems = $serviceEshopGame->getAll();
        } else {
            $bindings['ActiveNav'] = $report;
            switch ($report) {
                case 'with-link':
                    $feedItems = $serviceEshopGame->getAllWithLink();
                    break;
                case 'no-link':
                    $feedItems = $serviceEshopGame->getAllWithoutLink();
                    break;
                default:
                    abort(404);
                    break;
            }
        }

        $bindings['TopTitle'] = 'Admin - Feed items - eShop: Europe';
        $bindings['PageTitle'] = 'Feed items - eShop: Europe';
        $bindings['FeedItems'] = $feedItems;
        $bindings['jsInitialSort'] = $jsInitialSort;

        return view('admin.feed-items.eshop.europe.list', $bindings);
    }

    public function view($itemId)
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $bindings = [];

        $serviceEshopGame = $serviceContainer->getEshopEuropeGameService();
        $gameData = $serviceEshopGame->getByFsId($itemId);
        if (!$gameData) abort(404);

        $bindings['GameData'] = $gameData->toArray();

        $bindings['TopTitle'] = 'Admin - Feed items - eShop: Europe - '.$gameData->title;
        $bindings['PageTitle'] = $gameData->title.' - Feed items - eShop: Europe';

        return view('admin.feed-items.eshop.europe.view', $bindings);
    }

    public function addGame($itemId)
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $bindings = [];
        $customErrors = [];

        $serviceEshopGame = $serviceContainer->getEshopEuropeGameService();
        $serviceGame = $serviceContainer->getGameService();
        $serviceGameTitleHash = $serviceContainer->getGameTitleHashService();
        $serviceUrl = new UrlService();

        $eshopGameData = $serviceEshopGame->getByFsId($itemId);
        if (!$eshopGameData) abort(404);

        $bindings['EshopGameData'] = $eshopGameData->toArray();

        $bindings['TopTitle'] = 'Add game from eShop Europe feed';
        $bindings['PageTitle'] = 'Add game from eShop Europe feed';

        $bindings['FsId'] = $itemId;

        $request = request();
        $okToProceed = true;

        if ($request->isMethod('post')) {

            // Check title hash is unique
            $titleHash = $serviceGameTitleHash->generateHash($request->title);
            $existingTitleHash = $serviceGameTitleHash->getByHash($titleHash);

            // Check for duplicates
            if ($existingTitleHash != null) {
                $customErrors[] = 'Title already exists for another record!';
                $okToProceed = false;
            }

            if ($okToProceed) {

                // Generate usable game data
                $title = $eshopGameData->title;
                $linkText = $serviceUrl->generateLinkText($title);

                $gameData = [
                    'title' => $title,
                    'link_title' => $linkText,
                    'eshop_europe_fs_id' => $eshopGameData->fs_id,
                ];

                // Save details
                $game = GameDirectorFactory::createNew($gameData);
                $gameId = $game->id;

                // Update release dates
                $gameReleaseDateDirector = new GameReleaseDateDirector();

                $regionsToUpdate = $gameReleaseDateDirector->getRegionList();

                foreach ($regionsToUpdate as $region) {

                    $gameReleaseDateBuilder = new GameReleaseDateBuilder();
                    $gameReleaseDateDirector->setBuilder($gameReleaseDateBuilder);
                    $gameReleaseDateDirector->buildNewReleaseDate($region, $gameId, []);
                    $gameReleaseDate = $gameReleaseDateBuilder->getGameReleaseDate();
                    $gameReleaseDate->save();

                }

                // Game change history
                GameChangeHistoryFactory::makeHistory($game, null, Auth::user()->id, 'games');

                // Trigger event
                event(new GameCreated($game));

                return redirect('/admin/games/detail/'.$gameId.'?lastaction=add&lastgameid='.$gameId);

            }

        }

        return view('admin.feed-items.eshop.europe.add-game', $bindings);
    }
}
