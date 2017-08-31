# Champion Chest Checker

It tells you whether or not you have a chest with the specified champion.

Steps taken to acomplish:

1. Get `regionalEndpoint`: user selection
0. Get `summonerName`: user input
0. Get `summonerId`: `{regionalEndpoint}/lol/summoner/v3/summoners/by-name/{summonerName}?api_key={key}` *(only once per session)*
0. Get `championList`: `{regionalEndpoint}/lol/static-data/v3/champions?api_key={key}` *(only once per day)*
0. Get `championMasteryList`: `{regionalEndpoint}/lol/champion-mastery/v3/champion-masteries/by-summoner/{summonerId}?api_key={key}` *(only once per session)*
0. Get `championName`: user input
0. Get `championId`: search list until `championName` is found
0. Get `chestGranted`: search list until `championId` is found