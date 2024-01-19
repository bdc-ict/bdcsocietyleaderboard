<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BDC Societies Statistics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>

<body>

    <nav class="navbar has-shadow is-spaced">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                BDC Societies Statistics
            </a>
        </div>
    </nav>


    <div class="container">
        <section class="section">
            <center>
                <div class="title is-1">BDC Societies Statistics</div>
                <div class="subtitle">Pretty obvious innit</div>
            </center>

            <hr>

            <div class="columns is-centered">
                <div class="column is-4">
                    <div class="box has-background-primary">
                        <div class="title mb-2 has-text-white">What is this?</div>
                        <p class="has-text-white">It's a leaderboard of all BDC's societies Instagram account.</p>
                        <div class="title mt-3 mb-2 has-text-white"">Why?</div>
                        <p class="has-text-white">
                            This page is created for educational purposes.
                            <br>
                            The source code of this application is heavily documented making it a breeze to understand the workflow of a dynamic web application that extracts data from a third party.
                            <br>
                            <a href="" class="button is-fullwidth is-rounded mt-3 mb-2">Source Code</a>
                            <br>
                            <p class="is-size-7">
                                <b>DI</b>
                                <br>
                                2024
                            </p>
                        </p>
                    </div>
                </div>
                <div class="column is-6">
                    
                    <?php
           
                    /**
                     * Open the file we saved
                     */
                    $file = fopen("results.json", "r") or die("Unable to open file!");

                    /**
                     * Decode the file
                     * We encoded the $entireData in the API file from an array to 
                     * JSON text, so we need to decode it and serialise it into an array again
                     * from JSON so PHP can read it
                     */
                    $items = json_decode(fread($file, filesize("results.json")));

                    $counter = 1;

                    foreach ($items as $item) {

                        echo '
                            <div class="box">
                                <article class="media is-align-items-center">
                                    <figure class="media-left">
                                        <p class="image is-128x128">
                                            <img src="http://bdcsocietystats.000.pe/images/' . $item->name . '.jpg">
                                        </p>
                                    </figure>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                            <div class="title mb-1">
                                                <div class="tag is-primary">#' . $counter . '</div>
                                                ' . $item->name . '
                                            </div>
                                            <small><a href="https://www.instagram.com/' . $item->username . '">@' . $item->username . '</a></small>
                                            <br>
                                            <small>
                                            ' . $item->followers . ' Followers
                                            </small>
                                            <small>
                                            ' . $item->following . ' Followers
                                            </small>
                                            <p class="has-text-grey">
                                                ' . nl2br($item->bio) . '
                                            </p>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            ';

                        $counter++;

                    }

                    fclose($file);

                    ?>

                </div>
            </div>
        </section>
    </div>
</body>

<footer class="footer has-text-centered">

    <div class="subtitle is-5">
        BCP BDC
    </div>

    <div class="subtitle is-7 has-text-grey">
        <strong>Brought to you by the ICT Society</strong> ~ 2024
    </div>

</footer>

</html>