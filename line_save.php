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
 * AJAX handler for saving a connection line between two areas.
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
    $imagemapid = required_param('imagemapid', PARAM_INT);
    $fromareaid = required_param('from_areaid', PARAM_INT);
    $toareaid = required_param('to_areaid', PARAM_INT);

    $cm = get_coursemodule_from_id('imagemap', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

    require_login($course, true, $cm);
    $context = context_module::instance($cm->id);
    require_capability('mod/imagemap:manage', $context);

    // Validate areas exist and belong to this imagemap.
    $from = $DB->get_record('imagemap_area', ['id' => $fromareaid, 'imagemapid' => $imagemapid], '*', MUST_EXIST);
    $to = $DB->get_record('imagemap_area', ['id' => $toareaid, 'imagemapid' => $imagemapid], '*', MUST_EXIST);

    // Check for duplicate.
    $exists = $DB->record_exists('imagemap_line', [
        'imagemapid' => $imagemapid,
        'from_areaid' => $fromareaid,
        'to_areaid' => $toareaid,
    ]);

    if ($exists) {
        echo json_encode(['success' => false, 'error' => get_string('line_duplicate', 'imagemap')]);
        die();
    }

    $record = new stdClass();
    $record->imagemapid = $imagemapid;
    $record->from_areaid = $fromareaid;
    $record->to_areaid = $toareaid;
    $record->timecreated = time();

    $id = $DB->insert_record('imagemap_line', $record);

    echo json_encode(['success' => true, 'id' => (int)$id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
