<?php
/*
The MIT License (MIT)
Copyright (c) 2018 AroDev

www.arionum.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
OR OTHER DEALINGS IN THE SOFTWARE.
*/

const SANITY_LOCK_PATH = __DIR__.'/tmp/sanity-lock';

set_time_limit(0);
error_reporting(0);

// make sure it's not accessible in the browser
if (php_sapi_name() !== 'cli') {
    die("This should only be run as cli");
}

require_once __DIR__.'/include/init.inc.php';

// make sure there's only a single sanity process running at the same time
if (file_exists(SANITY_LOCK_PATH)) {
    $ignore_lock = false;
    if ($argv[1] == "force") {
        $res = intval(shell_exec("ps aux|grep sanity.php|grep -v grep|wc -l"));
        if ($res == 1) {
            $ignore_lock = true;
        }
    }
    $pid_time = filemtime(SANITY_LOCK_PATH);

    // If the process died, restart after 10 times the sanity interval
    if (time() - $pid_time > ($_config['sanity_interval'] ?? 900 * 10)) {
        @unlink(SANITY_LOCK_PATH);
    }

    if (!$ignore_lock) {
        die("Sanity lock in place".PHP_EOL);
    }
}

// set the new sanity lock
$lock = fopen(SANITY_LOCK_PATH, "w");
fclose($lock);
$arg = trim($argv[1]);
$arg2 = trim($argv[2]);
echo "Sleeping for 3 seconds\n";
// sleep for 3 seconds to make sure there's a delay between starting the sanity and other processes
if ($arg != "microsanity") {
    sleep(3);
}

if ($argv[1]=="dev") {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
    ini_set("display_errors", "on");
}

// the sanity can't run without the schema being installed
if ($_config['dbversion'] < 2) {
    die("DB schema not created");
    @unlink(SANITY_LOCK_PATH);
    exit;
}

ini_set('memory_limit', '2G');

$block = new Block();
$acc = new Account();
$trx= new Transaction();

$current = $block->current();
_log("Current block: ".$current['height']);


// bootstrapping the initial sync
if ($current['height']==1) {
    echo "Bootstrapping!\n";
    $db_name=substr($_config['db_connect'], strrpos($_config['db_connect'], "dbname=")+7);
    $db_host=substr($_config['db_connect'], strpos($_config['db_connect'], ":host=")+6);
    $db_host=substr($db_host, 0, strpos($db_host, ";"));

    echo "DB name: $db_name\n";
    echo "DB host: $db_host\n";
    echo "Downloading the blockchain dump from arionum.info\n";
    $arofile=__DIR__ . '/tmp/aro.sql';
    if (file_exists("/usr/bin/curl")) {
        system("/usr/bin/curl -o $arofile 'https://arionum.info/dump/aro.sql'", $ret);
    } elseif (file_exists("/usr/bin/wget")) {
        system("/usr/bin/wget -O $arofile 'https://arionum.info/dump/aro.sql'", $ret);
    } else {
        die("/usr/bin/curl and /usr/bin/wget not installed or inaccessible. Please install either of them.");
    }
    

    echo "Importing the blockchain dump\n";
    system("mysql -h ".escapeshellarg($db_host)." -u ".escapeshellarg($_config['db_user'])." -p".escapeshellarg($_config['db_pass'])." ".escapeshellarg($db_name). " < ".$arofile);
    echo "Bootstrapping completed. Waiting 2mins for the tables to be unlocked.\n";

    @unlink($arofile);

    while (1) {
        sleep(120);
  
        $res=$db->run("SHOW OPEN TABLES WHERE In_use > 0");
        if (count($res==0)) {
            break;
        }
        echo "Tables still locked. Sleeping for another 2 min. \n";
    }

   

    $current = $block->current();
}

_log("Finishing import");

@unlink(SANITY_LOCK_PATH);
