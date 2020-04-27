<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

// Front page
Route::get('/', 'WelcomeController@show')->name('welcome');

// Third-party logins
Route::get('login/twitter', 'Auth\LoginController@redirectToProviderTwitter')->name('auth.login.twitter');
Route::get('login/twitter/callback', 'Auth\LoginController@handleProviderCallbackTwitter')->name('auth.login.twitter.callback');

// Static content
Route::get('/about', 'AboutController@show')->name('about');
Route::get('/privacy', 'PrivacyController@show')->name('privacy');

// Main game pages
Route::get('/games', 'GamesController@landing')->name('games.landing');
Route::get('/games/recent', 'GamesController@recentReleases')->name('games.recentReleases');
Route::get('/games/upcoming', 'GamesController@upcomingReleases')->name('games.upcomingReleases');
//Route::get('/games/upcoming', 'GamesController@listUpcoming')->name('games.list.upcoming');

Route::get('/games/on-sale', 'GamesController@gamesOnSale')->name('games.onSale');

// Browse by...
Route::get('/games/by-title', 'GamesBrowseController@byTitleLanding')->name('games.browse.byTitle.landing');
Route::get('/games/by-title/{letter}', 'GamesBrowseController@byTitlePage')->name('games.browse.byTitle.page');

Route::get('/games/by-type', 'GamesBrowseController@byPrimaryTypeLanding')->name('games.browse.byPrimaryType.landing');
Route::get('/games/by-type/{primaryType}', 'GamesBrowseController@byPrimaryTypePage')->name('games.browse.byPrimaryType.page');

Route::get('/games/by-series', 'GamesBrowseController@bySeriesLanding')->name('games.browse.bySeries.landing');
Route::get('/games/by-series/{series}', 'GamesBrowseController@bySeriesPage')->name('games.browse.bySeries.page');

Route::get('/games/by-tag', 'GamesBrowseController@byTagLanding')->name('games.browse.byTag.landing');
Route::get('/games/by-tag/{tag}', 'GamesBrowseController@byTagPage')->name('games.browse.byTag.page');

Route::get('/games/by-date', 'GamesBrowseController@byDateLanding')->name('games.browse.byDate.landing');
Route::get('/games/by-date/{date}', 'GamesBrowseController@byDatePage')->name('games.browse.byDate.page');

// These must be after the game redirects
Route::get('/games/{id}', 'GamesController@showId')->name('game.showId');
Route::get('/games/{id}/{title}', 'GamesController@show')->name('game.show');

/* Top Rated */
Route::get('/top-rated', 'TopRatedController@landing')->name('topRated.landing');
Route::get('/top-rated/all-time', 'TopRatedController@allTime')->name('topRated.allTime');
Route::get('/top-rated/by-year/{year}', 'TopRatedController@byYear')->name('topRated.byYear');
Route::get('/top-rated/multiplayer', 'TopRatedController@multiplayer')->name('topRated.multiplayer');

/* Reviews */
//Route::get('/reviews', 'ReviewsController@landing')->name('reviews.landing');
Route::get('/reviews/site/{linkTitle}', 'ReviewsController@reviewSite')->name('reviews.site');
Route::get('/reviews/{year?}', 'ReviewsController@landingByYear')->name('reviews.landing');

/* Partners */
Route::get('/partners', 'PartnersController@landing')->name('partners.landing');
Route::get('/partners/review-sites', 'PartnersController@reviewSites')->name('partners.review-sites');
Route::get('/partners/developers-publishers', 'PartnersController@developersPublishers')->name('partners.developers-publishers');

Route::get('/partners/games-company/{linkTitle}', 'PartnersController@showGamesCompany')->name('partners.detail.games-company');

/* News */
Route::get('/news', 'NewsController@landing')->name('news.landing');
Route::get('/news/category/{linkName}', 'NewsController@categoryLanding')->name('news.category.landing');
Route::get('/news/{date}/{title}', 'NewsController@displayContent')->name('news.content');

