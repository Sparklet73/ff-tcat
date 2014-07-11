<?php
/**
 * Created by PhpStorm.
 * User: Ching-Ya Lin
 * Date: 2014/8/22
 */
include_once("../../config.php");

if (defined("ADMIN_USER") && ADMIN_USER != "" && (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != ADMIN_USER))
    die("Go away, you evil hacker!");

include_once("../query_manager.php");
include_once("../../common/functions.php");
include_once("../../capture/common/functions.php");

create_admin();
create_error_logs();

$captureroles = unserialize(CAPTUREROLES);

$querybins = getBins();
$activePhrases = getNrOfActivePhrases();
$activeUsers = getNrOfActiveUsers();
$lastRateLimitHit = getLastRateLimitHit();
?>

<html>
    <head>
        <title>FFtcat - Archive your own tweets</title>
        <meta charset='<?php echo mb_internal_encoding(); ?>'>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <style type="text/css">
            table {font-size:12px;}
            .form-control{width: 400px; margin: 5px;}
            th { background-color: #ccc; padding:8px;}
            td { background-color: #eee; padding:8px;}
        </style>
    </head>
    <body>
        <h1>Flood Fire - Create Query Bin</h1>
            <form onsubmit="sendNewForm(); return false;" method="post">
                <div class="form-group">
                    <label for="newbin_name" class="col-sm-2 control-label">Query Bin</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="newbin_name" placeholder="Bin name">
                        <li>之後不可修改。</li>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPhrase" class="col-sm-2 control-label">Phrase to search</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputPhrase" placeholder="Phrases">
                        <li>以OR區別關鍵字，例如:台灣 OR 中華民國 OR Taiwan OR Republic of China</li>
                        <br>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">Create query bin</button>
                    </div>
                </div>
            </form>
    <br>
    <?php
    echo "<h2>Query manager</h2>";
    echo '<table id="thetable">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>querybin</th>';
    echo '<th>description</th>';
    echo '<th class="keywords">queries</th>';
    echo '<th>no. tweets</th>';
    echo '<th>Periods in which the query bin was active</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($querybins as $bin) {
        $phraseList = array();
        $phrasePeriodsList = array();
        $activePhraseList = array();

        if ($bin->type == 'search')
        {
            foreach ($bin->phrases as $phrase)
            {
                $phrasePeriodList[$phrase->id] = array_unique($phrase->periods);
                $phraseList[$phrase->id] = $phrase->phrase;
                if ($phrase->active)
                {
                    $activePhraselist[$phrase->id] = $phrase->phrase;
                }
            }
        }
        $bin->periods = array_unique($bin->periods);
        sort($bin->periods);
        asort($phraseList);

        $action = ($bin->active == 0) ? "start" : "stop";
        if ($bin->type == 'search')
        {
            echo '<tr>';
            echo '<td valign="top">' . $bin->name . '</td>';
            echo '<td valign="top">' . $bin->active . '</td>';
            echo '<td class="keywords" valign="top">';
            echo '<table width="100%">';
            foreach ($phraseList as $phrase_id => $phrase)
            {
                echo "<td valign='top'>$phrase</td>";
            }
            echo '</table>';
            echo '</td>';
            echo '<td valign="top" align="right"> ' . number_format($bin->nrOfTweets, 0, ",", ".") . '</td>';
            echo '<td valign="top" align="right"> ' . implode("<br />", $bin->periods) . '</td>';
        }
    }
    ?>
    <script type="text/javascript">

        function sendNewForm() {
            var _bin = $("#newbin_name").val();
            if(!validateBin(_bin))
                return false;

            var _phrases = $("#newbin_phrases").val();
            var _check = window.confirm("You are about to create a new query bin. Are you sure?");
            if(_check == true) {
                var _params = {action:"newbin",type:_type,newbin_phrases:_phrases,newbin_name:_bin,active:$("#make_active").val()};

                $.ajax({
                    dataType: "json",
                    url: "query_manager.php",
                    type: 'POST',
                    data: _params
                }).done(function(_data) {
                    alert(_data["msg"]);
                    location.reload();
                });
            }
            return false;
        }

        function validateBin(binname) {
            if(binname == null || binname.trim()=="") {
                alert("You cannot use an empty bin name");
                return false;
            }
            var reg = /^[a-zA-Z0-9_]+$/;
            if(!reg.test(binname.trim())) {
                alert("bin names can only consist of alpha-numeric characters and underscores")
                return false;
            }
            return true;
        }

    </script>
    </body>
</html>
