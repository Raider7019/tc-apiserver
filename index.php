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
            // Retrieve the API route components
            $route = $_GET['route'];
            $leaf = basename($route);
        }
        else
        {
            // The route parameter is missing
            $route = '';
            $leaf = '';
        }
        
        // Convert API route to lowercase
        $api = strtolower($route);
        
        // Handle the different routes
        switch($api)
        {
            case 'tsupply':
            case 'tsupply/lunc':
                // Render the LUNC total supply value as raw text
                echo totalSupply($config, 'lunc');
                break;
                
            case 'csupply':
            case 'csupply/lunc':
                // Render the LUNC circulating supply value as raw text
                echo circulatingSupply($config, 'lunc');
                break;

            case 'tsupply/ustc':
                // Render the USTC  total supply value as raw text
                echo totalSupply($config, 'ustc');
                break;
               
            case 'csupply/ustc':
                // Render the USTC circulating supply value as raw text
                echo circulatingSupply($config, 'ustc');
                break;

            case '':
                echo "Invalid request";
                break;
                
            default:
                echo "Invalid API '" . $leaf . "'";
                break;
        }
    }
    else
    {
        header('HTTP/1.1 403 Forbidden');
    }
?>