// Sitemaps
Route::get('/sitemap', 'SitemapController@show')->name('sitemap.index');
Route::get('/sitemap/site', 'SitemapController@site')->name('sitemap.site');
Route::get('/sitemap/games', 'SitemapController@games')->name('sitemap.games');
Route::get('/sitemap/calendar', 'SitemapController@calendar')->name('sitemap.calendar');
Route::get('/sitemap/top-rated', 'SitemapController@topRated')->name('sitemap.topRated');
Route::get('/sitemap/reviews', 'SitemapController@reviews')->name('sitemap.reviews');
Route::get('/sitemap/tags', 'SitemapController@tags')->name('sitemap.tags');
Route::get('/sitemap/news', 'SitemapController@news')->name('sitemap.news');

/* Logged in */
Route::group(['middleware' => ['auth']], function() {

    // Index
    Route::get('/user', 'User\IndexController@show')->name('user.index');

    // Settings
    Route::get('/user/settings', 'User\SettingsController@show')->name('user.settings');

    // Collection
    Route::get('/user/collection/index', 'User\CollectionController@landing')->name('user.collection.landing');
    Route::get('/user/collection/add', 'User\CollectionController@add')->name('user.collection.add');
    Route::post('/user/collection/add', 'User\CollectionController@add')->name('user.collection.add');
    Route::get('/user/collection/edit/{itemId}', 'User\CollectionController@edit')->name('user.collection.edit');
    Route::post('/user/collection/edit/{itemId}', 'User\CollectionController@edit')->name('user.collection.edit');
    Route::get('/user/collection/delete', 'User\CollectionController@delete')->name('user.collection.delete');

    // User profile
    Route::get('/user/region/update', 'User\UserProfileController@updateRegion')->name('user.profile.updateRegion');

    // Quick reviews
    Route::get('/user/quick-reviews/add', 'User\QuickReviewController@add')->name('user.quick-reviews.add');
    Route::post('/user/quick-reviews/add', 'User\QuickReviewController@add')->name('user.quick-reviews.add');
    Route::get('/user/quick-reviews/{report?}', 'User\QuickReviewController@showList')->name('user.quick-reviews.list');

    // Partner reviews
    Route::get('/user/partner-reviews/add', 'User\PartnerReviewController@add')->name('user.partner-reviews.add');
    Route::post('/user/partner-reviews/add', 'User\PartnerReviewController@add')->name('user.partner-reviews.add');
    Route::get('/user/partner-reviews/{report?}', 'User\PartnerReviewController@showList')->name('user.partner-reviews.list');

    // Review partners: Unranked games
    Route::get('/user/review-partner/unranked', 'User\ReviewPartnerUnrankedController@landing')->name('user.review-partner.unranked.landing');
    Route::get('/user/review-partner/unranked/{mode}/{filter}', 'User\ReviewPartnerUnrankedController@showList')->name('user.review-partner.unranked.list');

    // Review partners: Review links
    Route::get('/user/review-link/{report?}', 'User\ReviewLinkController@landing')->name('user.review-link.landing');

    // Review partners: Games list
    Route::get('/user/games-list/{report}', 'User\GamesListController@landing')->name('user.games-list.landing');

});



// *************** Staff: General pages *************** //
Route::group(['middleware' => ['auth.staff']], function() {

    Route::get('/staff', 'Staff\IndexController@index')->name('staff.index');

});


