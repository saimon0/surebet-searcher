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

        $sportsEventObject = new SportsEvent();
        $sportsEventObject->gameIdentificator = $elementSportsEvent['eventId'];
        $sportsEventObject->sportsCategory = $elementSportsEvent['sportName'];
        if (array_key_exists('participants', $elementSportsEvent))
        {
            $tempArrayForSportsTeams = $elementSportsEvent['participants'];
            $sportsEventObject->homeTeam = $tempArrayForSportsTeams[0]['participantName'];
            $sportsEventObject->awayTeam = $tempArrayForSportsTeams[1]['participantName'];
        }
        if (array_key_exists('games', $elementSportsEvent))
        {
            #$temp = array();
            $temp = $elementSportsEvent['games'];

            foreach ($temp as $tempElement)
            {
                if (array_key_exists('outcomes', $tempElement) and strcmp($tempElement['gameName'], '1X2') == 0 || strcmp($tempElement['gameName'], 'Wynik końcowy') == 0)
                {
                    $tempOutcomes = $tempElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement)
                    {
                        if (strcmp($outcomesElement['outcomeName'], $sportsEventObject->homeTeam) == 0)
                        {
                            $sportsEventObject->homeWinOddValue = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], $sportsEventObject->awayTeam) == 0)
                        {
                            $sportsEventObject->awayWinOddValue = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], 'remis') == 0)
                        {
                            $sportsEventObject->drawOddValue = $outcomesElement['outcomeOdds'];
                        }
                        $elementIndex++;
                    }
                    $elementIndex = 0;
                    array_push($tempArrayForSportsEventOutcomes, $tempOutcomes);
                }
                else if (strcmp($sportsEventObject->sportsCategory,'Tenis') == 0 and strcmp($tempElement['gameName'],'Zwycięzca') == 0)
                {
                    $tempOutcomes = $tempElement['outcomes'];
                    $elementIndex = 0;
                    foreach ($tempOutcomes as $outcomesElement)
                    {
                        if (strcmp($outcomesElement['outcomeName'], $sportsEventObject->homeTeam) == 0)
                        {
                            $sportsEventObject->homeWinOddValue = $outcomesElement['outcomeOdds'];
                        }
                        if (strcmp($outcomesElement['outcomeName'], $sportsEventObject->awayTeam) == 0)
                        {
                        $sportsEventObject->awayWinOddValue = $outcomesElement['outcomeOdds'];
                        }
                        $sportsEventObject->drawOddValue = 0;
                    }
                }
            }
            $temp = 0;
        }
        if (strcmp($elementSportsEvent['result'], '?-?') != 0 and $sportsEventObject->homeWinOddValue != NULL and $sportsEventObject->awayWinOddValue != NULL)
        {
            array_push($sportsEventsIDsForbet, $sportsEventObject);
        }
        $index++;
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
        echo "Succcessfuly added row to *forbet* table\n";
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

    $removeDuplicatesQuery = 'DELETE FROM forbet using forbet, forbet f2 WHERE forbet.id > f2.id and forbet.sportsEventID = f2.sportsEventID';

    if($connect->query($removeDuplicatesQuery))
    {
        echo "Successfully removed duplicates from *forbet* table using *sportsEventID* column\n";
    }
    else
    {
        echo "error - couldnt remove duplicates from *forbet* table using *sportsEventsID* column\n";
    }
}


