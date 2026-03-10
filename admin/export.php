<?php
// This file is part of Moodle - http://moodle.org/

/**
 * Export CSS examples endpoint.
 *
 * @package    mod_imagemap
 */

require_once(__DIR__ . '/lib.php');

mod_imagemap_admin_require_access();
mod_imagemap_admin_setup_page('/mod/imagemap/admin/export.php');

require_sesskey();

$allexamples = $DB->get_records('imagemap_css_examples', null, 'type ASC, sortorder ASC, name ASC');

$exportdata = [];
foreach ($allexamples as $example) {
    $exportdata[] = [
        'type' => mod_imagemap_admin_export_type($example->type),
        'name' => $example->name,
        'css_text' => $example->css_text,
    ];
}

$filename = 'imagemap_css_examples_' . date('Y-m-d_H-i-s') . '.json';
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo json_encode($exportdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