// *************** Staff: GAMES *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_GAMES_MANAGER]], function() {

    Route::get('/staff/games/dashboard', 'Staff\Games\DashboardController@show')->name('staff.games.dashboard');

    // Find a game
    Route::match(['get', 'post'], '/staff/games/find', 'Staff\Games\FindController@show')->name('staff.games.find');

    // Games: Detail
    Route::get('/staff/games/detail/{gameId}', 'Staff\Games\GamesDetailController@show')->name('staff.games.detail');
    Route::get('/staff/games/detail/full-audit/{game}', 'Staff\Games\GamesDetailController@showFullAudit')->name('staff.games.detail.fullAudit');
    Route::get('/staff/games/detail/{gameId}/update-eshop-data', 'Staff\Games\GamesDetailController@updateEshopData')->name('staff.games.detail.updateEshopData');
    Route::get('/staff/games/detail/{gameId}/redownload-packshots', 'Staff\Games\GamesDetailController@redownloadPackshots')->name('staff.games.detail.redownloadPackshots');

    // Games: Add, edit, delete
    Route::match(['get', 'post'], '/staff/games/add', 'Staff\Games\GamesEditorController@add')->name('staff.games.add');
    Route::match(['get', 'post'], '/staff/games/edit/{gameId}', 'Staff\Games\GamesEditorController@edit')->name('staff.games.edit');
    Route::match(['get', 'post'], '/staff/games/edit-nintendo-co-uk/{gameId}', 'Staff\Games\GamesEditorController@editNintendoCoUk')->name('staff.games.editNintendoCoUk');
    Route::match(['get', 'post'], '/staff/games/delete/{gameId}', 'Staff\Games\GamesEditorController@delete')->name('staff.games.delete');
    Route::get('/staff/games/release', 'Staff\Games\GamesEditorController@releaseGame')->name('staff.games.release');

    // Game import rules
    Route::match(['get', 'post'], '/staff/games/{gameId}/import-rule-eshop/edit', 'Staff\Games\ImportRuleEshopController@edit')->name('staff.games.import-rule-eshop.edit');
    Route::match(['get', 'post'], '/staff/games/{gameId}/import-rule-wikipedia/edit', 'Staff\Games\ImportRuleWikipediaController@edit')->name('staff.games.import-rule-wikipedia.edit');

    // Game lists
    Route::get('/staff/games/list/recently-added', 'Staff\Games\GamesListController@recentlyAdded')->name('staff.games.list.recently-added');
    Route::get('/staff/games/list/recently-released', 'Staff\Games\GamesListController@recentlyReleased')->name('staff.games.list.recently-released');
    Route::get('/staff/games/list/upcoming-games', 'Staff\Games\GamesListController@upcomingGames')->name('staff.games.list.upcoming-games');
    Route::get('/staff/games/list/no-nintendo-co-uk-link', 'Staff\Games\GamesListController@noNintendoCoUkLink')->name('staff.games.list.no-nintendo-co-uk-link');
    Route::get('/staff/games/list/by-primary-type/{primaryType}', 'Staff\Games\GamesListController@byPrimaryType')->name('staff.games.list.by-primary-type');
    Route::get('/staff/games/list/by-series/{gameSeries}', 'Staff\Games\GamesListController@bySeries')->name('staff.games.list.by-series');

});


// *************** Staff: REVIEWS *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_REVIEWS_MANAGER]], function() {

    Route::get('/staff/reviews/dashboard', 'Staff\Reviews\DashboardController@show')->name('staff.reviews.dashboard');

    // Quick reviews
    Route::match(['get', 'post'], '/staff/reviews/quick-reviews/edit/{reviewId}', 'Staff\Reviews\QuickReviewController@edit')->name('staff.reviews.quick-reviews.edit');
    Route::get('/staff/reviews/quick-reviews/{report?}', 'Staff\Reviews\QuickReviewController@showList')->name('staff.reviews.quick-reviews.list');

    // Review feed items
    Route::get('/staff/reviews/feed-items/{report?}', 'Staff\Reviews\FeedItemsController@showList')->name('staff.reviews.feed-items.list');
    Route::get('/staff/reviews/feed-items/by-process-status/{status}', 'Staff\Reviews\FeedItemsController@byProcessStatus')->name('staff.reviews.feed-items.by-process-status');
    Route::match(['get', 'post'], '/staff/reviews/feed-items/edit/{linkId}', 'Staff\Reviews\FeedItemsController@edit')->name('staff.reviews.feed-items.edit');

    // Review feed imports
    Route::get('/staff/reviews/feed-imports', 'Staff\Reviews\FeedImportsController@show')->name('staff.reviews.feed-imports.list');
    Route::get('/staff/reviews/feed-imports/{feedImport}/items', 'Staff\Reviews\FeedImportsController@showItemList')->name('staff.reviews.feed-imports.item-list');

});


