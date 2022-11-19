<?php
    // Simple REST API to calculate and return the Terra Classic circulating supply
    
    // Definitions
    define("TSURI", "/cosmos/bank/v1beta1/supply/uluna");
 
    // Retrieve the total supply
    function totalSupply($config)
    {
        // Get the current total supply in uluna
        $tsjson = file_get_contents($config->lcd . TSURI);
        $totalSupply = json_decode($tsjson, false)->amount->amount;
      
        if ($config->debug)
        {
            echo "DEBUG: TS: $totalSupply";
        }
        
        // Calculate the total supply in LUNC
        $tSupply = bcdiv($totalSupply, '1000000', 6);

        return $tSupply;
    }
?>
