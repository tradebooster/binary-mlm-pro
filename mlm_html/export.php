<?php

$filename = $_POST['filename'] . '-export-' . date('YmdHis') . '.csv';
header("Content-type: text/csv");
header("Cache-Control: no-store, no-cache");
header('Content-Disposition: attachment; filename="' . $filename . '"');
$outstream = fopen("php://output", 'w');
$values = $_POST['listarray'];
$data = unserialize($values);
foreach ($data as $row) {
    foreach ($row as &$column) {
        $column = '="' . $column . '"';
    }
    unset($column);
    fputcsv($outstream, $row, ',', '"');
}
fclose($outstream);
?>