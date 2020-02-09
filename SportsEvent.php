<?php


class SportsEvent
{
    public $gameIdentificator;
    public $sportsCategory;
    public $homeTeam;
    public $awayTeam;
    public $homeWinOddValue; # 1
    public $drawOddValue; # X
    public $awayWinOddValue; # 2
    public $homeOrDrawOddValue; # 1X
    public $awayOrDrawOddValue; # X2
    public $runningEventsDate;

    public function __construct()
    {
    }
}