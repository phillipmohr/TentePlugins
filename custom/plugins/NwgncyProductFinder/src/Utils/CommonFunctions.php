<?php declare(strict_types=1);

namespace Nwgncy\ProductFinder\Utils;

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

     public static function extractUnit(string $inputString) {
          $pattern = '/([a-zA-Z]+)\b/';

          preg_match($pattern, $inputString, $matches);
      
          if (is_array($matches) && isset($matches[1])) {
              return $matches[1];
          } else {
              return '';
          }
     }
}