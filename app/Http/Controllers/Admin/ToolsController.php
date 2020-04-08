<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as Controller;

class ToolsController extends Controller
{
    protected $commandList = [];

    public function __construct()
    {
        $this->commandList = [
            /* *** Wikipedia *** */
            'WikipediaCrawlGamesList' => [
                'command' => 'WikipediaCrawlGamesList',
                'group' => 'Wikipedia',
                'title' => 'Wikipedia Crawl Games List',
                'desc' => 'Crawls the Nintendo Switch games list on Wikipedia and saves data to the WOS database',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0310',
                'nextStep' => 'WikipediaImportGamesList',
            ],
            'WikipediaImportGamesList' => [
                'command' => 'WikipediaImportGamesList',
                'group' => 'Wikipedia',
                'title' => 'Wikipedia Import Games List',
                'desc' => 'Converts crawler data into Wiki Updates for creating or updating games',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0315',
                'nextStep' => 'WikipediaUpdateGamesList',
                'relatedLink' => [
                    'url' => route('staff.wikipedia.wiki-updates.list-all-pending'),
                    'text' => 'Wiki updates'
                ],
            ],
            'WikipediaUpdateGamesList' => [
                'command' => 'WikipediaUpdateGamesList',
                'group' => 'Wikipedia',
                'title' => 'Wikipedia Update Games List',
                'desc' => 'Processes Wiki Updates that are marked as OK to update',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0320',
                'relatedLink' => [
                    'url' => route('staff.wikipedia.wiki-updates.list-all-pending'),
                    'text' => 'Wiki Updates'
                ],
            ],
            /* *** Reviews *** */
            'RunFeedImporter' => [
                'command' => 'RunFeedImporter',
                'group' => 'Reviews',
                'title' => 'Run Feed Importer',
                'desc' => 'Visits RSS feeds from partner sites, and loads any reviews that have not yet been imported',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0500',
                'nextStep' => 'RunFeedParser',
            ],
            'RunFeedParser' => [
                'command' => 'RunFeedParser',
                'group' => 'Reviews',
                'title' => 'Run Feed Parser',
                'desc' => 'Attempts to match titles from the feed importer to games in the WOS database',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0505',
                'nextStep' => 'RunFeedReviewGenerator',
                'relatedLink' => [
                    'url' => route('staff.reviews.feed-items.list'),
                    'text' => 'Feed Items - Reviews'
                ],
            ],
            'RunFeedReviewGenerator' => [
                'command' => 'RunFeedReviewGenerator',
                'group' => 'Reviews',
                'title' => 'Run Feed Review Generator',
                'desc' => 'Creates review links for feed items linked to games and with ratings',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0510',
                'nextStep' => 'UpdateGameRanks',
            ],
            'UpdateGameRanks' => [
                'command' => 'UpdateGameRanks',
                'group' => 'Game updates',
                'title' => 'Update Game Ranks',
                'desc' => 'Updates the rank field for each game',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0520',
            ],
            /* *** Games *** */
            'UpdateGameReviewStats' => [
                'command' => 'UpdateGameReviewStats',
                'group' => 'Game updates',
                'title' => 'Update Game Review Stats',
                'desc' => 'Updates the Review Count and Average Score fields for each game',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0535',
            ],
            'UpdateGameCalendarStats' => [
                'command' => 'UpdateGameCalendarStats',
                'group' => 'Game updates',
                'title' => 'Update Game Calendar Stats',
                'desc' => 'Updates the released game stats on the Release Calendar page',
                'scheduleFreq' => 'Daily',
                'scheduleTime' => '0540',
            ],
        ];

        //parent::__construct();
    }

    private function getToolDetails($commandName)
    {
        if (!array_key_exists($commandName, $this->commandList)) abort(404);

        return $this->commandList[$commandName];
    }

    public function landing()
    {
        $bindings = [];

        $bindings['TopTitle'] = 'Tools';
        $bindings['PageTitle'] = 'Tools';

        $bindings['ToolList'] = $this->commandList;

        return view('admin.tools.landing', $bindings);
    }

    public function toolLandingModular($commandName)
    {
        $bindings = [];

        $toolDetails = $this->getToolDetails($commandName);

        $toolTitle = $toolDetails['title'];

        $bindings['TopTitle'] = 'Tools - '.$toolTitle;
        $bindings['PageTitle'] = $toolTitle;
        $bindings['ToolDetails'] = $toolDetails;

        return view('admin.tools.toolLandingModular', $bindings);
    }

    public function toolProcessModular($commandName)
    {
        $toolDetails = $this->getToolDetails($commandName);

        \Artisan::call($commandName, []);
        $commandOutput = \Artisan::output();

        if ($commandName == 'RunFeedReviewGenerator') {
            // Also run the PartnerUpdateFields command
            \Artisan::call('PartnerUpdateFields', []);
        }

        $bindings = [];

        $toolTitle = $toolDetails['title'];

        $bindings['TopTitle'] = 'Tools - '.$toolTitle;
        $bindings['PageTitle'] = $toolTitle;
        $bindings['ToolDetails'] = $toolDetails;
        $bindings['CommandOutput'] = $commandOutput;

        return view('admin.tools.toolProcessModular', $bindings);
    }

}