// *************** Staff: CATEGORISATION *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_CATEGORY_MANAGER]], function() {

    Route::get('/staff/categorisation/dashboard', 'Staff\Categorisation\DashboardController@show')->name('staff.categorisation.dashboard');

    // Primary types
    Route::get('/staff/categorisation/game-primary-types/list', 'Staff\Categorisation\GamePrimaryTypesController@showList')->name('staff.categorisation.game-primary-types.list');
    Route::get('/staff/categorisation/game-primary-types/add', 'Staff\Categorisation\GamePrimaryTypesController@addPrimaryType')->name('staff.categorisation.game-primary-types.add');

    // Series
    Route::get('/staff/categorisation/game-series/list', 'Staff\Categorisation\GameSeriesController@showList')->name('staff.categorisation.game-series.list');
    Route::get('/staff/categorisation/game-series/add', 'Staff\Categorisation\GameSeriesController@addGameSeries')->name('staff.categorisation.game-series.add');

    // Genres
    Route::get('/staff/categorisation/genre/list', 'Staff\Categorisation\GenreController@showList')->name('staff.categorisation.genre.list');

    // Tags
    Route::get('/staff/categorisation/tag/list', 'Staff\Categorisation\TagController@showList')->name('staff.categorisation.tag.list');
    Route::get('/staff/categorisation/tag/add', 'Staff\Categorisation\TagController@addTag')->name('staff.categorisation.tag.add');
    Route::match(['get', 'post'], '/staff/categorisation/tag/edit/{tagId}', 'Staff\Categorisation\TagController@editTag')->name('staff.categorisation.tag.edit');
    Route::get('/staff/categorisation/tag/delete/{tagId}', 'Staff\Categorisation\TagController@deleteTag')->name('staff.categorisation.tag.delete');
    Route::get('/staff/categorisation/tag/game/{gameId}/list', 'Staff\Categorisation\TagController@showGameTagList')->name('staff.categorisation.tag.game.list');
    Route::get('/staff/categorisation/tag/game/{gameId}/add', 'Staff\Categorisation\TagController@addGameTag')->name('staff.categorisation.tag.game.add');
    Route::get('/staff/categorisation/tag/game/{gameId}/remove', 'Staff\Categorisation\TagController@removeGameTag')->name('staff.categorisation.tag.game.remove');

});

// *************** Staff: NEWS *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_NEWS_MANAGER]], function() {

    Route::get('/staff/news/dashboard', 'Staff\News\DashboardController@show')->name('staff.news.dashboard');

    // List
    Route::get('/staff/news/list', 'Staff\News\ListController@show')->name('staff.news.list');

    // Editor
    Route::get('/staff/news/add', 'Staff\News\EditorController@add')->name('staff.news.add');
    Route::post('/staff/news/add', 'Staff\News\EditorController@add')->name('staff.news.add');
    Route::match(['get', 'post'], '/staff/news/edit/{newsId}', 'Staff\News\EditorController@edit')->name('staff.news.edit');

    // Categories
    Route::get('/staff/news/category/list', 'Staff\News\CategoryController@showList')->name('staff.news.category.list');
    Route::match(['get', 'post'], '/staff/news/category/add', 'Staff\News\CategoryController@add')->name('staff.news.category.add');
    Route::match(['get', 'post'], '/staff/news/category/edit/{newsCategoryId}', 'Staff\News\CategoryController@edit')->name('staff.news.category.edit');

});


