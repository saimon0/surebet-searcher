<?php
include("getting_odds_values_sts.php");
include("getting_odds_values_forbet.php");
include("SportsEvent.php");
include("comparing_odds_values.php");
include("DBConnectivity.php");
include("getting_odds_values_orbit.php");


$todaysDate = date("Y-m-d");
$previousDateOfDay = date("Y-m-d", strtotime("-1 days"));


# STS
$stsUrlToData = "https://spoon.sts.pl/main_opportunities/?lang=pl";
$allSportsEventsStsObjArray = array();
$stsJsonData = 0;
$decodedStsJsonData = 0;
$jsonOddsSts = 0;
$jsonOpptiesSts = 0;
$sportsEventSts = array();
$allSportsEventsSts = array();
$allSportsEventsIdentificators = array();


# Forbet
$forbetUrlToData = "https://www.iforbet.pl/livebetting-api/rest/livebetting/v1/api/running/games/major";
$allSportsEventsForbetObjArray = array();
$jsonOddsForbet = 0;
$jsonOpptiesForbet = 0;
$sportsEventsIDsForbet = array();
$forbetJsonData = 0;
$decodedForbetJsonData = 0;



# calling functions


scrapDataFromStsUrl($stsUrlToData, $stsJsonData, $decodedStsJsonData, $jsonOddsSts, $jsonOpptiesSts);
saveDataToFile($stsJsondata, $jsonOddsSts, "jsonSts.json");

scrapDataFromForbetUrl($forbetUrlToData, $forbetJsonData, $decodedForbetJsonData, $jsonOddsForbet);
saveForbetDataToFile($jsonOddsForbet);

$sportsEventObject = new SportsEvent();

#getHomeDrawAwayWinOdds($sportsEventSts, $homeSportsTeamName, $awaySportsTeamName, $sportsEventObject);

#print_r($sportsEventObject);



$allOtlSts = array();

getAllOtlSts($jsonOddsSts, $allOtlSts);
getAllIgSts($jsonOddsSts, $allSportsEventsIdentificators);

echo "\nSize of allOtlSts (before filter): " . sizeof($allOtlSts);

$sizeAllOtlStsBeforeFilter = sizeof($allOtlSts);

filterOtlSts($allOtlSts);

#getTodaysSportsEvents($jsonOddsSts, $todaysDate);

getSportsEventRows($jsonOddsSts, $allSportsEventsIdentificators, $allSportsEventsSts);

getSportsEventsSts($allSportsEventsSts, $allSportsEventsStsObjArray);

getSportsEventsForbet($jsonOddsForbet, $allSportsEventsForbetObjArray);
getOddsForSportsEventsForbet($jsonOddsForbet, $allSportsEventsForbetObjArray);

echo "\nSize of allOtlSts (before filter): " . $sizeAllOtlStsBeforeFilter;
echo "\nSize of allOtlSts (after filter): " . sizeof($allOtlSts);
echo "\nSize of array of ig's: " . sizeof(($allSportsEventsIdentificators));
echo "\nSize of array of allSportsEventsSts: " . sizeof(($allSportsEventsSts));

#print_r($allSportsEventsIdentificators);
//print_r($allSportsEventsStsObj);
//print_r($jsonOddsForbet);

echo "\n\n *** begining here: \n"; echo "*****************************\n\n";
#print_r($allSportsEventsForbetObj);

$connection = connectWithDataBase();
echo "\n\nclient info: " . $connection->client_info . "\n";
echo "client version: " . $connection->client_version . "\n";

#insertSportsEventInDatabase($allSportsEventsForbetObjArray[0], $connection);

#updateForbetTableInDatabase($allSportsEventsForbetObjArray, $connection);
#updateStsTableInDatabase($allSportsEventsStsObjArray, $connection);

print_r($allSportsEventsForbetObjArray);
#print_r($allSportsEventsStsObjArray);

#compareStsForbetOdds($allSportsEventsStsObjArray, $allSportsEventsForbetObjArray);

echo "\n\nSize of forbet events:  " . sizeof($allSportsEventsForbetObjArray) . "\n";
echo 'size of sts events:  ' . sizeof($allSportsEventsStsObjArray) . "\n";

