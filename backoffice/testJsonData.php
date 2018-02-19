<?php
header("Content-Type:text/html;Charset:utf-8");
include 'include/config.php';


    $string = file_get_contents("../data/source.json");
    $objectJson = json_decode($string, true);
    $source = $objectJson["source"];



     if(!(file_exists( "../data/autoSource.json"))) {
        $myfile = fopen("../data/autoSource.json", "x+");
        
        $jsonStr = "{";

        $jsonStr .= ('"source":' . json_encode($source));

        $jsonStr .= "}";



        fwrite($myfile, $jsonStr);
        fclose($myfile);
        chmod("../data/autoSource.json", 0777);
    }











?>