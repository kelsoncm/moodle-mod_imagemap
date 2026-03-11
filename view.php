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
 * Prints a particular instance of imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT); // Course module ID.

if ($id) {
    $cm = get_coursemodule_from_id('imagemap', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $imagemap = $DB->get_record('imagemap', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    throw new moodle_exception('missingparameter');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/imagemap:view', $context);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$event = \mod_imagemap\event\course_module_viewed::create(['objectid' => $imagemap->id, 'context' => $context]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('imagemap', $imagemap);
$event->trigger();

$PAGE->set_url('/mod/imagemap/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($imagemap->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Get the image file.
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_imagemap', 'image', 0, 'itemid, filepath, filename', false);
$imagefile = reset($files);

echo $OUTPUT->header();

if ($imagefile) {
    // Render content with JavaScript initialization.
    $content = imagemap_render_content_with_script($imagemap, $cm, $context, $course, $cm, $imagefile);
    echo $content;
}

echo $OUTPUT->footer();
