<?php
/**
 * @author: Ching-Ya Lin
 * @since: 2014/8/22
 *
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

$querybins = getSearchBins();
?>

<html>
<head>
    <title>FFtcat - Flood Fire Twitter Capturing and Analysis Toolset</title>
    <meta charset='<?php echo mb_internal_encoding(); ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <style type="text/css">
        body, html {
            font-family: "Comic Sans MS", cursive, sans-serif;
            padding: 2px;
            font-size: 14px;
        }

        table {
            overflow: hidden;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control {
            width: 400px;
            margin: 5px;
        }

        th {
            background-color: #ccc;
            padding: 8px;
        }

        td {
            background-color: #eee;
            padding: 8px;
        }

        .row {
            padding-left: 30px;
            padding-right: 30px;
        }

        .keywords {
            width: 600px;
        }
    </style>
</head>
<body>
<h1>Flood Fire - Twitter Capturing and Analysis Toolset</h1>

<form onsubmit="sendNewForm(); return false;">
    <div class="form-group">
        <label for="newbin_name" class="col-sm-2 control-label">Query Bin</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" id="newbin_name" placeholder="Bin name">
            <li>之後不可修改。</li>
            <br>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPhrase" class="col-sm-2 control-label">Phrase to search</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" id="inputPhrase" placeholder="Phrases">
            <li>以OR區別關鍵字，例如:台灣 OR 中華民國 OR Taiwan OR "Republic of China"</li>
            <br>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPhrase" class="col-sm-2 control-label">Description</label>

        <div class="col-sm-10">
            <input type="text" class="form-control" id="inputDescription" placeholder="Description">
            <br>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Create query bin</button>
            <br>
            <br>
        </div>
    </div>
</form>
<h2>Query manager</h2>

<div class="row">
    <table align="center" class="table table-hover">
        <thead>
        <tr>
            <th>Querybin</th>
            <th class="keywords">Phrases</th>
            <th>No. tweets</th>
            <th>Created Time</th>
            <th>Last Update</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($querybins as $bin) {
            echo '<tr>';
            echo '<td>' . $bin->name . '</td>';
            echo '<td>' . implode(', ', explode("OR", $bin->phrases)) . '</td>';
            echo '<td align="center"> ' . number_format($bin->nrOfTweets) . '</td>';
            echo '<td align="center"> ' . $bin->createtime . '</td>';
            echo '<td align="center"> ' . $bin->updatetime . '</td>';
            echo '<td> ' . $bin->description . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<script type='text/javascript' src='../../analysis/scripts/jquery-1.7.1.min.js'></script>
<script type="text/javascript">
    function sendNewForm() {
        var _bin = $("#newbin_name").val();
        var _description = $("#inputDescription").val();
        if (!validateBin(_bin))
            return false;

        var _phrases = $("#inputPhrase").val();
        var _check = window.confirm("You are about to create a new search query bin. Are you sure?");
        if (_check == true) {
            var _params = {action: "newbin", type: "search", newbin_phrases: _phrases, newbin_name: _bin, description: _description, active: $("#make_active").val()};
            $.ajax({
                dataType: "json",
                url: "../query_manager.php",
                type: 'POST',
                data: _params
            }).done(function (_data) {
                alert(_data["msg"]);
                location.reload();
            });
        }
        return false;
    }

    function validateBin(binname) {
        if (binname == null || binname.trim() == "") {
            alert("You cannot use an empty bin name");
            return false;
        }
        var reg = /^[a-zA-Z0-9_]+$/;
        if (!reg.test(binname.trim())) {
            alert("bin names can only consist of alpha-numeric characters and underscores")
            return false;
        }
        return true;
    }

</script>
</body>
</html>
