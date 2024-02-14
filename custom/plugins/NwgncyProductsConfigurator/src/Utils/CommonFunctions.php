<?php declare(strict_types=1);

namespace Nwgncy\ProductsConfigurator\Utils;

class CommonFunctions
{
     public static function parsefloatFromString(string $input) {
          $matches = [];
          if (preg_match('/[-+]?[0-9,]*\.?[0-9]+|[0-9]*,?[0-9]+/', $input, $matches)) {
               $input = $matches[0];
               $commaCount = substr_count($input, ',');
               $dotCount = substr_count($input, '.');
          
               if ($dotCount > 0 && $commaCount > 0) {
                    $input = str_replace(',', '', $input);
               } else {
                    $input = str_replace(',', '.', $input);
               }

               $input = str_replace(',', '', $input);

               $result = floatval($input);

               return $result;
          }
          return null;
     }
}