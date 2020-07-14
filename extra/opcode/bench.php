<?php

require_once __DIR__ . '/fibonacci.php';

function gethrtime()
{
    $hrtime = hrtime();
    return (($hrtime[0] * 1000000000 + $hrtime[1]) / 1000000000);
}

$time = gethrtime();

$data = fibonacci_r(37);

echo gethrtime() - $time . PHP_EOL;
