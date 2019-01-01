<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as Controller;

use App\Services\ServiceContainer;

class SitemapController extends Controller
{
    public function getTimestampNow()
    {
        $now = new \DateTime('now');
        $timestamp = $now->format('c');
        return $timestamp;
    }

    public function show()
    {
        $bindings = [];
        $timestamp = $this->getTimestampNow();
        $bindings['TimestampNow'] = $timestamp;

        return response()->view('sitemap.index', $bindings)->header('Content-Type', 'text/xml');
    }

    public function site()
    {
        $bindings = [];
        $timestamp = $this->getTimestampNow();
        $bindings['TimestampNow'] = $timestamp;

        $sitemapPages = [];
        $sitemapPages[] = array('url' => route('welcome'), 'lastmod' => $timestamp, 'changefreq' => 'daily', 'priority' => '1.0');
        $sitemapPages[] = array('url' => route('games.landing'), 'lastmod' => $timestamp, 'changefreq' => 'daily', 'priority' => '0.9');
        $sitemapPages[] = array('url' => route('games.onSale'), 'lastmod' => $timestamp, 'changefreq' => 'daily', 'priority' => '0.9');
        $sitemapPages[] = array('url' => route('games.list.released'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        $sitemapPages[] = array('url' => route('games.list.upcoming'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        $sitemapPages[] = array('url' => route('news.landing'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');

        $bindings['SitemapPages'] = $sitemapPages;

        return response()->view('sitemap.standard', $bindings)->header('Content-Type', 'text/xml');
    }

    public function games()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $gameService = $serviceContainer->getGameService();

        $xmlFilePath = storage_path().'/app/public/sitemaps/sitemap-games.xml';

        if (file_exists($xmlFilePath)) {

            return response()->file($xmlFilePath, ['Content-Type', 'text/xml']);

        } else {

            $bindings = [];
            $timestamp = $this->getTimestampNow();
            $bindings['TimestampNow'] = $timestamp;

            $bindings['GameList'] = $gameService->getAll('eu');

            return response()->view('sitemap.games', $bindings)->header('Content-Type', 'text/xml');

        }
    }

    public function calendar()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $serviceCalendar = $serviceContainer->getGameCalendarService();

        $xmlFilePath = storage_path().'/app/public/sitemaps/sitemap-calendar.xml';

        if (file_exists($xmlFilePath)) {

            return response()->file($xmlFilePath, ['Content-Type', 'text/xml']);

        } else {

            $bindings = [];
            $timestamp = $this->getTimestampNow();
            $bindings['TimestampNow'] = $timestamp;

            $sitemapPages = [];

            $sitemapPages[] = array(
                'url' => route('games.calendar.landing'),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );

            $dateList = $serviceCalendar->getAllowedDates();

            foreach ($dateList as $dateListItem) {

                $sitemapPages[] = array(
                    'url' => route('games.calendar.page', ['date' => $dateListItem]),
                    'lastmod' => $timestamp,
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                );

            }

            $bindings['SitemapPages'] = $sitemapPages;

            return response()->view('sitemap.standard', $bindings)->header('Content-Type', 'text/xml');

        }
    }

    public function topRated()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $serviceCalendar = $serviceContainer->getGameCalendarService();

        $xmlFilePath = storage_path().'/app/public/sitemaps/sitemap-top-rated.xml';

        if (file_exists($xmlFilePath)) {

            return response()->file($xmlFilePath, ['Content-Type', 'text/xml']);

        } else {

            $bindings = [];

            $now = new \DateTime('now');
            $timestamp = $now->format('c');
            $bindings['TimestampNow'] = $timestamp;

            $sitemapPages = [];

            $sitemapPages[] = array(
                'url' => route('topRated.landing'),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );

            $sitemapPages[] = array(
                'url' => route('topRated.allTime'),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );

            $sitemapPages[] = array(
                'url' => route('topRated.byYear', ['year' => '2017']),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );

            $sitemapPages[] = array(
                'url' => route('topRated.byYear', ['year' => '2018']),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );

            $sitemapPages[] = array(
                'url' => route('topRated.byMonthLanding'),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );

            $dateList = $serviceCalendar->getAllowedDates();

            foreach ($dateList as $dateListItem) {

                $sitemapPages[] = array(
                    'url' => route('topRated.byMonthPage', ['date' => $dateListItem]),
                    'lastmod' => $timestamp,
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                );

            }

            $bindings['SitemapPages'] = $sitemapPages;

            return response()->view('sitemap.standard', $bindings)->header('Content-Type', 'text/xml');

        }

    }

    public function reviews()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $serviceReviewSite = $serviceContainer->getReviewSiteService();

        $bindings = [];
        $timestamp = $this->getTimestampNow();
        $bindings['TimestampNow'] = $timestamp;

        $sitemapPages = array();
        $sitemapPages[] = array(
            'url' => route('reviews.landing'),
            'lastmod' => $timestamp,
            'changefreq' => 'weekly',
            'priority' => '0.8'
        );

        $reviewSiteList = $serviceReviewSite->getActive();
        foreach ($reviewSiteList as $reviewSite) {
            $sitemapPages[] = array(
                'url' => route('reviews.site', ['linkTitle' => $reviewSite->link_title]),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );
        }

        $bindings['SitemapPages'] = $sitemapPages;

        return response()->view('sitemap.standard', $bindings)->header('Content-Type', 'text/xml');
    }

    public function genres()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $serviceGenre = $serviceContainer->getGenreService();

        $bindings = [];
        $timestamp = $this->getTimestampNow();
        $bindings['TimestampNow'] = $timestamp;

        $sitemapPages = array();
        $sitemapPages[] = array(
            'url' => route('games.genres.landing'),
            'lastmod' => $timestamp,
            'changefreq' => 'weekly',
            'priority' => '0.8'
        );

        $genreList = $serviceGenre->getAll();
        foreach ($genreList as $genre) {
            $sitemapPages[] = array(
                'url' => route('games.genres.item', ['linkTitle' => $genre->link_title]),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );
        }

        $bindings['SitemapPages'] = $sitemapPages;

        return response()->view('sitemap.standard', $bindings)->header('Content-Type', 'text/xml');
    }

    public function tags()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $serviceTag = $serviceContainer->getTagService();

        $bindings = [];
        $timestamp = $this->getTimestampNow();
        $bindings['TimestampNow'] = $timestamp;

        $sitemapPages = array();
        $sitemapPages[] = array(
            'url' => route('tags.landing'),
            'lastmod' => $timestamp,
            'changefreq' => 'weekly',
            'priority' => '0.8'
        );

        $tagList = $serviceTag->getAll();
        foreach ($tagList as $tag) {
            $sitemapPages[] = array(
                'url' => route('tags.page', ['linkTitle' => $tag->link_title]),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );
        }

        $bindings['SitemapPages'] = $sitemapPages;

        return response()->view('sitemap.standard', $bindings)->header('Content-Type', 'text/xml');
    }

    public function news()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $newsService = $serviceContainer->getNewsService();

        $xmlFilePath = storage_path().'/app/public/sitemaps/sitemap-news.xml';

        if (file_exists($xmlFilePath)) {

            return response()->file($xmlFilePath, ['Content-Type', 'text/xml']);

        } else {

            $bindings = [];
            $timestamp = $this->getTimestampNow();
            $bindings['TimestampNow'] = $timestamp;

            $bindings['NewsList'] = $newsList = $newsService->getAll();

            return response()->view('sitemap.news', $bindings)->header('Content-Type', 'text/xml');

        }
    }
}
