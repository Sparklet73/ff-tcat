<html>
<head>
    <title>FFtcat - Flood Fire Twitter Capturing and Analysis Toolset</title>
    <meta charset='<?php echo mb_internal_encoding(); ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <style type="text/css">
        body, html {
            font-family: serif, sans-serif, fantasy, monospace;
            padding: 10px;
            font-size: 12px;
        }

        h1 {
            font-size: 34px;
            margin-bottom: 10px;
            margin-top: 0px;
        }

        .navbar {
            font-size: 14px;
        }

        .brand {
            font-size: 22px;
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
            margin-top: 20px;
        }

        .keywords {
            width: 600px;
        }
    </style>

</head>
<body>
<div class="navbar navbar-default" role="navigation">
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li><a class="brand" href="#">FFtcat</a></li>
            <li><a href="index.php" data-toggle="tab">Search</a></li>
            <li class="active"><a href="#archived" data-toggle="tab">Saved Archive</a></li>
            <li><a href="../../analysis/index.php" data-toggle="tab">Analysis</a></li>
        </ul>
    </div>
</div>
<div class="tab-content">
    <div class="tab-pane active" id="archived">
        <h1>FFtcat - Saved Archive</h1>

        <div class="row">
            <table align="center" class="table table-hover">
                <thead>
                <tr>
                    <th>Querybin</th>
                    <th class="keywords">Phrases</th>
                    <th>No. tweets</th>
                    <th>Created Time</th>
                    <th>Saved Time</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
