<?php

function testInput($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;

}

function pushToErrorMessage( &$errorMessage, $data) {
    if (!empty($data)) {
        $errorMessage = $errorMessage . "<br>" . $data;
    }  
}