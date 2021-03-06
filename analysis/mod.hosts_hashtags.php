<?php
require_once './common/config.php';
require_once './common/functions.php';
require_once './common/Gexf.class.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Twitter Analytics host hashtag co-occurence</title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" href="css/main.css" type="text/css" />

        <script type="text/javascript" language="javascript">
	
	
	
        </script>

    </head>

    <body>

        <h1>Twitter Analytics - host hashtag co-occurence</h1>

        <?php
        validate_all_variables();
        $filename = get_filename_for_export("hostHashtag");

        $sql = "SELECT COUNT(LOWER(h.text)) AS frequency, LOWER(h.text) AS hashtag, u.domain AS domain FROM ";
        $sql .= $esc['mysql']['dataset'] . "_tweets t, " . $esc['mysql']['dataset'] . "_hashtags h, " . $esc['mysql']['dataset'] . "_urls u ";
        $where = "t.id = h.tweet_id AND h.tweet_id = u.tweet_id AND u.url_followed !='' AND ";
        $sql .= sqlSubset($where);
        $sql .= " GROUP BY u.domain, LOWER(h.text) ORDER BY frequency DESC";
        //print $sql." - <br>";

        $sqlresults = mysql_query($sql);

        $content = "frequency, hashtag, domain\n";
        while ($res = mysql_fetch_assoc($sqlresults)) {
            $content .= $res['frequency'] . "," . $res['hashtag'] . "," . $res['domain'] . "\n";
            $urlHashtags[$res['domain']][$res['hashtag']] = $res['frequency'];
        }
        file_put_contents($filename, chr(239) . chr(187) . chr(191) . $content);

        echo '<fieldset class="if_parameters">';

        echo '<legend>Your spreadsheet (CSV) file</legend>';

        echo '<p><a href="' . str_replace("#", urlencode("#"), str_replace("\"", "%22", $filename)) . '">' . $filename . '</a></p>';

        echo '</fieldset>';



        $gexf = new Gexf();
        $gexf->setTitle("URL-hashtag " . $filename);
        $gexf->setEdgeType(GEXF_EDGE_UNDIRECTED);
        $gexf->setCreator("tools.digitalmethods.net");
        foreach ($urlHashtags as $url => $hashtags) {
            foreach ($hashtags as $hashtag => $frequency) {
                $node1 = new GexfNode($url);
                $node1->addNodeAttribute("type", 'host', $type = "string");
                $gexf->addNode($node1);
                $node2 = new GexfNode($hashtag);
                $node2->addNodeAttribute("type", 'hashtag', $type = "string");
                $gexf->addNode($node2);
                $edge_id = $gexf->addEdge($node1, $node2, $frequency);
            }
        }

        $gexf->render();

        $filename = str_replace(".csv", ".gexf", $filename);
        file_put_contents($filename, $gexf->gexfFile);

        echo '<fieldset class="if_parameters">';

        echo '<legend>Your network (GEXF) file</legend>';

        echo '<p><a href="' . filename_to_url($filename) . '">' . $filename . '</a></p>';

        echo '</fieldset>';
        ?>

    </body>
</html>
