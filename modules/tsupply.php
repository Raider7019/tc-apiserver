<?php
    // Simple REST API to calculate and return the Terra Classic circulating supply
 
    require_once('common-defs.php');

    // Retrieve the total supply
    function totalSupply($config, $denom)
    {
        switch($denom)
        {
            case 'lunc':
                // Get the current total supply in uluna
                $tsjson = file_get_contents($config->lcd . TSURI . 'uluna');
                $totalSupply = json_decode($tsjson, false)->amount->amount;
 
                if ($config->debug)
                {
                    echo "DEBUG: ULUNA TS: $totalSupply<br>";
                }
                break;

            case 'ustc':
                // Get the current total supply in uusd
                $tsjson = file_get_contents($config->lcd . TSURI . 'uusd');
                $totalSupply = json_decode($tsjson, false)->amount->amount;
 
                if ($config->debug)
                {
                    echo "DEBUG: UUSD TS: $totalSupply<br>";
                }
                break;

            default:
                $tSupply = 0;
                break;
        }

        // Convert the total supply from micro-denom to denom
        $tSupply = bcdiv($totalSupply, '1000000', 6);

        if ($config->debug)
        {
            echo "DEBUG: " . strtoupper($denom) . " $tSupply<br>";
        } 

        return $tSupply;
    }
?>
