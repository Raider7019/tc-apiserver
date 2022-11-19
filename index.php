<?php
    // Simple REST API router

    // Include API modules here
    require_once('./modules/tsupply.php');
    require_once('./modules/csupply.php');

    // Read the API server configuration
    $cjson = file_get_contents("apiconf.json");
    $config = json_decode($cjson, false);

    // Main API router
    if ($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        // Determine the API route
        if (array_key_exists('route', $_GET))
        {
            // Retrieve the API route
            $route = basename($_GET['route']);
        }
        else
        {
            // The route parameter is missing
            $route = '';
        }
        
        // Convert API route to lowercase
        $api = strtolower($route);
        
        // Handle the different routes
        switch($api)
        {
            case 'tsupply':
                // Render the total supply value as raw text
                echo totalSupply($config);
                break;
                
            case 'csupply':
                // Render the circulating supply value as raw text
                echo circulatingSupply($config);
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
