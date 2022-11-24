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
                if ($config->calc_mode > 0)
                {
                    if ($config->calc_mode == 1)
                    {
                        // Calculate circulating supply as:
                        // total_supply - bonded_tokens - community_pool

                        // Get the current total supply
                        $tsjson = file_get_contents($config->lcd . TSURI . "uluna");
                        $totalSupply = json_decode($tsjson, false)->amount->amount;

                        // Get the community pool uluna amount
                        $communityPool = getCpool($config, "uluna");
                    }
                    else
                    {
                        // Calculate circulating supply as:
                        // FCD circulating_supply - bonded_tokens

                        // Get the FCD circulating supply in LUNC
                        $fCSupply = file_get_contents($config->fcd . CSURI . "luna");
                        // Round to 6 decimal places and convert to uluna
                        $totalSupply = bcmul(round($fCSupply, 6, PHP_ROUND_HALF_DOWN), '1000000'); 
                        // FCD figure already includes the community pool
                        $communityPool = 0;
                    }

                    // Retrieve staking data
                    $stjson = file_get_contents($config->lcd . STURI);
                    $bondedTokens = json_decode($stjson, false)->pool->bonded_tokens;

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
                    $fCSupply = file_get_contents($config->fcd . CSURI . "luna");
                    // Trim to 6 decimals
                    $cSupply = nTrim($fCSupply, 6, '.');

                    if ($config->debug)
                    {
                        echo "DEBUG: ULUNA FCS: $fCSupply CS: $cSupply<br>";
                    }   
                }
                break;

            case 'ustc':
                if ($config->calc_mode > 0)
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
                    $cSupply = file_get_contents($config->fcd . CSURI . "ust");
                }
                break;

            default:
                $cSupply = 0;
                break;
        }

        return $cSupply;
    }
?>
