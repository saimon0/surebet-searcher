<?php


function scrapDataFromForbetUrl($stsUrl, &$data, &$decodedJsonData, &$jsonOdds)
{
    $data = file_get_contents($stsUrl);
    $decodedJsonData = json_decode($data, true);
    $jsonOdds = $decodedJsonData['data'];
}



function getSportsEventsForbet(&$jsonOdds, &$sportsEventsIDsForbet)
{
    $index = 0;
    foreach ($jsonOdds as $elementSportsEvent)
    {
        #print_r($elementSportsEvent);
        #$tempArrayForSportsTeams = array();
        $tempArrayForSportsEventGames = array();
        $tempArrayForSportsEventOutcomes = array();

        if (strcmp($elementSportsEvent['sportName'], 'Piłka nożna') == 0) # 1. SOCCER
        {
            $sportsEventObject = new SoccerSportsEvent();
            $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
            $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
            $sportsEventObject->currentResult = $elementSportsEvent['result'];
        }
        else if (strcmp($elementSportsEvent['sportName'], 'Tenis') == 0) # 2. TENNIS
        {
            $sportsEventObject = new TennisSportsEvent();
            $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
            $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
            $sportsEventObject->currentResult = $elementSportsEvent['result'];
        }
        else if (strcmp($elementSportsEvent['sportName'], 'Koszykówka') == 0) # 3. BASKETBALL
        {
            $sportsEventObject = new BasketballSportsEvent();
            $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
            $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
            $sportsEventObject->currentResult = $elementSportsEvent['result'];
        }
        else if (strcmp($elementSportsEvent['sportName'], 'Piłka ręczna') == 0) # 4. HANDBALL
        {
            $sportsEventObject = new HandballSportsEvent();
            $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
            $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
            $sportsEventObject->currentResult = $elementSportsEvent['result'];
        }
        else if (strcmp($elementSportsEvent['sportName'], 'Hokej na lodzie') == 0) # 5. ICE HOCKEY
        {
            $sportsEventObject = new IceHockeySportsEvent();
            $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
            $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
            $sportsEventObject->currentResult = $elementSportsEvent['result'];
        }
        else if (strcmp($elementSportsEvent['sportName'], 'Siatkówka') == 0) # 6. VOLLEYBALL
        {
            $sportsEventObject = new VolleyballSportsEvent();
            $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
            $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
            $sportsEventObject->currentResult = $elementSportsEvent['result'];
        }


        if (array_key_exists('participants', $elementSportsEvent))
        {
            $tempArrayForSportsTeams = $elementSportsEvent['participants'];
            $sportsEventObject->homeTeam = $tempArrayForSportsTeams[0]['participantName'];
            $sportsEventObject->awayTeam = $tempArrayForSportsTeams[1]['participantName'];
        }

        if (strcmp($elementSportsEvent['result'], '?-?') != 0)
        {
            array_push($sportsEventsIDsForbet, $sportsEventObject);
        }
        $index++;
    }
}


function getOddsForSportsEventsForbet(&$jsonOdds, &$sportsEventsIDsForbet) # wyciąganie kursów dla poszczególnych zdarzeń w zależności od sportu
{
    foreach ($sportsEventsIDsForbet as $elementSportsEvent)
    {
        if (strcmp($elementSportsEvent->sportsCategory, "Piłka nożna") == 0) # wyciaganie kursow dla zdarzenia, ktore jest pilka nozna
        {
            getOddsForSoccerSportsEvent($jsonOdds, $sportsEventsIDsForbet, $elementSportsEvent);
        }
    }
}


