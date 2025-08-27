<?php
declare(strict_types=1);
namespace MathUtils;

/**
 * Convert a number from one base to another base
 *
 * Notes:
 * - Supports bases from 2 to 62
 * - Any invalid characters in the number passed for the conversion attempt are silently ignored (silently removed)
 *
 */
class BaseConvert {

    private int $min = 2;
    private int $max = 62;
    private string $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    private string $base10_alphabet = '0123456789';

    public function __construct() {}

    /**
     * Convert a number from one base to another base
     *
     * @param string $number The number to convert. Any invalid characters in the number passed for the conversion attempt are silently ignored (silently removed)
     * @param int|string $from_base The base $number is in
     * @param int|string $to_base The base to convert $number to
     *
     * @return string The number converted
     */
    public function convert($number, $from_base, $to_base) {
        $number = strval($number);

        if (is_int($from_base)) {
            if ($from_base < $this->min || $from_base > $this->max) {
                return null;
            }
            if ($from_base < 37) {
                $number = strtoupper($number);
            }
            $from_base = substr($this->alphabet, 0, $from_base);
        }

        if (is_int($to_base)) {
            if ($to_base < $this->min || $to_base > $this->max) {
                return null;
            }
            if ($to_base < 37) {
               $number = strtoupper($number);
            }
            $to_base = substr($this->alphabet, 0, $to_base);
        }

        // Remove any invalid characters passed for attempted conversion
        $pattern = "/[^".$from_base."]/";
        $number = preg_replace($pattern, '', $number);

        return $this->baseConvert($number, $from_base, $to_base);
    }

    private function baseConvert($number, $from_base, $to_base) {
        if ($from_base == $to_base) {
            return $number;
        }

        $number_chars = str_split($number, 1);
        $from_chars = str_split($from_base, 1);
        $to_chars = str_split($to_base, 1);

        $number_len = strlen($number);
        $from_len = strlen($from_base);
        $to_len = strlen($to_base);

        // Convert number from any base to base10
        // Use expanded notation
        // Ex.
        // basé10          : 1234 = ( 1 * 10^3) + ( 2 * 10^2) + ( 3 * 10^1) + ( 4 * 10^0)
        // base16 to base10: 1234 = ( 1 * 16^3) + ( 2 * 16^2) + ( 3 * 16^1) + ( 4 * 16^0) = 4660
        // base16 to base10: abcd = (10 * 16^3) + (11 * 16^2) + (13 * 16^1) + (14 * 16^0) = 43981

        if ($to_base == $this->base10_alphabet) {
            $res = '0';
            $base_len = strval($from_len);
            for ($i=1; $i <= $number_len; $i++) {
                $digit = strval(array_search($number_chars[$i-1], $from_chars));
                $digit_pos = strval($number_len-$i);
                $res = bcadd($res, bcmul($digit, bcpow($base_len, $digit_pos)));
            }
            return $res;
        }


        // Convert number from any base to any base
        // First, converts number from source base to base10
        if ($from_base == $this->base10_alphabet) {
            $base10_number = $number;
        }
        else {
            $base10_number=$this->baseConvert($number, $from_base, $this->base10_alphabet);
        }


        // Then, converts number from base10 to destination/target base
        // Perform divisions with remainder (Euclidean division) until the quotient becomes zero
        // Use the remainders in reverse order
        // Ex. converts 1234 from base10 to base16
        // - 1234 /16  q 77, r 2
        // -   77 /16  q  4, r 13 (d)
        // -    4 /16  q  0, r 4
        // > 4d2

        if ($base10_number < $to_len) {
            return $to_chars[$base10_number];
        }

        $res = '';
        $base_len = strval($to_len);
        while ($base10_number != '0') {
            $res = $to_chars[bcmod($base10_number, $base_len)] . $res;
            $base10_number = bcdiv($base10_number, $base_len, 0);
        }
        return $res;
    }
}
