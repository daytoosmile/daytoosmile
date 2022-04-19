<?php
    class rands{
        function random() {
            $randomNumber = "";
            for ($i = 1;$i <= 8;$i++) {
                $randomNumber .= rand(0,9);
            }
            return $randomNumber;
        }
        function randomOnlyNumber() {
            $randomNumber = "";
            for ($i = 1;$i <= 32;$i++) {
                $randomNumber .= rand(0,9);
            }
            return $randomNumber;
        }
    }
?>
