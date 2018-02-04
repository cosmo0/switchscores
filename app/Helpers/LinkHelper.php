<?php


namespace App\Helpers;

use App\Game;

class LinkHelper
{
    static function gameShow(Game $game)
    {
        return route('game.show', ['id' => $game->id, 'link_title' => $game->link_title]);
    }
}