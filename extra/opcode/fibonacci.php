<?php

function fibonacci_r($n)
{
    if ($n < 2) {
        return 1;
    }

    return fibonacci_r($n - 2) + fibonacci_r($n - 1);
}
