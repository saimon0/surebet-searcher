<?php


class SportsEvent
{
    public $gameIdentificator;
    public $sportsCategory;
    public $homeTeam;
    public $awayTeam;
    public $homeWinOddValue; # 1
    public $awayWinOddValue; # 2
    public $runningEventsDate;
    public $currentResult;

    public function __construct()
    {

    }
}


class SoccerSportsEvent extends SportsEvent
{
    public $drawOddValue; # X
    public $homeOrDrawOddValue; # 1X
    public $awayOrDrawOddValue; # X2
    public $drawOddValueHT; # X HT
    public $homeWinOddValueHT; # 1 HT
    public $awayWinOddValueHT; # 2 HT
    public $overHalfGoal; # ov 0.5 HT
    public $underHalfGoal; # under 0.5 HT
    public $overOneAndHalfGoals;
    public $underOneAndHalfGoals;
    public $overTwoAndHalfGoals;
    public $underTwoAndHalfGoals;
    public $overThreeAndHalfGoals;
    public $underThreeAndHalfGoals;
    public $overHalfGoalHT;
    public $underHalfGoalHT;
    public $overOneAndHalfGoalsHT;
    public $underOneAndHalfGoalsHT;
    public $overTwoAndHalfGoalsHT;
    public $underTwoAndHalfGoalsHT;
    public $overThreeAndHalfGoalsHT;
    public $underThreeAndHalfGoalsHT;
}


class TennisSportsEvent extends SportsEvent
{

}


class BasketballSportsEvent extends SportsEvent
{

}


class VolleyballSportsEvent extends SportsEvent
{

}


class HandballSportsEvent extends  SportsEvent
{
    
}


class IceHockeySportsEvent extends  SportsEvent
{

}