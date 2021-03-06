<?php

function isCurrentFile($file)
{
    if (!file_exists($file)) {
        return false;
    }
    $filedate = date('Ymd', filemtime($file));
    $todaydate = date('Ymd');
    return $filedate == $todaydate;
}

$apiKey = 'RGAPI';

header('Access-Control-Allow-Origin: *');

if (isset($_REQUEST['request'])) {
    // Get the data
    $data = json_decode(stripslashes($_REQUEST['data']));

    // And now check what kind of request we're dealing with
    if ($_REQUEST['request'] == 'championList') {
        // Get the championList
        if (isCurrentFile('./championList.json')) {
            // If we have a championList from today, we might as well use that one
            $result = file_get_contents('./championList.json');
        } else {
            // But if we don't (or it's old), request it again
            $query = 'https://ddragon.leagueoflegends.com/realms/' . $data->regionalEndpoint . 'json';
            $result = @file_get_contents($query);
            if ($result === false) {
                // Unable to get live patch version on your region. Using EUW instead
                $query = 'https://ddragon.leagueoflegends.com/realms/euw.json';
                $result = @file_get_contents($query);
            }

            $decodedJson = json_decode($result, true);
            $version = $decodedJson["n"]["champion"];

            $query = 'https://ddragon.leagueoflegends.com/cdn/' . $version . '/data/en_US/champion.json';
            $result = @file_get_contents($query);

            if ($result === false) {
                // Server must be down
                //header('HTTP/1.1 502 Bad Gateway');
                die('Unable to get static data');
            }

            // Now, cache it. We'll use this for next request
            file_put_contents('./championList.json', $result);
        }

    } elseif ($_REQUEST['request'] == 'summonerId') {
        $query = 'https://' . $data->regionalEndpoint . '.api.riotgames.com/lol/summoner/v4/summoners/by-name/' . $data->summonerName . '?api_key=' . $apiKey;
        $result = @file_get_contents($query);

        if ($result === false) {
            //header('HTTP/1.1 404 Not Found');
            die('Summoner not found');
        }
    } elseif ($_REQUEST['request'] == 'championMastery') {
        $query = 'https://' . $data->regionalEndpoint . '.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/' . $data->summonerId . '/by-champion/' . $data->championId . '?api_key=' . $apiKey;
        $result = @file_get_contents($query);

        if ($result === false) {
            //header('HTTP/1.1 404 Not Found');
            die('Champion never played');
        }
    } else {
        // Invalid request type
        header('HTTP/1.1 404 Not Found');
        die(json_encode(array('message' => 'No request found')));
    }
    echo $result;
} else {
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(array('message' => 'Invalid request')));
}
