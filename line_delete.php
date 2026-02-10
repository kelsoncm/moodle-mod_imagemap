<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * AJAX handler for deleting a connection line.
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

header('Content-Type: application/json');

try {
    require_sesskey();

    $cmid = required_param('cmid', PARAM_INT);
    $lineid = required_param('lineid', PARAM_INT);

    $cm = get_coursemodule_from_id('imagemap', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, true, $cm);
    $context = context_module::instance($cm->id);
    require_capability('mod/imagemap:manage', $context);

    // Get line and verify it belongs to this imagemap.
    $line = $DB->get_record('imagemap_line', array('id' => $lineid), '*', MUST_EXIST);
    $imagemap = $DB->get_record('imagemap', array('id' => $cm->instance), '*', MUST_EXIST);

    if ((int)$line->imagemapid !== (int)$imagemap->id) {
        throw new moodle_exception('invalidrecord');
    }

    $DB->delete_records('imagemap_line', array('id' => $lineid));

    echo json_encode(array('success' => true));
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'error' => $e->getMessage()));
}
