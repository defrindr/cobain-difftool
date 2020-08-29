# Diff-tool

Simple tool to compare 2 string.

## installation

1. Clone this repository
```bash
git clone https://github.com/defrindr/diff-tool
```

2. Move ```diff.php``` to directory project

3. And call it, if you need it.


## List Function


<table>
    <thead>
        <th>Name</th>
        <th>Params</th>
        <th>Description</th>
        <th>Return</th>
    </thead>
    <tbody>
        <tr>
            <td rowspan="3">Compare</td>
            <td>$origin_string</td>
            <td>
                Contain origin text, this param can`t be blank
            </td>
            <td rowspan="3">Array</td>
        </tr>
        <tr>
            <td>$modified_text</td>
            <td>
                Contain modification text, this param can`t be blank
            </td>
        </tr>
        <tr>
            <td>$combine_string</td>
            <td>
                default value is false
            </td>
        </tr>
        <tr>
            <td rowspan="3">setDefaultTemplate</td>
            <td>$deleted_template</td>
            <td>-</td>
            <td rowspan="3">object</td>
        </tr>
        <tr>
            <td>$added_template</td>
            <td>-</td>
        </tr>
    </tbody>
</table>

## Example

### Simple Usage
```php
<?php
include 'diff.php';

$first_string = "this is example";
$another_string = "what is example";

$result = (new Diff)->compare($first_string, $another_string);

print_r($result);

// Output :
// Array
// (
//     [origin] => <div class='red'>this</div> is example
//     [modified] => <div class='geen'>what</div> is example
// )

```

### With Combine
```php
<?php
include 'diff.php';

$first_string = "this is example";
$another_string = "what is example";

$result = (new Diff)->compare($first_string, $another_string, $with_combine = true);

print_r($result);

// Output :
// <div class='red'>this</div><div class='geen'>what</div> is example

```

### Customize Template
```php
<?php
include 'diff.php';

$first_string = "this is example";
$another_string = "what is example";

$deleted = "<deleted>[text]</deleted>";
$added = "<added>[text]</added>";


$result = (new Diff)
            ->setDefaultTemplate($deleted, $added)
            ->compare($first_string, $another_string, $with_combine = true);

print_r($result);

// Output :
// <deleted>this</deleted><added>what</added> is example
```

### Run sample

To run simple example.

1. Go to repository dir

2. Run this command
```bash
./run
```

### Screen Shot

<img src="./assets/screenshot.png" style="width: auto;display: block;margin: auto" alt="thank you">
