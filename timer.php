<?php
    class timer {
        function getOverdueTimer() {
            $Year = date('Y');
            $Month = date('n');
            $Day = date('d');
            $Hour = date('H');
            $Minute = date('i');
            $Second = date('s');
            $time = $Year . $Month .  $Day .  $Hour .  ($Minute + 5) .  $Second;
            return $time;
        }
        function getIntervalTimer() {
            $Year = date('Y');
            $Month = date('n');
            $Day = date('d');
            $Hour = date('H');
            $Minute = date('i');
            $Second = date('s');
            $time = $Year . $Month .  $Day .  $Hour .  ($Minute + 1) .  $Second;
            return $time;
        }
        function getNowTimer() {
            $Year = date('Y');
            $Month = date('n');
            $Day = date('d');
            $Hour = date('H');
            $Minute = date('i');
            $Second = date('s');
            $time = $Year . $Month .  $Day .  $Hour .  $Minute .  $Second;
            return $time;
        }
    }
?>