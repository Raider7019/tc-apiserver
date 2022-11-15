<?php
    // Simple REST API to calculate and return the Terra Classic circulating supply
    
    // Definitions
    define("TSURI", "/cosmos/bank/v1beta1/supply/uluna");
    define("STURI", "/cosmos/staking/v1beta1/pool");
    define("CPURI", "/cosmos/distribution/v1beta1/community_pool");

    // Read the API server configuration
    $cjson = file_get_contents("apiconf.json");
    $config = json_decode($cjson, false);

    // Main API router
    if ($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        // Determine if in debug mode
        $debugMode = $_GET['m'] === 'd';
        
        // Retieve API route
        $route = $_GET['route'];
        
        // Remove any trailing / from the API name and convert to lowercase
        $api = strtolower(rtrim($route, "/"));
        
        // Handle the different routes
        switch($api)
        {
            case 'csupply':
                // Render the circulating supply value as raw text
                echo circulatingSupply($config, $debugMode);
                break;
               
            case '':
                echo "Invalid request";
                break;
                
            default:
                echo "Invalid API '" . $route . "'";
                break;
        }
    }
    else
    {
        header('HTTP/1.0 403 Forbidden');
    }

    // Calculate the circulating supply, taking into account staking
    function circulatingSupply($config, $debugMode)
    {
        // Get the current total supply
        $tsjson = file_get_contents($config->lcd . TSURI);
        $totalSupply = json_decode($tsjson, false)->amount->amount;

        // Retrieve staking data
        $stjson = file_get_contents($config->lcd . STURI);
        $bondedTokens = json_decode($stjson, false)->pool->bonded_tokens;

        // Get the community pool uluna amount
        $communityPool = getCpool($config, "uluna");

        if ($debugMode)
        {
            echo "DEBUG: TS: " . $totalSupply . " BT: " . $bondedTokens . " CP: " . $communityPool . "<br>";
        }
        
        // Calculate the revised circulating supply in luna
        $cSupply = bcdiv(bcsub(bcsub($totalSupply, $bondedTokens), $communityPool), 1000000, 6);

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
