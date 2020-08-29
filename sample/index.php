<?php
include '../diff.php';

$files1 = file_get_contents('files1.txt');
$files2 = file_get_contents('files2.txt');

$result_combine = (new Diff)->compare($files1, $files2, $combine_string = true);

$result = (new Diff)->compare($files1, $files2);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .red {
            background: rgba(180, 60, 80, .6);
            color: #444;
            display: inline-block
        }

        .green {
            background: rgba(60, 180, 80, .6);
            color: #444;
            display: inline-block
        }

        .box {
            width: 200px;
            border: 1px solid #444;
            padding: 20px;
            margin: 20px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <h3>Default Result</h3>

    Origin :
    <div class="box">
        <?= $result['origin']?>
    </div>
    Modified :
    <div class="box">
        <?= $result['modified']?>
    </div>

    <h3>Result with Combinator</h3>

    <div class="box">
        <?= $result_combine?>
    </div>
</body>

</html>