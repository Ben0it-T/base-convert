# Base convert

PHP class to convert a number between arbitrary bases or alphabets.

## Usage

```php
use MathUtils\BaseConvert;
$bconv = new BaseConvert();

// Convert 1000 from decimal to hexadecimal
echo $bconv->convert(1000, 10, 8);
// '1750'

// Convert 1000 from decimal to hexadecimal
echo $bconv->convert(1000, 10, 16);
// '3e8'

echo $bconv->convert(1000, 10, "0123456789abcdef");
// '3e8'

// Convert 1000 from decimal to 'alphabet'
$alphabet = "1234abcd";
echo $bconv->convert(1000, 10, $alphabet);
// '2db1'

echo $bconv->convert('2db1', $alphabet, 10);
// '1000'

// 'Integer' bases are converted to alphabet strings.
// Any invalid characters in the number passed for the conversion attempt are silently ignored.
// Converting 100.4 from decimal to hexadecimal will convert 1004 from decimal to hexadecimal.
echo $bconv->convert(100.4, 10, 16);
// '3ec'

echo $bconv->convert(microtime(true), 10, 16);
// something like 'fe215a4979b'

echo $bconv->convert(microtime(true), 10, 62);
// something like '4Xsps2f2'
```