// *************** Staff: PARTNERS *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_PARTNERSHIPS_MANAGER]], function() {

    Route::get('/staff/partners/dashboard', 'Staff\Partners\DashboardController@show')->name('staff.partners.dashboard');

    // Partners: Review sites
    Route::get('/staff/partners/review-site', 'Staff\Partners\ReviewSiteController@showList')->name('staff.reviews.site.list');
    Route::match(['get', 'post'], '/staff/partners/review-site/add', 'Staff\Partners\ReviewSiteController@add')->name('staff.reviews.site.add');
    Route::match(['get', 'post'], '/staff/partners/review-site/edit/{siteId}', 'Staff\Partners\ReviewSiteController@edit')->name('staff.reviews.site.edit');

    // Partners: Games companies
    Route::get('/staff/partners/games-company/list', 'Staff\Partners\GamesCompanyController@showList')->name('staff.partners.games-company.list');
    Route::match(['get', 'post'], '/staff/partners/games-company/add', 'Staff\Partners\GamesCompanyController@add')->name('staff.partners.games-company.add');
    Route::match(['get', 'post'], '/staff/partners/games-company/edit/{partnerId}', 'Staff\Partners\GamesCompanyController@edit')->name('staff.partners.games-company.edit');
    Route::match(['get', 'post'], '/staff/partners/games-company/delete/{partnerId}', 'Staff\Partners\GamesCompanyController@delete')->name('staff.partners.games-company.delete');

    Route::get('/staff/partners/games-company/show/{partner}', 'Staff\Partners\GamesCompanyController@show')->name('staff.partners.games-company.show');

    Route::get('/staff/partners/games-company/devs-with-unranked-games', 'Staff\Partners\GamesCompanyController@devsWithUnrankedGames')->name('staff.partners.games-company.devs-with-unranked-games');
    Route::get('/staff/partners/games-company/pubs-with-unranked-games', 'Staff\Partners\GamesCompanyController@pubsWithUnrankedGames')->name('staff.partners.games-company.pubs-with-unranked-games');

    // Partners: Outreach
    Route::get('/staff/partners/outreach/list/{partner?}', 'Staff\Partners\OutreachController@showList')->name('staff.partners.outreach.list');
    Route::match(['get', 'post'], '/staff/partners/outreach/add', 'Staff\Partners\OutreachController@add')->name('staff.partners.outreach.add');
    Route::match(['get', 'post'], '/staff/partners/outreach/edit/{partnerOutreach}', 'Staff\Partners\OutreachController@edit')->name('staff.partners.outreach.edit');

    // Partners: Data cleanup
    Route::get('/staff/partners/data-cleanup/legacy-partner-multiple', 'Staff\Partners\DataCleanupController@legacyPartnerMultiple')->name('staff.partners.data-cleanup.legacy-partner-multiple');
    Route::get('/staff/partners/data-cleanup/legacy-developer-no-games-company', 'Staff\Partners\DataCleanupController@legacyDeveloperNoGamesCompany')->name('staff.partners.data-cleanup.legacy-developer-no-games-company');
    Route::get('/staff/partners/data-cleanup/legacy-developer-no-games-company/{developer}/game-list', 'Staff\Partners\DataCleanupController@legacyDeveloperNoGamesCompanyGameList')->name('staff.partners.data-cleanup.legacy-developer-no-games-company.game-list');
    Route::get('/staff/partners/data-cleanup/legacy-publisher-no-games-company', 'Staff\Partners\DataCleanupController@legacyPublisherNoGamesCompany')->name('staff.partners.data-cleanup.legacy-publisher-no-games-company');
    Route::get('/staff/partners/data-cleanup/legacy-publisher-no-games-company/{publisher}/game-list', 'Staff\Partners\DataCleanupController@legacyPublisherNoGamesCompanyGameList')->name('staff.partners.data-cleanup.legacy-publisher-no-games-company.game-list');
    Route::get('/staff/partners/data-cleanup/games-with-old-dev-field-set', 'Staff\Partners\DataCleanupController@gamesWithOldDevFieldSet')->name('staff.partners.data-cleanup.games-with-old-dev-field-set');
    Route::get('/staff/partners/data-cleanup/games-with-old-pub-field-set', 'Staff\Partners\DataCleanupController@gamesWithOldPubFieldSet')->name('staff.partners.data-cleanup.games-with-old-pub-field-set');
    Route::get('/staff/partners/data-cleanup/games-with-missing-developer', 'Staff\Partners\DataCleanupController@gamesWithMissingDeveloper')->name('staff.partners.data-cleanup.games-with-missing-developer');
    Route::get('/staff/partners/data-cleanup/games-with-missing-publisher', 'Staff\Partners\DataCleanupController@gamesWithMissingPublisher')->name('staff.partners.data-cleanup.games-with-missing-publisher');

    // Partners: Tools
    Route::match(['get', 'post'], '/staff/partners/tools/partner-update-fields', 'Staff\Partners\ToolsController@partnerUpdateFields')->name('staff.partners.tools.partnerUpdateFields');
    Route::match(['get', 'post'], '/staff/partners/tools/partner-migrate-game-devs-pubs', 'Staff\Partners\ToolsController@partnerMigrateGameDevsPubs')->name('staff.partners.tools.partnerMigrateGameDevsPubs');

});


