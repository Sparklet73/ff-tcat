<?php

if ($argc < 1)
    die; // only run from command line

include_once('../config.php');
include_once('../common/functions.php');
include_once('../capture/common/functions.php');

// specify the name of the bin here 
$bin_name = '';
// specify dir with the user timelines (json)
$dir = '';

if (empty($bin_name))
    die("bin_name not set\n");
$querybin_id = queryManagerBinExists($bin_name);

$dbh = pdo_connect();
create_bin($bin_name, $dbh);

$all_files = glob("$dir/*");

$all_users = $all_tweet_ids = array();
$tweets_processed = $tweets_failed = $tweets_success = 0;
$count = count($all_files);
$c = $count;

for ($i = 0; $i < $count; ++$i) {
    $filepath = $all_files[$i];
    process_json_file_timeline($filepath, $dbh);
    print $c-- . "\n";
}

queryManagerCreateBinFromExistingTables($bin_name, $querybin_id, 'import gnip');

function process_json_file_timeline($filepath, $dbh) {
    print $filepath . "\n";
    global $tweets_processed, $tweets_failed, $tweets_success, $all_tweet_ids, $all_users, $bin_name;

    ini_set('auto_detect_line_endings', true);

    $handle = @fopen($filepath, "r");
    if ($handle) {
        while (($buffer = fgets($handle, 40960)) !== false) {
            $buffer = trim($buffer);
            if (empty($buffer))
                continue;
            $tweet = json_decode($buffer);

            $buffer = "";

            $t = Tweet::fromGnip($tweet);

            if ($t === false)
                continue;

            $all_users[] = $t->user->id;
            $all_tweet_ids[] = $t->id;

            $saved = $t->save($dbh, $bin_name);

            if ($saved) {
                $tweets_success++;
            } else {
                $tweets_failed++;
            }

            $tweets_processed++;

            print ".";
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
}

print "\n\n\n\n";
print "Number of tweets: " . count($all_tweet_ids) . "\n";
print "Unique tweets: " . count(array_unique($all_tweet_ids)) . "\n";
print "Unique users: " . count(array_unique($all_users)) . "\n";

print "Processed $tweets_processed tweets!\n";
print "Failed storing $tweets_failed tweets!\n";
print "Succesfully stored $tweets_success tweets!\n";
print "\n";
?>
