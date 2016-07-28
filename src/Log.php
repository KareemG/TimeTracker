<?php namespace Acme;

class Log {
    function __construct($time, $msg, $working) {
        $this->time = $time;
        $this->msg = $msg;
        $this->working = $working;
    }
}