// *************** Staff: ESHOP *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_ESHOP_MANAGER]], function() {

    Route::get('/staff/eshop/dashboard', 'Staff\Eshop\DashboardController@show')->name('staff.eshop.dashboard');

    // Feed items: eShop (Europe)
    Route::get('/staff/eshop/feed-items-europe/{report?}', 'Staff\Eshop\FeedItemEshopEuropeController@showList')->name('staff.eshop.feed-items-europe.list');
    Route::get('/staff/eshop/feed-items-europe/view/{itemId}', 'Staff\Eshop\FeedItemEshopEuropeController@view')->name('staff.eshop.feed-items-europe.view');
    Route::match(['get', 'post'], '/staff/eshop/feed-items-europe/add-game/{itemId}', 'Staff\Eshop\FeedItemEshopEuropeController@addGame')->name('staff.eshop.feed-items-europe.add-game');
    Route::get('/staff/eshop/europe/add-ignore', 'Staff\Eshop\FeedItemEshopEuropeController@addToIgnoreList')->name('staff.eshop.europe.addToIgnoreList');
    Route::get('/staff/eshop/europe/remove-ignore', 'Staff\Eshop\FeedItemEshopEuropeController@removeFromIgnoreList')->name('staff.eshop.europe.removeFromIgnoreList');

});


// *************** Staff: WIKIPEDIA *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_WIKIPEDIA_MANAGER]], function() {

    Route::get('/staff/wikipedia/dashboard', 'Staff\Wikipedia\DashboardController@show')->name('staff.wikipedia.dashboard');

    // Wiki updates
    Route::get('/staff/wikipedia/wiki-updates/all-pending', 'Staff\Wikipedia\WikiUpdatesController@showListAllPending')->name('staff.wikipedia.wiki-updates.list-all-pending');
    Route::get('/staff/wikipedia/wiki-updates/pending-no-game-id', 'Staff\Wikipedia\WikiUpdatesController@showListNoGameId')->name('staff.wikipedia.wiki-updates.list-no-game-id');
    Route::get('/staff/wikipedia/wiki-updates/pending-with-game-id', 'Staff\Wikipedia\WikiUpdatesController@showListWithGameId')->name('staff.wikipedia.wiki-updates.list-with-game-id');
    Route::get('/staff/wikipedia/wiki-updates/all-complete', 'Staff\Wikipedia\WikiUpdatesController@showListAllComplete')->name('staff.wikipedia.wiki-updates.list-all-complete');
    Route::get('/staff/wikipedia/wiki-updates/all-inactive', 'Staff\Wikipedia\WikiUpdatesController@showListAllInactive')->name('staff.wikipedia.wiki-updates.list-all-inactive');

    Route::match(['get', 'post'], '/staff/wikipedia/wiki-updates/edit/{linkId}', 'Staff\Wikipedia\WikiUpdatesController@edit')->name('staff.wikipedia.wiki-updates.edit');

});


// *************** Staff: DATA SOURCES *************** //
Route::group(['middleware' => ['auth.staff', 'check.user.role:'.\App\UserRole::ROLE_DATA_SOURCE_MANAGER]], function() {

    Route::get('/staff/data-sources/dashboard', 'Staff\DataSources\DashboardController@show')->name('staff.data-sources.dashboard');

    // Data sources: Lists
    Route::get('/staff/data-sources/{sourceId}/list-raw', 'Staff\DataSources\DataSourceRawController@show')->name('staff.data-sources.list-raw');
    Route::get('/staff/data-sources/{sourceId}/list-raw/{itemId}/view', 'Staff\DataSources\DataSourceRawController@view')->name('staff.data-sources.list-raw.view');

    // Data sources: Ignore list
    Route::get('/staff/data-sources/ignore/add', 'Staff\DataSources\DataSourceIgnoreController@addToIgnoreList')->name('staff.data-sources.ignore.addToIgnoreList');
    Route::get('/staff/data-sources/ignore/remove', 'Staff\DataSources\DataSourceIgnoreController@removeFromIgnoreList')->name('staff.data-sources.ignore.removeFromIgnoreList');

    // Data sources: Nintendo.co.uk
    Route::get('/staff/data-sources/nintendo-co-uk/unlinked', 'Staff\DataSources\DataSourceParsedController@nintendoCoUkUnlinkedItems')->name('staff.data-sources.nintendo-co-uk.unlinked');
    Route::get('/staff/data-sources/nintendo-co-uk/ignored', 'Staff\DataSources\DataSourceParsedController@nintendoCoUkIgnoredItems')->name('staff.data-sources.nintendo-co-uk.ignored');
    Route::match(['get', 'post'], '/staff/data-sources/nintendo-co-uk/add-game/{itemId}', 'Staff\DataSources\DataSourceParsedController@addGameNintendoCoUk')->name('staff.data-sources.nintendo-co-uk.add-game');

    // Tools: Nintendo.co.uk
    Route::match(['get', 'post'], '/staff/data-sources/tools/nintendo-co-uk/import-parse-link', 'Staff\DataSources\ToolsController@nintendoCoUkImportParseLink')->name('staff.data-sources.tools.nintendo-co-uk.importParseLink');
    Route::match(['get', 'post'], '/staff/data-sources/tools/nintendo-co-uk/update-games', 'Staff\DataSources\ToolsController@nintendoCoUkUpdateGames')->name('staff.data-sources.tools.nintendo-co-uk.updateGames');
    Route::match(['get', 'post'], '/staff/data-sources/tools/nintendo-co-uk/download-images', 'Staff\DataSources\ToolsController@nintendoCoUkDownloadImages')->name('staff.data-sources.tools.nintendo-co-uk.downloadImages');

    // Data sources: Wikipedia
    Route::get('/staff/data-sources/wikipedia/unlinked', 'Staff\DataSources\DataSourceParsedController@wikipediaUnlinkedItems')->name('staff.data-sources.wikipedia.unlinked');
    Route::get('/staff/data-sources/wikipedia/ignored', 'Staff\DataSources\DataSourceParsedController@wikipediaIgnoredItems')->name('staff.data-sources.wikipedia.ignored');
    Route::match(['get', 'post'], '/staff/data-sources/wikipedia/unlinked/add-link/{itemId}', 'Staff\DataSources\DataSourceParsedController@wikipediaAddLink')->name('staff.data-sources.wikipedia.add-link');

    // Tools: Wikipedia
    Route::match(['get', 'post'], '/staff/data-sources/tools/wikipedia/import-parse-link', 'Staff\DataSources\ToolsController@wikipediaImportParseLink')->name('staff.data-sources.tools.wikipedia.importParseLink');
    Route::match(['get', 'post'], '/staff/data-sources/tools/wikipedia/update-games', 'Staff\DataSources\ToolsController@wikipediaUpdateGames')->name('staff.data-sources.tools.wikipedia.updateGames');

});


