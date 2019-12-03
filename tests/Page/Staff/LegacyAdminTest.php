<?php

namespace Tests\Page\Staff;

//use Illuminate\Support\Facades\Auth;
use App\User;
use Tests\TestCase;

class LegacyAdminTest extends TestCase
{
    /**
     * @var User
     */
    private $userStandard;

    /**
     * @var User
     */
    private $userAdmin;

    public function setUp()
    {
        parent::setUp();

        $this->userStandard = new User(
            ['display_name' => 'Stuart', 'email' => 'stu@worldofswitch.com']
        );
        $this->userAdmin = new User(
            ['display_name' => 'Adam', 'email' => 'adam@worldofswitch.com', 'is_owner' => '1']
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->userStandard);
        unset($this->userAdmin);
    }

    // Auth

    public function testLoggedOut()
    {
        $response = $this->get('/admin');
        $response->assertStatus(401);
    }

    public function testAsNormalUser()
    {
        $this->be($this->userStandard);
        $response = $this->get('/admin');
        $response->assertStatus(401);
    }

    public function testAsAdmin()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin');
        $response->assertStatus(302);
    }

    // Admin pages

    public function testAdminGamesList()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list');
        $response->assertStatus(200);
    }

    public function testAdminGamesListReleased()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/released');
        $response->assertStatus(200);
    }

    public function testAdminGamesListNoGenre()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/no-genre');
        $response->assertStatus(200);
    }

    public function testAdminGamesListNoVideoUrl()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/no-video-url');
        $response->assertStatus(200);
    }

    public function testAdminGamesListNoAmazonUkLink()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/no-amazon-uk-link');
        $response->assertStatus(200);
    }

    public function testAdminGamesListUpcoming()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/upcoming');
        $response->assertStatus(200);
    }

    public function testAdminGamesListUpcoming2018WithDates()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/upcoming-2018-with-dates');
        $response->assertStatus(200);
    }

    public function testAdminGamesListUpcomingBeyond()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/games/list/upcoming-beyond');
        $response->assertStatus(200);
    }

    public function testAdminChartsDate()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/charts/date');
        $response->assertStatus(200);
    }

    public function testAdminNewsList()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/news/list');
        $response->assertStatus(200);
    }

    public function testStaffWikipediaWikiUpdates()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/staff/wikipedia/wiki-updates');
        $response->assertStatus(200);
    }

    public function testAdminFeedItemsReviews()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/feed-items/reviews');
        $response->assertStatus(200);
    }

    public function testAdminReviewsLink()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/reviews/link');
        $response->assertStatus(200);
    }

    public function testAdminReviewsSite()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/reviews/site');
        $response->assertStatus(200);
    }

    public function testAdminTools()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/tools');
        $response->assertStatus(200);
    }

    public function testAdminUserList()
    {
        $this->be($this->userAdmin);
        $response = $this->get('/admin/user/list');
        $response->assertStatus(200);
    }
}
