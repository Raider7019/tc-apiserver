<?php
    // Simple REST API to calculate and return the Terra Classic
    // circulating supply

    // Include API modules here
    require_once('./modules/csupply.php');

    // Read the API server configuration
    $cjson = file_get_contents("apiconf.json");
    $config = json_decode($cjson, false);

    // Main API router
    if ($_SERVER['REQUEST_METHOD'] === 'GET')
    {
        // Determine if in debug mode
        $debugMode = (array_key_exists('m', $_GET) && ($_GET['m'] === 'd'));
        
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
