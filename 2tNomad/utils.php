<?php

/* Utility function to truncate long product names to maximum of
50 characters for consisten tUI */
function truncateText($text, $maxLength = 50)
{
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

function getCartCount($array) // $array e.g $_SESSION['cart']
{
    // Count total items
    // $count = 0;
    // foreach ($array as $item) {
    //     $count += 1;
    // }    
    return count($array);
}