// *************** Staff: Admin-only (owner) *************** //
Route::group(['middleware' => ['auth.admin:admin']], function() {

    // Users
    Route::get('/owner/user/list', 'Owner\UserController@showList')->name('owner.user.list');
    Route::get('/owner/user/view/{userId}', 'Owner\UserController@showUser')->name('owner.user.view');
    Route::match(['get', 'post'], '/owner/user/edit/{userId}', 'Owner\UserController@editUser')->name('owner.user.edit');
    Route::match(['get', 'post'], '/owner/user/delete/{userId}', 'Owner\UserController@deleteUser')->name('owner.user.delete');

    // Audit
    Route::get('/owner/audit/index', 'Owner\AuditController@index')->name('owner.audit.index');
    Route::get('/owner/audit/{reportName}', 'Owner\AuditController@showReport')->name('owner.audit.report');

    // Stats
    Route::get('/staff/stats/dashboard', 'Staff\Stats\DashboardController@show')->name('staff.stats.dashboard');

    Route::get('/staff/stats/review-site', 'Staff\Stats\ReviewSiteController@show')->name('staff.stats.reviewSite');

});



/*
 * Admin - old routes
 * To be gradually moved into Staff routes
 */
Route::group(['middleware' => ['auth.admin:admin']], function() {

    // Games: Core
    Route::get('/admin/games/list/{report?}', 'Admin\GamesController@showList')->name('admin.games.list');

    // Games: Filter list
    Route::get('/admin/games/filter-list/with-tag/{linkTitle}', 'Admin\GamesFilterListController@gamesWithTag')->name('admin.games-filter.games-with-tag');
    Route::get('/admin/games/filter-list/no-tag', 'Admin\GamesFilterListController@gamesWithNoTag')->name('admin.games-filter.games-with-no-tag');
    Route::get('/admin/games/filter-list/no-type-or-tag', 'Admin\GamesFilterListController@gamesWithNoTypeOrTag')->name('admin.games-filter.games-with-no-type-or-tag');
    Route::get('/admin/games/filter-list/with-genre/{linkTitle}', 'Admin\GamesFilterListController@gamesWithGenre')->name('admin.games-filter.games-with-genre');
    Route::get('/admin/games/filter-list/series-title-match/{linkTitle}', 'Admin\GamesFilterListController@gameSeriesTitleMatches')->name('admin.games-filter.game-series-title-matches');
    Route::get('/admin/games/filter-list/tag-title-match/{linkTitle}', 'Admin\GamesFilterListController@gameTagTitleMatches')->name('admin.games-filter.game-tag-title-matches');
    Route::get('/admin/games/filter-list/with-genre-no-primary-type', 'Admin\GamesFilterListController@gamesWithGenresNoPrimaryType')->name('admin.games-filter.games-with-genre-no-primary-type');
    Route::get('/admin/games/filter-list/no-eshop-europe-link', 'Admin\GamesFilterListController@gamesWithNoEshopEuropeLink')->name('admin.games-filter.games-no-eshop-europe-link');

    // Games: Title hashes
    Route::get('/admin/games-title-hash/list/{gameId?}', 'Admin\GamesTitleHashController@showList')->name('admin.games-title-hash.list');
    Route::match(['get', 'post'], '/admin/games-title-hash/add', 'Admin\GamesTitleHashController@add')->name('admin.games-title-hash.add');
    Route::match(['get', 'post'], '/admin/games-title-hash/edit/{itemId}', 'Admin\GamesTitleHashController@edit')->name('admin.games-title-hash.edit');
    Route::match(['get', 'post'], '/admin/games-title-hash/delete/{itemId}', 'Admin\GamesTitleHashController@delete')->name('admin.games-title-hash.delete');

    // Games partner links
    Route::get('/admin/game/partner/{gameId}/list', 'Admin\GamePartnerController@showGamePartners')->name('admin.game.partner.list');
    Route::get('/admin/game/partner/{gameId}/save-dev-pub', 'Admin\GamePartnerController@saveDevPub')->name('admin.game.partner.saveDevPub');
    Route::get('/admin/game/partner/{gameId}/legacy-fix-dev', 'Admin\GamePartnerController@legacyFixDev')->name('admin.game.partner.legacyFixDev');
    Route::get('/admin/game/partner/{gameId}/legacy-fix-pub', 'Admin\GamePartnerController@legacyFixPub')->name('admin.game.partner.legacyFixPub');
    Route::get('/admin/game/partner/create-new-company', 'Admin\GamePartnerController@createNewCompany')->name('admin.game.partner.createNewCompany');
    Route::get('/admin/game/developer/{gameId}/add', 'Admin\GamePartnerController@addGameDeveloper')->name('admin.game.developer.add');
    Route::get('/admin/game/developer/{gameId}/remove', 'Admin\GamePartnerController@removeGameDeveloper')->name('admin.game.developer.remove');
    Route::get('/admin/game/publisher/{gameId}/add', 'Admin\GamePartnerController@addGamePublisher')->name('admin.game.publisher.add');
    Route::get('/admin/game/publisher/{gameId}/remove', 'Admin\GamePartnerController@removeGamePublisher')->name('admin.game.publisher.remove');

    // Action lists
    Route::get('/admin/action-lists/no-price', 'Admin\ActionListController@noPrice')->name('admin.action-lists.no-price');

    // Reviews: Links
    Route::match(['get', 'post'], '/admin/reviews/link/add', 'Admin\ReviewLinkController@add')->name('admin.reviews.link.add');
    Route::match(['get', 'post'], '/admin/reviews/link/edit/{linkId}', 'Admin\ReviewLinkController@edit')->name('admin.reviews.link.edit');
    Route::get('/admin/reviews/link/{report?}', 'Admin\ReviewLinkController@showList')->name('admin.reviews.link.list');

    // Partner reviews
    Route::match(['get', 'post'], '/admin/reviews/partner/edit/{reviewId}', 'Admin\PartnerReviewController@edit')->name('admin.reviews.partner.edit');
    Route::get('/admin/reviews/partner/{report?}', 'Admin\PartnerReviewController@showList')->name('admin.reviews.partner.list');

    // Tools
    Route::get('/admin/tools', 'Admin\ToolsController@landing')->name('admin.tools.landing');
    Route::get('/admin/tools/tool/landing/modular/{commandName}', 'Admin\ToolsController@toolLandingModular')->name('admin.tools.toolLandingModular');
    Route::get('/admin/tools/tool/process/modular/{commandName}', 'Admin\ToolsController@toolProcessModular')->name('admin.tools.toolProcessModular');

});

Auth::routes();
