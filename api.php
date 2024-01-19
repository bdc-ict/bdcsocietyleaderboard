<?php

/**
 * This line of code imports the Instagram library.
 * Please note that this is NOT a native library of PHP,
 * meaning this isn't like the System.IO you import in VB.net, this was made by another 
 * programmer.
 * 
 * Now you CAN make a program like this without the use of any external libraries, but Instagram's API
 * is a mess to deal with as they impose a lot of restrictions on outsiders trying to extract their data.
 * 
 *  It is also easier this way for both the programmer and the viewer as we are doing the same thing in less lines.
 */
use Instagram\SDK\Instagram;

/**
 * These three lines basically allow all errors to be shown.
 * You can ignore them, but I would advise you do not remove them while debugging or editing
 * this software as PHP may hide errors even when there are many.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * We are importing the modules/libraries we will use.
 * 
 * Basically there are a plethora of libraries created by others to use to ease programming.
 * Libraries may handle a various number of tasks. For example, if you are building an application that
 * requires you to deal with time differences, you can import a library that handles that and write less code as
 * handling time manually may require a lot of logic but a popular library that does that can probably do it better
 * than you as it is maintained by expert programmers and update regularly to keep up w industry standards.
 * Libraries need to be imported, so software managers are used to import such libraries. The file I am importing
 * here contains references to the Instagram library I called above.
 * 
 * If you have worked with PHP, you will know this is a Composer autoloader.
 * I added the vendor folder as well in the repo so beginners won't have to deal with downloading composer.
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * This is the function that gets the user's data.
 */
function getUserInfo($usernameToSearch, $loginInfo) {

    /**
     * This is a try catch block you did in A Level CS.
     */
    try {

        /**
         * Attempt to login the account.
         * AKA "try"
         */
        $instagram = Instagram::builder()->build();
        $instagram->login($loginInfo["username"], $loginInfo["password"]);

        /**
         * Get the user.
         */
        $response = $instagram->userByName($usernameToSearch)->getUser();

        /**
         * Create an array containing all the attributes and their values of the account.
         */
        $returnArray = [
            "followers"       => $response->getFollowerCount(),
            "following"       => $response->getFollowingCount(),
            "profile_pic_url" => $response->getProfilePictureUrl(),
            "bio"             => $response->getBiography(),
            "name"            => $response->getFullName(),
            "username"        => $response->getUsername(),
        ];

        /**
         * Return the array built above and conclude the function.
         */
        return $returnArray;
    
    /**
     * If we catch an error/exception, return false to let the function caller know that their
     * request failed. We are only handling the rate limit exception here though.
     */
    } catch (\Instagram\SDK\Exceptions\RateLimitResponseException $e) {
        return false;
    }
}

/**
 * This is just a glorified print function. You can ignore this.
 */
function output($text) {
    if (gettype($text) == "array" OR gettype($text) == "object") {
        var_dump($text);
        echo PHP_EOL;
    } else {
        echo $text . PHP_EOL;
    }
    echo "<br>";
}

/**
 * This array contains the usernames of all the accounts we need the data of.
 */
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

/**
 * Since Instagram has an API limit in place (meaning we can only make a few requests simultaneously before Instagram blocks us),
 * I have created a system that switches accounts once the limit is reached.
 * You can add more than 2 accounts as well.
 * 
 * This array contains the username and password of such accounts. Would advise you to make some burner accounts for this.
 */
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

/**
 * This variable keeps record of what account we are using in the array.
 * PHP arrays start from 0 so the 0th index of $loginList will be used first.
 * This will be incremented if the account limit is reached to use the next account.
 */
$currentAccount = 0;

/**
 * We will append/push the data we extract from the accounts mentioned in $accountsList.
 */
$entireData = [];

/**
 * Here I run a foreach loop to iterate through all the accounts we have in $accountsList.
 */
foreach ($accountList as $account) {

    /**
     * Call the getUserInfo function with:
     * 1) $account (the account's username)
     * 2) $loginList[$currentAccount] (the login info of the current account we are using (starts with 1 then increments if API limit reached))
     */
    $data = getUserInfo($account, $loginList[$currentAccount]);

    output("[INFO] Saving data for account: " . $account);

    /**
     * If the data we get is FALSE, it means the API limit has exceeded.
     * We attempt to change accounts here.
     */
    while ($data == false) {
        
        output("[INFO] API Limit reached, switching accounts");

        /**
         * Increment the $currentAccount pointer.
         * Can also be written as $currentAccount = $currentAccount + 1
         */
        $currentAccount++;

        /**
         * If this was the last account we had, we simply exit the script because
         * there's not much we can do.
         */
        if (!isset($loginList[$currentAccount])) {
            output("[ERROR] Account limit reached, add new accounts or wait to escape API rate limiting");
            exit;
        }

        output("[INFO] Switched to account: " . $loginList[$currentAccount]["username"]);

        /**
         * Lastly, attempt to get the user info again. If false, the while loop reruns,
         * if not, we go outside the loop.
         */
        $data = getUserInfo($account, $loginList[$currentAccount]);

    }

    /**
     * We push the data into the $entireData array,
     * aka appending. You may appending from CS file handling stuff, except this is an array, not a file.
     */
    $entireData[] = $data;

}

output("[INFO] Sorting users...");

/**
 * Here we attempt to sort the users based on their follower count.
 * This is basically a built in bubble sort algorithm in PHP, meaning I do not
 * have to write the entire thing by myself.
 * We are sorting $entireData (data of users in $accountList) based on the followers attribute of the items.
 */
usort($entireData, function ($account1, $account2) {
    return $account2["followers"] <=> $account1["followers"];
});

output("[INFO] Saving file...");

/**
 * We take the data and save it in a JSON file.
 * Notice how I can the json_encode function, thats because:
 * 1) I cant save an array into a file. An array on exists in the context of a programming language.
 *    It is not a readable mode of information.
 * 2) JSON is the industry standard of information communication. This means this file can be read
 *    and used by any programming language or software.
 */
file_put_contents('results.json', json_encode($entireData));

output("[INFO] Downloading images...");

/**
 * Here we download the profile pictures of the accounts we put in $accountsList.
 * Why am I downloading these instead of just using the URL?
 * Its because Facebook servers do not allow their images to be rendered on HTML;
 * or atleast thats what I think so. Referencing the links in img HTML tags did not load the image,
 * so I just started downloading them and hosting them locally.
 */
foreach ($entireData as $account) {
    output("[INFO] Saving image for " . $account["username"]);
    $content = file_get_contents($account["profile_pic_url"]);
    file_put_contents('./images/'.$account["name"].'.jpg', $content);
}

die(output("OK"));