<?php


function compareStsForbetOdds(&$allSportsEvenetsStsObjArray, &$allSportsEvenetsForbetObjArray)
{
    $numberOfStsSportsEvents = sizeof($allSportsEvenetsStsObjArray);
    $numberOfForbetSportsEvents = sizeof($allSportsEvenetsForbetObjArray);
    $biggerAmountOfEvents = array();
    $smallerAmountOfEvents = array();

    if ($numberOfForbetSportsEvents >= $numberOfStsSportsEvents)
    {
        $biggerAmountOfEvents = $allSportsEvenetsForbetObjArray;
        $smallerAmountOfEvents = $allSportsEvenetsStsObjArray;
        $biggerAmountOfEventsBookmaker = 'Forbet';
        $smallerAmountOfEventsBookmaker = 'STS';
    }
    else if ($numberOfForbetSportsEvents < $numberOfStsSportsEvents)
    {
        $biggerAmountOfEvents = $allSportsEvenetsStsObjArray;
        $smallerAmountOfEvents = $allSportsEvenetsForbetObjArray;
        $biggerAmountOfEventsBookmaker = 'STS';
        $smallerAmountOfEventsBookmaker = 'Forbet';
    }

    foreach ($biggerAmountOfEvents as $firstSportsEvent)
    {
        foreach ($smallerAmountOfEvents as $secondSportsEvent)
        {
            $firstSportsEventName = $firstSportsEvent->homeTeam;
            $firstSportsEventName = strtolower($firstSportsEventName);
            $firstSportsEventName = str_replace(' ','', $firstSportsEventName);
            $secondSportsEventName = $secondSportsEvent->homeTeam;
            $secondSportsEventName = strtolower($secondSportsEventName);
            $secondSportsEventName = str_replace(' ','', $secondSportsEventName);

            if (strlen($firstSportsEventName) >= strlen($secondSportsEventName))
            {
                if (strpos($secondSportsEventName, $firstSportsEventName) !== false)
                {
                    echo "\nmatched: sports event: " . $firstSportsEventName . " | second sports events name: " . $secondSportsEventName . "\n";
                    echo "\nbkmkr: " . $biggerAmountOfEventsBookmaker . " | home team: " . $firstSportsEvent->homeTeam . " | HomeWinOddValue: " . $firstSportsEvent->homeWinOddValue;
                    echo "\nbkmkr: " . $biggerAmountOfEventsBookmaker . " | away team: " . $firstSportsEvent->awayTeam . " | AwayWinOddValue: " . $firstSportsEvent->awayWinOddValue;
                    echo "\nbkmkr: " . $biggerAmountOfEventsBookmaker . " | draw |" . " DrawOddValue: " . $firstSportsEvent->drawOddValue;

                    echo "\nbkmkr: " . $smallerAmountOfEventsBookmaker . " | home team: " . $secondSportsEvent->homeTeam . " | HomeWinOddValue: " . $secondSportsEvent->homeWinOddValue;
                    echo "\nbkmkr: " . $smallerAmountOfEventsBookmaker . " | away team: " . $secondSportsEvent->awayTeam . " | AwayWinOddValue: " . $secondSportsEvent->awayWinOddValue;
                    echo "\nbkmkr: " . $smallerAmountOfEventsBookmaker . " | draw |" . " DrawOddValue: " . $secondSportsEvent->drawOddValue;
                }
            }
            else
            {
                if (strpos($firstSportsEventName, $secondSportsEventName) !== false)
                {
                    echo "matched: second sports evenets name: " . $secondSportsEventName . " | first sports events name: " . $firstSportsEventName . "\n";

                }
            }
        }
    }
}