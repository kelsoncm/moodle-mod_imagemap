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

$id = optional_param('id', 0, PARAM_INT); // Course module ID

if ($id) {
    $cm = get_coursemodule_from_id('imagemap', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $imagemap = $DB->get_record('imagemap', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('missingparameter');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/imagemap:view', $context);

$event = \mod_imagemap\event\course_module_viewed::create(array(
    'objectid' => $imagemap->id,
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('imagemap', $imagemap);
$event->trigger();

$PAGE->set_url('/mod/imagemap/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($imagemap->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Get the image file
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_imagemap', 'image', 0, 'itemid, filepath, filename', false);
$imagefile = reset($files);

echo $OUTPUT->header();

$areadata = array();

if ($imagefile) {
    $imageurl = moodle_url::make_pluginfile_url(
        $context->id,
        'mod_imagemap',
        'image',
        $imagefile->get_itemid(),
        $imagefile->get_filepath(),
        $imagefile->get_filename()
    );
    
    // Get areas
    $areas = imagemap_get_areas($imagemap->id);
    
    // Prepare area data for JavaScript
    $areadata = array();
    foreach ($areas as $area) {
        $isactive = imagemap_is_area_active($area, $USER->id);
        $url = imagemap_get_area_url($area, $course->id);
        
        $areadata[] = array(
            'shape' => $area->shape,
            'coords' => $area->coords,
            'title' => $area->title,
            'url' => $url ? $url->out() : '',
            'active' => $isactive,
            'activefilter' => $area->activefilter ?: 'none',
            'inactivefilter' => $area->inactivefilter ?: 'grayscale(1) opacity(0.5)'
        );
    }

    // Prepare template data
    $template_data = array(
        'has_image' => !empty($imagefile),
        'imagemap_id' => $imagemap->id,
        'has_manage' => has_capability('mod/imagemap:manage', $context),
        'manage_url' => new moodle_url('/mod/imagemap/areas.php', array('id' => $cm->id)),
        'manage_text' => get_string('managereas', 'imagemap'),
        'no_image_text' => get_string('error:noimage', 'imagemap'),
        'edit_settings_url' => new moodle_url('/course/modedit.php', array('update' => $cm->id, 'return' => 1)),
        'edit_settings_text' => get_string('editsettings', 'moodle'),
    );

    echo $OUTPUT->render_from_template('mod_imagemap/view', $template_data);
    
    if (!empty($imagefile)) {
        // Add JavaScript to render the canvas imagemap with CSS overlays
        $PAGE->requires->js_call_amd('mod_imagemap/view', 'init', array($imagemap->id, $areadata, $imageurl->out()));
    }

}

echo $OUTPUT->footer();
