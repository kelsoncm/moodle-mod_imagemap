<?php
// This file is part of Moodle - http://moodle.org/

/**
 * Import form page for CSS examples.
 *
 * @package    mod_imagemap
 */

require_once(__DIR__ . '/lib.php');

mod_imagemap_admin_require_access();
mod_imagemap_admin_setup_page('/mod/imagemap/admin/import_form.php');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('importexamples', 'imagemap'));

echo html_writer::start_tag('form', [
    'method' => 'post',
    'enctype' => 'multipart/form-data',
    'action' => (new moodle_url('/mod/imagemap/admin/import.php'))->out(false),
]);

echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

echo html_writer::start_div('form-group mb-3');
echo html_writer::tag('label', get_string('importfile', 'imagemap'), ['for' => 'importfile', 'class' => 'form-label']);
echo html_writer::empty_tag('input', [
    'type' => 'file',
    'name' => 'importfile',
    'id' => 'importfile',
    'accept' => '.json',
    'class' => 'form-control',
    'required' => 'required',
]);
echo html_writer::tag('small', get_string('importfile_help', 'imagemap'), ['class' => 'form-text text-muted']);
echo html_writer::end_div();

echo html_writer::start_div('form-group');
echo html_writer::tag('button', get_string('import', 'moodle'), ['type' => 'submit', 'class' => 'btn btn-primary']);
echo ' ';
echo html_writer::link(new moodle_url('/mod/imagemap/admin/index.php'), get_string('cancel'), ['class' => 'btn btn-secondary']);
echo html_writer::end_div();

echo html_writer::end_tag('form');

echo $OUTPUT->footer();
