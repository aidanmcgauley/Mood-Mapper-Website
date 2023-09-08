<?php

function shorten($string, $length){
    if(strlen($string)<=$length){
        echo $string;
    } else {
        $shorterStr = substr($string, 0, $length).'... ';
        return $shorterStr;
    }
}
?>