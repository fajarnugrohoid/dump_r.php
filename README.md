dump_r()
========
a cleaner, leaner mix of `print_r()` and `var_dump()` _(MIT Licensed)_

![screenshot](https://github.com/leeoniya/dump_r.php/raw/master/test/dump_r.png)

### Demo: http://o-0.me/dump_r/

### Installing

__Composer__

https://packagist.org/packages/leeoniya/dump-r

```json
{
	"require": {
		"leeoniya/dump-r": "dev-master"
	}
}
```

__Require__

```php
require 'dump_r.php';
```

### Using & Config

Use `dump_r()` as a drop-in replacement for `print_r()` and `var_dump()`. It has some additional arguments that control output. The full signature of the function is:

```php
function dump_r($value, $return = false, $html = true, $depth = 1e3, $expand = 1e3);
```

- `$value` is the thing you want to dump
- `$return` determines whether to return the dump rather than output to screen
- `$html` controls whether the output is text or html
- `$depth` sets the recursion limit for the dump
- `$expand` sets the auto-expanded child node depth

There are also two modifier keys that can be used to control how the node expanding/collapsing works:

1. Holding `Shift` while toggling will expand/collapse the full depth of the node.
2. Hold `Ctrl` while toggling will expand/collapse all siblings after that node also. This is useful if you have an array of objects/arrays and want to expand all of them to one level simultaneously by clicking just the first one in the group. It works well for deep, complex objects.
3. `Shift` and `Ctrl` can be used together.

Some types of strings can be pretty-printed and additonal rendering options can be tweaked (shown with defaults):

```php
dump_r\Rend::$xml_pretty	= false;	// pretty-print xml strings
dump_r\Rend::$json_pretty	= false;	// pretty-print json strings
dump_r\Rend::$sql_pretty	= false;	// pretty-print sql strings (requires https://github.com/jdorn/sql-formatter)
dump_r\Rend::$recset_tbls	= true;		// recordset detection & table-style output
dump_r\Rend::$val_space		= 4;		// number of spaces between key and value columns (affects text output only, not html)
```

Circular reference (recursion) detection and duplicate output is indicated like this for arrays, objects, closures and resources respectively: `[*]`,`{*}`,`(*)`,`<*>`.

You can re-style all aspects of the html output using CSS, everything is class-tagged.

### Extending

Adding your own classifiers & parsers is extremely easy. Here are instructions and two concrete examples of how the `String` base type can be subtyped. First for displaying EXIF data of `jpeg` and `tiff` image paths and then showing row data from CSV file paths.

This array

```php
$stuff = [
	'imgs/img_1771.jpg',
	'data/people.csv',
];
```

Which would normally dump like this:

![coretyped](https://github.com/leeoniya/dump_r.php/raw/master/test/coretyped.png)

Can be dumped like this with subtyping:

![usertyped](https://github.com/leeoniya/dump_r.php/raw/master/test/usertyped.png)

To do this, hook the correct core type and provide a function that classifies and processes the raw value, returning an instance of `UserType` instantiated with these 3 arguments:

1. `$types` - Array of subtype string(s) of your choice. These get appended as CSS classes and are also displayed inline.
2. `$nodes` - Array of expandable subnodes to display. Provide `null` if no subnodes are needed or to retain any subnodes extracted by the core type.
3. `$length` - A string to be displayed at the end of the line, indicating length of subnodes. You can also abuse this param to display other length-esque information (the EXIF example below uses it to display image dimensions inline). Provide `null` to retain the default length display for the hooked core type.

```php
use dump_r\Type;
use dump_r\UserType;

// Example 1: dump EXIF data with image filepath strings

Type::hook('String', function($raw, $type) {
	// match path-esque strings (containing '/' or '\') trailed by an
	// EXIF-capable image extension, then verify this file actually exists
	if (preg_match('#[\/]+.+\.(jpe?g|tiff?)$#', $raw) && is_file($raw)) {
		$types = ['image'];
		$nodes = $exif = exif_read_data($raw, 0, true);
		$length = $exif['COMPUTED']['Width'] . 'x' . $exif['COMPUTED']['Height'];

		return new UserType($types, $nodes, $length);
	}
});

// Example 2: dump CSV records with csv filepath strings

Type::hook('String', function($raw, $type) {
	if (preg_match('#[\/]+.+\.csv$#', $raw) && is_file($raw)) {
		$types = ['csv'];
		$nodes = csv2array($raw);
		$length = count($nodes);
		return new UserType($types, $nodes, $length);
	}
});

function csv2array($file) {
	$csv = array();
	$rows = array_map('str_getcsv', file($file));
	$header = array_shift($rows);
	foreach ($rows as $row)
		$csv[] = array_combine($header, $row);
	return $csv;
}
```

All core types (see `src/dump_r/Type` dir) can be hooked by their fully namespaced names. For example, if you wanted to further subtype a JSON object string, you would use

```php
Type::hook('String\\JSON\\Object', function($raw, $type) {
	// code here
});
```