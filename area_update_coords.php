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
 * AJAX handler for updating area coordinates (move/resize).
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
    $areaid = required_param('areaid', PARAM_INT);
    $coords = required_param('coords', PARAM_TEXT);

    $cm = get_coursemodule_from_id('imagemap', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $imagemap = $DB->get_record('imagemap', array('id' => $cm->instance), '*', MUST_EXIST);

    require_login($course, true, $cm);
    $context = context_module::instance($cm->id);
    require_capability('mod/imagemap:manage', $context);

    // Verify area belongs to this imagemap.
    $area = $DB->get_record('imagemap_area', array('id' => $areaid, 'imagemapid' => $imagemap->id), '*', MUST_EXIST);

    // Validate coords format (comma-separated numbers).
    $parts = explode(',', $coords);
    foreach ($parts as $part) {
        if (!is_numeric(trim($part))) {
            throw new moodle_exception('error:invalidcoords', 'imagemap');
        }
    }

    $DB->set_field('imagemap_area', 'coords', $coords, array('id' => $areaid));

    echo json_encode(array('success' => true));
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'error' => $e->getMessage()));
}
