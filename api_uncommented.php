<?php

use Instagram\SDK\Instagram;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

function getUserInfo($usernameToSearch, $loginInfo) {
    try {

        $instagram = Instagram::builder()->build();
        $instagram->login($loginInfo["username"], $loginInfo["password"]);

        $response = $instagram->userByName($usernameToSearch)->getUser();

        $returnArray = [
            "followers" => $response->getFollowerCount(),
            "following" => $response->getFollowingCount(),
            "profile_pic_url" => $response->getProfilePictureUrl(),
            "bio" => $response->getBiography(),
            "name" => $response->getFullName(),
            "username" => $response->getUsername(),
        ];

        return $returnArray;

    } catch (\Instagram\SDK\Exceptions\RateLimitResponseException $e) {
        return false;
    }
}
function output($text) {
    if (gettype($text) == "array" OR gettype($text) == "object") {
        var_dump($text);
        echo PHP_EOL;
    } else {
        echo $text . PHP_EOL;
    }
    echo "<br>";
}

$accountList = [
    // "bcp.bdc",
    "bdcfashionsociety",
    "bdc_business_society",
    "bdcsportssociety",
    "bdcmediasociety",
    "musicsocietybdc",
    "bdc_science_society",
    "bdcdebates",
    "bdcliterarysociety",
    "bdc_robotics",
    "bdcmunsociety",
    "bdc.ictsociety"
];

$loginList = [
    [
        "username" => "x",
        "password" => "y",
    ],
    [
        "username" => "a",
        "password" => "b",
    ],
];

$currentAccount = 0;

$entireData = [];

foreach ($accountList as $account) {

    $data = getUserInfo($account, $loginList[$currentAccount]);

    output("[INFO] Saving data for account: " . $account);

    while ($data == false) {
        
        output("[INFO] API Limit reached, switching accounts");

        $currentAccount++;

        if (!isset($loginList[$currentAccount])) {
            output("[ERROR] Account limit reached, add new accounts or wait to escape API rate limiting");
            exit;
        }

        output("[INFO] Switched to account: " . $loginList[$currentAccount]["username"]);

        $data = getUserInfo($account, $loginList[$currentAccount]);

    }

    $entireData[] = $data;

}

output("[INFO] Sorting users...");

usort($entireData, function ($account1, $account2) {
    return $account2["followers"] <=> $account1["followers"];
});

output("[INFO] Saving file...");

file_put_contents('results.json', json_encode($entireData));

output("[INFO] Downloading images...");

foreach ($entireData as $account) {
    output("[INFO] Saving image for " . $account["username"]);
    $content = file_get_contents($account["profile_pic_url"]);
    file_put_contents('./images/'.$account["name"].'.jpg', $content);
}

die(output("OK"));