function getOddsForSoccerSportsEvent(&$jsonOdds, &$sportsEventsIDsForbet, $elementSportsEvent)
{
    foreach ($jsonOdds as $jsonSportsEvent)
    {
        $tempArrayForSportsTeams = $jsonSportsEvent['participants'];
        $eventID = $jsonSportsEvent['eventId'];
        $sportsEventLink = 'https://www.iforbet.pl/livebetting-api/rest/livebetting/v1/api/running/multi/';
        $sportsEventLink .= $eventID;
        if (array_key_exists('games',$jsonSportsEvent) and strcmp($elementSportsEvent->homeTeam, $tempArrayForSportsTeams[0]['participantName']) == 0 and strcmp($elementSportsEvent->awayTeam, $tempArrayForSportsTeams[1]['participantName']) == 0) {
            $tempGames = $jsonSportsEvent['games'];

            foreach ($tempGames as $gamesElement)
            {
                if (array_key_exists('outcomes', $gamesElement) and strcmp($gamesElement['gameName'], '1X2') == 0 || strcmp($gamesElement['gameName'], 'Wynik końcowy') == 0) {
                    #$elementSportsEvent->homeWinOddValue = $gamesElement[]

                    $tempOutcomes = $gamesElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement) {
                        if (strcmp($outcomesElement['outcomeName'], $elementSportsEvent->homeTeam) == 0)
                        {
                            $elementSportsEvent->homeWinOddValue = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], $elementSportsEvent->awayTeam) == 0)
                        {
                            $elementSportsEvent->awayWinOddValue = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], 'remis') == 0)
                        {
                            $elementSportsEvent->drawOddValue = $outcomesElement['outcomeOdds'];
                        }
                        $elementIndex++;
                    }
                }
                if (array_key_exists('outcomes', $gamesElement) and (strcmp($gamesElement['gameName'], 'poniżej/powyżej 0.5') == 0 or strcmp($gamesElement['gameName'], 'poniżej/powyżej 0.5 goli') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 0.5') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 0.5 goli') == 0)) {
                    $tempOutcomes = $gamesElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement) {
                        if (strcmp($outcomesElement['outcomeName'], "poniżej 0.5") == 0) {
                            $elementSportsEvent->underHalfGoal = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "powyżej 0.5") == 0) {
                            $elementSportsEvent->overHalfGoal = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Poniżej 0.5") == 0) {
                            $elementSportsEvent->underHalfGoal = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Powyżej 0.5") == 0) {
                            $elementSportsEvent->overHalfGoal = $outcomesElement['outcomeOdds'];
                        }
                        $elementIndex++;
                    }
                }
                if (array_key_exists('outcomes', $gamesElement) and (strcmp($gamesElement['gameName'], 'poniżej/powyżej 1.5') == 0 or strcmp($gamesElement['gameName'], 'poniżej/powyżej 1.5 goli') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 1.5') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 1.5 goli') == 0) ){
                    $tempOutcomes = $gamesElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement) {
                        if (strcmp($outcomesElement['outcomeName'], "poniżej 1.5") == 0) {
                            $elementSportsEvent->underOneAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "powyżej 1.5") == 0) {
                            $elementSportsEvent->overOneAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Poniżej 1.5") == 0) {
                            $elementSportsEvent->underOneAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Powyżej 1.5") == 0) {
                            $elementSportsEvent->overOneAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        $elementIndex++;
                    }
                }
                if (array_key_exists('outcomes', $gamesElement) and (strcmp($gamesElement['gameName'], 'poniżej/powyżej 2.5') == 0 or strcmp($gamesElement['gameName'], 'poniżej/powyżej 2.5 goli') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 2.5') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 2.5 goli') == 0)) {
                    $tempOutcomes = $gamesElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement) {
                        if (strcmp($outcomesElement['outcomeName'], "poniżej 2.5") == 0) {
                            $elementSportsEvent->underTwoAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "powyżej 2.5") == 0) {
                            $elementSportsEvent->overTwoAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Poniżej 2.5") == 0) {
                            $elementSportsEvent->underTwoAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Powyżej 2.5") == 0) {
                            $elementSportsEvent->overTwoAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        $elementIndex++;
                    }
                }
                if (array_key_exists('outcomes', $gamesElement) and (strcmp($gamesElement['gameName'], 'poniżej/powyżej 3.5') == 0 or strcmp($gamesElement['gameName'], 'poniżej/powyżej 3.5 goli') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 3.5') == 0 or strcmp($gamesElement['gameName'], 'Poniżej/Powyżej 3.5 goli') == 0)) {
                    $tempOutcomes = $gamesElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement) {
                        if (strcmp($outcomesElement['outcomeName'], "poniżej 3.5") == 0) {
                            $elementSportsEvent->underThreeAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "powyżej 3.5") == 0) {
                            $elementSportsEvent->overThreeAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Poniżej 3.5") == 0) {
                            $elementSportsEvent->underThreeAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], "Powyżej 3.5") == 0) {
                            $elementSportsEvent->overThreeAndHalfGoals = $outcomesElement['outcomeOdds'];
                        }
                        $elementIndex++;
                    }
                }
            }

            $eventData = file_get_contents($sportsEventLink);
            $decodedEventJsonData = json_decode($eventData, true);
            $eventJsonOdds = $decodedEventJsonData['data'];

            foreach ($eventJsonOdds as $eventElement)
            {
                if (array_key_exists('games', $eventElement))
                {
                    $tempEventGames = $eventElement['games'];
                    foreach ($tempEventGames as $eventGamesElement)
                    {
                        if (strcmp($eventGamesElement['gameName'], '1. połowa - poniżej/powyżej 0.5') == 0)
                        {
                            $tempEventOutcomes = $eventGamesElement['outcomes'];
                            foreach ($tempEventOutcomes as $tempEventOutcomesElement) {
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "poniżej 0.5") == 0)
                                {
                                    $elementSportsEvent->underHalfGoalHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "powyżej 0.5") == 0)
                                {
                                    $elementSportsEvent->overHalfGoalHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                            }
                        }
                        if (strcmp($eventGamesElement['gameName'], '1. połowa - poniżej/powyżej 1.5') == 0)
                        {
                            $tempEventOutcomes = $eventGamesElement['outcomes'];
                            foreach ($tempEventOutcomes as $tempEventOutcomesElement) {
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "poniżej 1.5") == 0)
                                {
                                    $elementSportsEvent->underOneAndHalfGoalsHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "powyżej 1.5") == 0)
                                {
                                    $elementSportsEvent->overOneAndHalfGoalsHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                            }
                        }
                        if (strcmp($eventGamesElement['gameName'], '1. połowa - poniżej/powyżej 2.5') == 0)
                        {
                            $tempEventOutcomes = $eventGamesElement['outcomes'];
                            foreach ($tempEventOutcomes as $tempEventOutcomesElement) {
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "poniżej 2.5") == 0)
                                {
                                    $elementSportsEvent->underTwoAndHalfGoalsHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "powyżej 2.5") == 0)
                                {
                                    $elementSportsEvent->overTwoAndHalfGoalsHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                            }
                        }
                        if (strcmp($eventGamesElement['gameName'], '1. połowa - poniżej/powyżej 3.5') == 0)
                        {
                            $tempEventOutcomes = $eventGamesElement['outcomes'];
                            foreach ($tempEventOutcomes as $tempEventOutcomesElement) {
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "poniżej 3.5") == 0)
                                {
                                    $elementSportsEvent->underThreeAndHalfGoalsHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                                if (strcmp($tempEventOutcomesElement['outcomeName'], "powyżej 3.5") == 0)
                                {
                                    $elementSportsEvent->overThreeAndHalfGoalsHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                            }
                        }
                        if (strcmp($eventGamesElement['gameName'], '1. połowa - 1X2') == 0)
                        {
                            $tempEventOutcomes = $eventGamesElement['outcomes'];
                            foreach ($tempEventOutcomes as $tempEventOutcomesElement) {
                                if (strcmp($tempEventOutcomesElement['outcomeName'], $elementSportsEvent->homeTeam) == 0)
                                {
                                    $elementSportsEvent->homeWinOddValueHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                                if (strcmp($tempEventOutcomesElement['outcomeName'], $elementSportsEvent->awayTeam) == 0)
                                {
                                    $elementSportsEvent->awayWinOddValueHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                                if (strcmp($tempEventOutcomesElement['outcomeName'], 'remis') == 0)
                                {
                                    $elementSportsEvent->drawOddValueHT = $tempEventOutcomesElement['outcomeOdds'];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}


function saveForbetDataToFile(&$jsonOdds)
{
    $encodedStsOdds = json_encode($jsonOdds);
    $fileName = "ForbetOdds.json";
    file_put_contents($fileName, "");
    if (file_put_contents($fileName, $encodedStsOdds))
    {
        echo "File *ForbetOdds.json* successfully saved\n\n";
    }
}



function insertForbetSportsEventInDatabase($sportsEventObj, mysqli $connection)
{
    $todaysDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    $insertQuery = "INSERT INTO forbet (sportsEventID, eventDateTime, addedToDBTime, sportsCategory, homeTeamName, awayTeamName, homeWinOddValue, awayWinOddValue, drawOddValue) VALUES ('$sportsEventObj->gameIdentificator', '$todaysDate', '$currentTime','$sportsEventObj->sportsCategory', '$sportsEventObj->homeTeam','$sportsEventObj->awayTeam','$sportsEventObj->homeWinOddValue','$sportsEventObj->awayWinOddValue','$sportsEventObj->drawOddValue')";

    if($connection->query($insertQuery))
    {
        #echo "Succcessfuly added row to *forbet* table\n";
    }
    else
    {
        echo 'error - data was not inserted: ' . $connection->connect_error . "\n";
    }
}



function updateForbetTableInDatabase($allSportsEventsForbetObjArray, mysqli $connect)
{
    foreach ($allSportsEventsForbetObjArray as $element)
    {
        $getFromForbetTable = "SELECT * FROM forbet WHERE sportsEventID = '$element->gameIdentificator')";

        $ifExistsResult = $connect->query($getFromForbetTable);

        if (!$ifExistsResult || mysqli_num_rows($ifExistsResult) != 0)
        {
            insertForbetSportsEventInDatabase($element, $connect);
        }
    }

    foreach ($allSportsEventsForbetObjArray as $element)
    {
        if (strcmp($element->sportsCategory, 'Piłka nożna') == 0)
        {
            $updateForbetTableQuery = "UPDATE forbet SET homeWinOddValue = '$element->homeWinOddValue', awayWinOddValue = '$element->awayWinOddValue', drawOddValue = '$element->drawOddValue' WHERE sportsEventID = '$element->gameIdentificator'";
            if ($connect->query($updateForbetTableQuery))
            {
                echo "table *forbet* was updated (soccer)\n";
            }
            else
            {
                echo "table *forbet* was NOT updated (soccer)\n";
            }
        }
        else if (strcmp($element->sportsCategory, 'Tenis') == 0)
        {
            $updateForbetTableQuery = "UPDATE forbet SET homeWinOddValue = '$element->homeWinOddValue', awayWinOddValue = '$element->awayWinOddValue' WHERE sportsEventID = '$element->gameIdentificator'";
            if ($connect->query($updateForbetTableQuery))
            {
                echo "table *forbet* was updated (tenis)\n";
            }
            else
            {
                echo "table *forbet* was NOT updated (tenis)\n";
            }
        }
    }

    $removeDuplicatesQuery = 'DELETE FROM forbet using forbet, forbet f2 WHERE forbet.ID > f2.ID and forbet.sportsEventID = f2.sportsEventID';

    if($connect->query($removeDuplicatesQuery))
    {
        echo "Successfully removed duplicates from *forbet* table using *sportsEventID* column\n";
    }
    else
    {
        echo "error - couldnt remove duplicates from *forbet* table using *sportsEventsID* column\n";
    }
}


