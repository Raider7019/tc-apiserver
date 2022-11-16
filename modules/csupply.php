<?php
    // Simple REST API to calculate and return the Terra Classic circulating supply
    
    // Definitions
    define("TSURI", "/cosmos/bank/v1beta1/supply/uluna");
    define("STURI", "/cosmos/staking/v1beta1/pool");
    define("CPURI", "/cosmos/distribution/v1beta1/community_pool");

    // Calculate the circulating supply, taking into account staking
    function circulatingSupply($config)
    {
        // Get the current total supply
        $tsjson = file_get_contents($config->lcd . TSURI);
        $totalSupply = json_decode($tsjson, false)->amount->amount;

        // Retrieve staking data
        $stjson = file_get_contents($config->lcd . STURI);
        $bondedTokens = json_decode($stjson, false)->pool->bonded_tokens;

        // Get the community pool uluna amount
        $communityPool = getCpool($config, "uluna");

        if ($config->debug)
        {
            echo "DEBUG: TS: $totalSupply BT: $bondedTokens CP: $communityPool <br>";
        }
        
        // Calculate the revised circulating supply in luna
        $cSupply = bcdiv(bcsub(bcsub($totalSupply, $bondedTokens), $communityPool), '1000000', 6);

        return $cSupply;
    }

    // Retieve the community pool uluna amount
    function getCpool($config, $denom)
    {
        // Get the array of community pool denoms
        $cpjson = file_get_contents($config->lcd . CPURI);
        $cpDenoms = json_decode($cpjson, false)->pool;     

        // Find the amount for the specified denom
        foreach ($cpDenoms as $cpPair)
        {
            if ($cpPair->denom === $denom)
            {
                return (string) intval($cpPair->amount);
            }
        }
        return 0;
            
    }
?>
