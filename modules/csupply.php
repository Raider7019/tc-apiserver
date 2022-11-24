<?php
    // Simple REST API to calculate and return the Terra Classic circulating supply
    
    require_once('common-defs.php');
    require_once('common-func.php');

    // Calculate the circulating supply, taking into account staking
    function circulatingSupply($config, $denom)
    {
        switch($denom)
        {
            case 'lunc':
                if (!$config->legacy
                {
                    // Get the current total supply
                    $tsjson = file_get_contents($config->lcd . TSURI . "uluna");
                    $totalSupply = json_decode($tsjson, false)->amount->amount;

                    // Retrieve staking data
                    $stjson = file_get_contents($config->lcd . STURI);
                    $bondedTokens = json_decode($stjson, false)->pool->bonded_tokens;

                    // Get the community pool uluna amount
                    $communityPool = getCpool($config, "uluna");

                    if ($config->debug)
                    {
                        echo "DEBUG: ULUNA TS: $totalSupply BT: $bondedTokens CP: $communityPool<br>";
                    }   
        
                    // Calculate the revised circulating supply in luna
                    $cSupply = bcdiv(bcsub(bcsub($totalSupply, $bondedTokens), $communityPool), '1000000', 6);
                }
                else
                {
                    // Return the legacy FCD circulating supply figure
                    cSupply = file_get_contents($config->fcd . CSURI . "lunc");
                }
                break;

            case 'ustc':
                if (!$config->legacy)
                {
                    // Get the current total supply
                    $tsjson = file_get_contents($config->lcd . TSURI . "uusd");
                    $totalSupply = json_decode($tsjson, false)->amount->amount;

                    // Get the community pool uluna amount
                    $communityPool = getCpool($config, "uusd");

                    if ($config->debug)
                    {
                        echo "DEBUG: UUSD TS: $totalSupply CP: $communityPool<br>";
                    }   
        
                    // Calculate the revised circulating supply in ustc
                    $cSupply = bcdiv(bcsub($totalSupply, $communityPool), '1000000', 6);
                }
                else
                {
                    // Return the legacy FCD circulating supply figure
                    cSupply = file_get_contents($config->fcd . CSURI . "ust");
                }
                break;

            default:
                $cSupply = 0;
                break;
        }

        return $cSupply;
    }
?>
