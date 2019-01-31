# php-daisydiff

[![PHP Version](https://img.shields.io/packagist/php-v/snebes/php-daisydiff.svg?maxAge=3600)](https://packagist.org/packages/snebes/php-daisydiff)
[![Latest Version](https://img.shields.io/packagist/v/snebes/php-daisydiff.svg?maxAge=3600)](https://packagist.org/packages/snebes/php-daisydiff)
[![Build Status](https://img.shields.io/scrutinizer/build/g/snebes/php-daisydiff.svg?maxAge=3600)](https://scrutinizer-ci.com/g/snebes/php-daisydiff)
[![Code Quality](https://img.shields.io/scrutinizer/g/snebes/php-daisydiff.svg?maxAge=3600)](https://scrutinizer-ci.com/g/snebes/php-daisydiff)
[![Test Coverage](https://img.shields.io/scrutinizer/coverage/g/snebes/php-daisydiff.svg?maxAge=3600)](https://scrutinizer-ci.com/g/snebes/php-daisydiff)

Daisy Diff is a PHP implementation of the [Java library](https://github.com/DaisyDiff/DaisyDiff) that diffs (compares) HTML files. It highlights added and removed words and annotates changes to the styling.

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require snebes/php-daisydiff
```

### Usage

The `DaisyDiff` class can be used to generate a textual representation of the difference between two HTML strings:

```php
<?php

use SN\DaisyDiff\DaisyDiff;

$original = '<html><body>The original document</body></html>';
$modified = '<html><body>The changed document</body></html>';

$daisyDiff = new DaisyDiff();
\printf("%s\n", $daisyDiff->diff($original, $modified));
```

The code above yields the output below:

```html
<html>The <del class="diff-html-removed">original </del><ins class="diff-html-added">changed </ins>document</html>
```
## Thanks

Many thanks to:

- [Java DaisyDiff](https://github.com/DaisyDiff/DaisyDiff), the original Java version of the DaisyDiff library.
- [gdevanla/assist](https://github.com/gdevanla/assist) from which many of the tests of this library are extracted.
