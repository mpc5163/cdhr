<?php

require '../vendor/autoload.php';

use YExcel\Excel;

$data = [
    ["a1", "a2", "a3", "a4", "a5", "a6", "a7", "a8", "a9", "a10"],
    ["b1", "b2", "b3", "b4", "b5", "b6", "b7", "b8", "b9", "b10"],
    ["c1", "c2", "c3", "c4", "c5", "c6", "c7", "c8", "c9", "c10"],
    ["d1", "d2", "d3", "d4", "d5", "d6", "d7", "d8", "d9", "d10"],
];

$generator = function ($data) {
    foreach ($data as $row) {
        yield $row;
    }
};


Excel::put('test1.xlsx', $data, null, 0);

Excel::put('test2.xlsx', $generator($data), 'template.xlsx', 2);

Excel::put('test3.xlsx', $data, null, 5);

$tmp = [];
foreach (Excel::get('test3.xlsx', null, [1, 2], false) as $row) {
    $tmp[] = $row;
}

echo expectTrue(count($tmp) === count($data) + 3, 'skip blank row 0');

$tmp = [];
foreach (Excel::get('test3.xlsx', null, [1], true) as $row) {
    $tmp[] = $row;
}

echo expectTrue(count($tmp) === count($data), 'skip blank row 1');

echo "\n";

foreach (Excel::get('test2.xlsx', null, []) as $row) {
    echo "\n".implode("\t", $row);
}

function expectTrue($flag, $name)
{
    return "\n".($flag ? '√' : '×').' '.$name;
}
