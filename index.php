<?php
    // Simple REST API to calculate and return the Terra Classic
    // circulating supply

    // Include API modules here
    require_once('./modules/csupply.php');
    
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
        
        // Remove any trailing / from the API name and convert to lowercase rtrim($route, "/")
        $api = basename(strtolower($route));
        
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
        header('HTTP/1.1 403 Forbidden');
    }
?>
