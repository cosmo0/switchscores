<?php

namespace App\Http\Controllers\User;

use App\Construction\DbEdit\GameBuilder;
use App\Construction\DbEdit\GameDirector;
use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\DbEditGame;
use App\Services\Migrations\Category as MigrationsCategory;

use App\Traits\AuthUser;
use App\Traits\SwitchServices;
use Illuminate\Support\Facades\Validator;

class DatabaseHelpController extends Controller
{
    use SwitchServices;
    use AuthUser;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array
     */
    private $validationRules = [
        //'game_id' => 'required',
        'category_id' => 'required',
    ];

    public function landing()
    {
        $bindings = [];

        $serviceMigrationsCategory = new MigrationsCategory();
        $bindings['NoCategoryCount'] = $serviceMigrationsCategory->countGamesWithNoCategory();

        $bindings['TopTitle'] = 'User - Database help - Index';
        $bindings['PageTitle'] = 'Database help';

        return view('user.database-help.index', $bindings);
    }

    public function gamesWithoutCategories()
    {
        $allowedYears = $this->getServiceGameCalendar()->getAllowedYears();
        $serviceMigrationsCategory = new MigrationsCategory();

        $bindings = [];

        $bindings['AllowedYears'] = $allowedYears;

        foreach ($allowedYears as $year) {
            $bindings['NoCategoryCount'.$year] = $serviceMigrationsCategory->countGamesWithNoCategory($year);
        }

        $bindings['TopTitle'] = 'User - Database help - Games without categories';
        $bindings['PageTitle'] = 'Games without categories';

        return view('user.database-help.games-without-categories.index', $bindings);
    }

    public function gamesWithoutCategoriesByYear($year)
    {
        $serviceMigrationsCategory = new MigrationsCategory();

        $bindings = [];

        $bindings['GameList'] = $serviceMigrationsCategory->getGamesWithNoCategory($year);

        $bindings['PendingCategoryEditsGameIdList'] = $this->getServiceDbEditGame()->getPendingCategoryEditsGameIdList();

        $bindings['TopTitle'] = 'User - Database help - Games without categories';
        $bindings['PageTitle'] = 'Games without categories';

        return view('user.database-help.games-without-categories.list', $bindings);
    }

    public function submitGameCategorySuggestion($gameId)
    {
        $request = request();

        $game = $this->getServiceGame()->find($gameId);
        if (!$game) abort(404);

        $bindings = [];

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), $this->validationRules);

            if ($validator->fails()) {
                return redirect(route('user.database-help.games-without-categories.submit-game-category-suggestion', ['gameId' => $gameId]))
                    ->withErrors($validator)
                    ->withInput();
            }

            $validator->after(function ($validator) use ($game, $request) {
                // Block duplicate submissions
                if ($this->getServiceDbEditGame()->getPendingCategoryEditForGame($game->id)) {
                    $validator->errors()->add('title', 'There is already a pending change for this game.');
                }
                // Check if the value's changed
                if ($game->category_id == $request->category_id) {
                    $validator->errors()->add('title', 'The game already has this category assigned. Please select another.');
                }
            });

            if ($validator->fails()) {
                return redirect(route('user.database-help.games-without-categories.submit-game-category-suggestion', ['gameId' => $gameId]))
                    ->withErrors($validator)
                    ->withInput();
            }

            $dbEditDirector = new GameDirector();
            $dbEditBuilder = new GameBuilder();

            $dbEditDirector->setBuilder($dbEditBuilder);

            $params = [
                'user_id' => $this->getAuthId(),
                'game_id' => $gameId,
                'data_to_update' => DbEditGame::DATA_CATEGORY,
                'current_data' => $game->category_id,
                'new_data' => $request->category_id,
            ];
            $dbEditDirector->buildNew($params);
            $dbEditGame = $dbEditBuilder->getDbEditGame();
            $dbEditGame->save();

            return redirect(route('user.database-help.games-without-categories'));

        }

        $bindings['TopTitle'] = 'User - Database help - Submit category change suggestion';
        $bindings['PageTitle'] = 'Submit category change suggestion';
        $bindings['FormMode'] = 'add';

        $bindings['GameId'] = $gameId;
        $bindings['GameData'] = $game;
        $bindings['CategoryList'] = $this->getServiceCategory()->getAllWithoutParents();
        $bindings['DataSourceNintendoCoUk'] = $this->getServiceDataSourceParsed()->getSourceNintendoCoUkForGame($gameId);

        return view('user.database-help.games-without-categories.form', $bindings);
    }

}
