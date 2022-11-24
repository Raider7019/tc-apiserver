<?php
    require_once("common-defs.php");

    // Retieve the community pool amount for the specified denom
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

    function nTrim($number, $precision = 2, $separator = '.')
    {
        $numberParts = explode($separator, $number);
        $response = $numberParts[0];
        if (count($numberParts)>1 && $precision > 0)
        {
            $response .= $separator;
            $response .= substr($numberParts[1], 0, $precision);
        }
        return $response;
    }
?>
