<?php
// This file is part of Moodle - http://moodle.org/

/**
 * Delete CSS example endpoint.
 *
 * @package    mod_imagemap
 */

require_once(__DIR__ . '/lib.php');

mod_imagemap_admin_require_access();
mod_imagemap_admin_setup_page('/mod/imagemap/admin/delete.php');

$id = required_param('id', PARAM_INT);
require_sesskey();

$DB->delete_records('imagemap_css_examples', ['id' => $id]);

redirect(
    new moodle_url('/mod/imagemap/admin/index.php'),
    get_string('exampledeleted', 'imagemap'),
    null,
    \core\output\notification::NOTIFY_SUCCESS
);
