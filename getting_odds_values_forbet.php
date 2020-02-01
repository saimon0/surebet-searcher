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
            echo 'array games element number: ' . $index . "\n";

            foreach ($temp as $tempElement)
            {
                if (array_key_exists('outcomes', $tempElement) and strcmp($tempElement['gameName'], '1X2') == 0)
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
                else
                {
                    echo '\n*outcomes* key was not found\n';
                }
            }
            $temp = 0;
        }
        array_push($sportsEventsIDsForbet, $sportsEventObject);
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