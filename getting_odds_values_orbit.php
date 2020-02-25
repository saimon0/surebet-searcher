<?php


function scrapDataFromOrbitUrl($orbitUrl, &$data, &$decodedJsonData, &$jsonOdds)
{
    $data = file_get_contents($orbitUrl);
    $decodedJsonData = json_decode($data, true);
    $jsonOdds = $decodedJsonData['data'];
}

