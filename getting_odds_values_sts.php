<?php

function scrapDataFromStsUrl($stsUrl, &$data, &$decodedJsonData, &$jsonOdds, &$jsonOppties)
{
    $data = file_get_contents($stsUrl);
    $decodedJsonData = json_decode($data, true);
    $jsonOdds = $decodedJsonData['odds'];
    $jsonOppties = $decodedJsonData['oppties'];
}


function saveDataToFile(&$data, &$jsonOdds, $fileName)
{
    $encodedStsOdds = json_encode($jsonOdds);
    $fileName = "StsOdds.json";
    file_put_contents($fileName, "");
    if (file_put_contents($fileName, $encodedStsOdds))
    {
        echo "File *StsOdds.json* successfully saved\n\n";
    }
}


function FindSportsEventRows($jsonOdds, &$sportsEvent, $sportsEventName)
{
    $tempEvent = 0;
    foreach ($jsonOdds as $element)
    {
        if (strcmp($element['otl'], $sportsEventName) == 0)
        {
            $tempEvent = $element;
        }
    }
    foreach ($jsonOdds as $element)
    {
        if ($element["ig"] == $tempEvent["ig"])
        {
            array_push($sportsEvent, $element);
        }
    }
}


function getHomeDrawAwayWinOdds($sportsEvent, $homeSportsTeamName, $awaySportsTeamName, &$sportsEventObject)
{
    $tempHomeTeamArray = array();
    $tempAwayTeamArray = array();
    $tempDrawRow = 0;

    foreach ($sportsEvent as $element)
    {
        if (strcmp($element['otl'], $homeSportsTeamName) == 0)
        {
            array_push($tempHomeTeamArray, $element);
        }
        if (strcmp($element['otl'], $awaySportsTeamName) == 0)
        {
            array_push($tempAwayTeamArray, $element);
        }
        if (strcmp($element['otl'], 'remis') == 0)
        {
            $tempDrawRow = $element['ov'];
        }
    }
    /*$homeOddsValue = min($tempHomeTeamArray);
    $homeOddsValue = $homeOddsValue['ov'];

    $awayOddsValue = min($tempAwayTeamArray);
    $awayOddsValue = $awayOddsValue['ov'];

    $sportsEventObject->homeWinOddValue = $homeOddsValue;
    $sportsEventObject->awayWinOddValue = $awayOddsValue;
    $sportsEventObject->drawOddValue = $tempDrawRow;*/
}


function getAllOtlSts($jsonOdds, &$allOtlSts)
{
    foreach ($jsonOdds as $element)
    {
        array_push($allOtlSts, $element['otl']);
    }
}


function getAllIgSts($jsonOdds, &$allGameIdentificators)
{
    foreach ($jsonOdds as $element)
    {
        array_push($allGameIdentificators, $element['ig']);
    }
    $allGameIdentificators = array_unique($allGameIdentificators);
}


function filterOtlSts(&$allOtlSts)
{
    $j = 0;
    $index = 0;
    foreach ($allOtlSts as $element)
    {
        $elementAsString = strval($element);
        if (strcmp($elementAsString, strval(0)) ==  0)
        {
            unset($allOtlSts[$index]);
        }
        if (strpos($elementAsString, 'więcej') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        if (strpos($elementAsString, 'mniej') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        if (strpos($elementAsString, 'remis') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        if (strpos($elementAsString, '1X') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        if (strpos($elementAsString, 'X2') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        if (strpos($elementAsString, '12') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        if (strpos($elementAsString, 'nikt') !== false)
        {
            unset($allOtlSts[$index]);
            $j++;
        }
        $index++;
    }
    $allOtlSts = array_unique($allOtlSts);
}


function getTodaysSportsEvents(&$jsonOddsSts, $todaysDate)
{
    $index = 0;
    $previousDateOfDay = date("Y-m-d", strtotime("-1 days"));
    foreach ($jsonOddsSts as $element)
    {
        $elementAsString = strval($element['aot']);
        $todaysDateAsString = strval($todaysDate);
        $firstCondition = strpos($elementAsString, $todaysDateAsString);
        $secondCondition = strpos($elementAsString, $previousDateOfDay);
        if ($firstCondition === false && $secondCondition === false)
        {
            unset($jsonOddsSts[$index]);
        }
        $index++;
    }
}


function getSportsEventRows($jsonOdds, $allGameIdentificators, &$allSportsEventsSts)
{
    $sportsEventRows = array();
    foreach ($allGameIdentificators as $gameIdentificator)
    {
        foreach ($jsonOdds as $jsonElement)
        {
            if ($gameIdentificator == $jsonElement['ig'])
            {
                array_push($sportsEventRows, $jsonElement);
            }
        }
        array_push($allSportsEventsSts, $sportsEventRows);
        $sportsEventRows = array();
    }
}


function retrieveSportsEventOdds(array $allSportsEventsSts, array &$allSportsEventsStsObj)
{
    foreach ($allSportsEventsSts as $sportsEvent)
    {
        $sportsEventStsObj = new SportsEvent();
        foreach ($sportsEvent as $element)
        {
            $sportsEventStsObj->gameIdentificator = $element['ig'];
            #$sportsEventStsObj->runningEventsDate = $element['aot'];
            #echo "element w retrieve sports events co tw obj: \n"; print_r($element);
            if (strcmp($element['otl'] , 'remis') == 0 )#&& strlen($element['it']) == 4)
            {
                $sportsEventStsObj->sportsCategory = 'pilka_nozna';
                array_push($allSportsEventsStsObj, $sportsEventStsObj);
            }
            if ($element['otl'] != 'nikt' && $element['otl'] != 'remis' && $element['otl'] != '1X' && $element['otl'] != 'X2' && $element['otl'] != '12')
            {
                $firstComparison = strpos($element['otl'], 'więcej');
                $secondComparison = strpos($element['otl'], 'mniej');
                $elementTnAsString = strval($element['tn']);
                $oneDigitAsString = strval(1);
                $twoDigitAsString = strval(2);
                $thirdCondition = strpos($elementTnAsString, $oneDigitAsString);
                $fourthCondition = strpos($elementTnAsString, $twoDigitAsString);

                if ($firstComparison === false && $secondComparison === false && $thirdCondition !== false)
                {
                    $sportsEventStsObj->homeTeam = $element['otl'];
                }
                if ($firstComparison === false && $secondComparison === false && $fourthCondition !== false)
                {
                    $sportsEventStsObj->awayTeam = $element['otl'];
                }
            }
            if ($element['otl'] == 'remis')
            {
                $sportsEventStsObj->drawOddValue = $element['ov'];
            }
        }
        foreach ($sportsEvent as $elementRow)
    {
        if (strcmp($elementRow['otl'], $sportsEventStsObj->homeTeam) == 0)
        {
            $sportsEventStsObj->homeWinOddValue = $elementRow['ov'];
            break;
        }
        foreach ($sportsEvent as $elementRow2)
        {
            if (strcmp($elementRow2['otl'], $sportsEventStsObj->awayTeam) == 0)
            {
                $sportsEventStsObj->awayWinOddValue = $elementRow2['ov'];
                break;
            }
        }
    }
    }
}





function putSportsEventsToDataBase()
{

}