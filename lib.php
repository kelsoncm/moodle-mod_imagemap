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
 * Library of interface functions and constants for module imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function imagemap_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the imagemap into the database
 *
 * @param stdClass $imagemap An object from the form in mod_form.php
 * @param mod_imagemap_mod_form $mform The form instance
 * @return int The id of the newly inserted imagemap record
 */
function imagemap_add_instance(stdClass $imagemap, mod_imagemap_mod_form $mform = null) {
    global $DB;

    $imagemap->timemodified = time();

    $imagemap->id = $DB->insert_record('imagemap', $imagemap);

    // Save files
    if ($mform) {
        $context = context_module::instance($imagemap->coursemodule);
        file_save_draft_area_files($imagemap->image, $context->id, 'mod_imagemap', 'image', 0);
    }

    return $imagemap->id;
}

/**
 * Updates an instance of the imagemap in the database
 *
 * @param stdClass $imagemap An object from the form in mod_form.php
 * @param mod_imagemap_mod_form $mform The form instance
 * @return boolean Success/Fail
 */
function imagemap_update_instance(stdClass $imagemap, mod_imagemap_mod_form $mform = null) {
    global $DB;

    $imagemap->timemodified = time();
    $imagemap->id = $imagemap->instance;

    $result = $DB->update_record('imagemap', $imagemap);

    // Save files
    if ($mform) {
        $context = context_module::instance($imagemap->coursemodule);
        file_save_draft_area_files($imagemap->image, $context->id, 'mod_imagemap', 'image', 0);
    }

    return $result;
}

/**
 * Removes an instance of the imagemap from the database
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function imagemap_delete_instance($id) {
    global $DB;

    if (!$imagemap = $DB->get_record('imagemap', array('id' => $id))) {
        return false;
    }

    // Delete all areas
    $DB->delete_records('imagemap_area', array('imagemapid' => $imagemap->id));

    // Delete all connection lines
    $dbman = $DB->get_manager();
    $linetable = new xmldb_table('imagemap_line');
    if ($dbman->table_exists($linetable)) {
        $DB->delete_records('imagemap_line', array('imagemapid' => $imagemap->id));
    }

    // Delete the instance
    $DB->delete_records('imagemap', array('id' => $imagemap->id));

    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function imagemap_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * Serve the files from the imagemap file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function imagemap_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if ($filearea !== 'image') {
        return false;
    }

    $fs = get_file_storage();
    $itemid = array_shift($args);
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_imagemap/$filearea/$itemid/$relativepath";

    $file = $fs->get_file_by_hash(sha1($fullpath));
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Get all areas for an imagemap instance
 *
 * @param int $imagemapid
 * @return array
 */
function imagemap_get_areas($imagemapid) {
    global $DB;
    return $DB->get_records('imagemap_area', array('imagemapid' => $imagemapid), 'sortorder ASC');
}

/**
 * Check if an area is active based on completion condition
 *
 * @param stdClass $area
 * @param int $userid
 * @return bool
 */
function imagemap_is_area_active($area, $userid) {
    global $DB;

    if ($area->targettype === 'module') {
        $cm = get_coursemodule_from_id(null, (int)$area->targetid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return false;
        }
        $modinfo = get_fast_modinfo($cm->course, $userid);
        $cminfo = $modinfo->get_cm($cm->id);
        return $cminfo->uservisible && $cminfo->available;
    }

    if ($area->targettype === 'section') {
        $sectionrecord = $DB->get_record('course_sections', array('id' => (int)$area->targetid),
            'id, course', IGNORE_MISSING);
        if (!$sectionrecord) {
            return false;
        }
        $modinfo = get_fast_modinfo($sectionrecord->course, $userid);
        $sectioninfo = $modinfo->get_section_info_by_id($sectionrecord->id, IGNORE_MISSING);
        return $sectioninfo && $sectioninfo->uservisible && $sectioninfo->available;
    }

    return false;
}

/**
 * Get the target URL for an area
 *
 * @param stdClass $area
 * @param int $courseid
 * @return moodle_url|null
 */
function imagemap_get_area_url($area, $courseid) {
    if ($area->targettype === 'module') {
        $cm = get_coursemodule_from_id(null, (int)$area->targetid, 0, false, IGNORE_MISSING);
        if ($cm) {
            return new moodle_url('/mod/' . $cm->modname . '/view.php', array('id' => $cm->id));
        }
        return null;
    }

    if ($area->targettype === 'section') {
        $course = get_course($courseid);
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info_by_id((int)$area->targetid, IGNORE_MISSING);
        if ($section) {
            return course_get_url($course, (object)$section, array('navigation' => true));
        }
        return null;
    }

    return null;
}

/**
 * Resolve target data for an area (url, active, tooltip).
 *
 * @param stdClass $area
 * @param stdClass $course
 * @param context_module $context
 * @return array{url:moodle_url|null,active:bool,tooltip:string}
 */
function imagemap_get_area_target_data($area, $course, $context) {
    $data = array('url' => null, 'active' => false, 'tooltip' => '');

    if ($area->targettype === 'module') {
        $cm = get_coursemodule_from_id(null, (int)$area->targetid, $course->id, false, IGNORE_MISSING);
        if (!$cm) {
            return $data;
        }
        $data['url'] = new moodle_url('/mod/' . $cm->modname . '/view.php', array('id' => $cm->id));
        $data['active'] = $cm->available && $cm->uservisible;
        if (!$data['active']) {
            $info = $cm->availableinfo ?? '';
            $tooltip = trim(strip_tags(format_text($info, FORMAT_HTML, array('context' => $context))));
            $data['tooltip'] = $tooltip !== '' ? $tooltip : get_string('arearestricted', 'imagemap');
        }
        return $data;
    }

    if ($area->targettype === 'section') {
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info_by_id((int)$area->targetid, IGNORE_MISSING);
        if (!$section) {
            return $data;
        }
        $data['url'] = course_get_url($course, (object)$section, array('navigation' => true));
        $data['active'] = $section->available && $section->uservisible;
        if (!$data['active']) {
            $info = $section->availableinfo ?? '';
            $tooltip = trim(strip_tags(format_text($info, FORMAT_HTML, array('context' => $context))));
            $data['tooltip'] = $tooltip !== '' ? $tooltip : get_string('arearestricted', 'imagemap');
        }
        return $data;
    }

    return $data;
}

/**
 * Normalize area shape to HTML imagemap compatible value.
 *
 * @param string $shape
 * @return string|null
 */
function imagemap_normalize_shape_for_html_map($shape) {
    if ($shape === 'rect' || $shape === 'rectangle') {
        return 'rect';
    }
    if ($shape === 'circle') {
        return 'circle';
    }
    if ($shape === 'poly' || $shape === 'polygon') {
        return 'poly';
    }
    return null;
}

/**
 * Sanitize coordinate list for HTML imagemap area.
 *
 * @param string $coords
 * @return string
 */
function imagemap_sanitize_coords_for_html_map($coords) {
    if ($coords === '') {
        return '';
    }

    $parts = explode(',', $coords);
    $clean = array();
    foreach ($parts as $part) {
        $value = trim($part);
        if ($value === '' || !is_numeric($value)) {
            continue;
        }
        $number = (float)$value;
        $clean[] = (string)round($number);
    }

    return implode(',', $clean);
}

/**
 * Inject image map preview in course section activity card.
 *
 * @param cm_info $cm
 * @return void
 */
function imagemap_cm_info_view(cm_info $cm) {
    global $DB, $PAGE;
    static $summaryscriptadded = false;

    if (empty($cm->uservisible)) {
        return;
    }

    $imagemap = $DB->get_record('imagemap', array('id' => $cm->instance), 'id, name, course', IGNORE_MISSING);
    if (!$imagemap) {
        return;
    }

    $context = context_module::instance($cm->id);
    if (!has_capability('mod/imagemap:view', $context)) {
        return;
    }

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_imagemap', 'image', 0, 'itemid, filepath, filename', false);
    $imagefile = reset($files);
    if (!$imagefile) {
        return;
    }

    $imageurl = moodle_url::make_pluginfile_url(
        $context->id,
        'mod_imagemap',
        'image',
        $imagefile->get_itemid(),
        $imagefile->get_filepath(),
        $imagefile->get_filename()
    );

    $course = get_course($imagemap->course);
    $areas = imagemap_get_areas($imagemap->id);

    $summaryid = 'imagemap-cm-summary-' . $cm->id;
    $areadata = array();
    $restrictedcount = 0;

    foreach ($areas as $area) {
        $coords = imagemap_sanitize_coords_for_html_map($area->coords);
        if ($coords === '') {
            continue;
        }

        $targetdata = imagemap_get_area_target_data($area, $course, $context);
        $isactive = (bool)$targetdata['active'];
        if (!$isactive) {
            $restrictedcount++;
        }

        $shape = imagemap_normalize_shape_for_html_map($area->shape);
        if (!$shape) {
            continue;
        }

        $areadata[] = array(
            'id' => (int)$area->id,
            'shape' => $shape,
            'coords' => $coords,
            'title' => $area->title,
            'url' => !empty($targetdata['url']) ? $targetdata['url']->out(false) : '',
            'active' => $isactive,
            'tooltip' => $targetdata['tooltip'],
            'activefilter' => $area->activefilter ?: 'none',
            'inactivefilter' => $area->inactivefilter ?: 'grayscale(1) opacity(0.5)'
        );
    }

    $summarydata = array(
        'id' => $summaryid,
        'imageurl' => $imageurl->out(false),
        'areas' => $areadata,
    );
    $encodedsummary = json_encode($summarydata);

    $imageattrs = array(
        'src' => $imageurl->out(false),
        'alt' => format_string($imagemap->name),
        'loading' => 'lazy',
        'class' => 'mod-imagemap-summary-image',
        'style' => 'width:100%;height:auto;display:block;border-radius:4px;'
    );
    $content = html_writer::start_div('mod-imagemap-course-summary', array(
        'id' => $summaryid,
        'data-imagemap-summary' => $encodedsummary,
        'style' => 'margin-top:.5rem;width:100%;position:relative;'
    ));
    $content .= html_writer::empty_tag('img', $imageattrs);
    $content .= html_writer::div('', 'mod-imagemap-summary-overlays', array(
        'style' => 'position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;'
    ));

    if ($restrictedcount > 0) {
        $content .= html_writer::div(
            get_string('coursepreviewrestricted', 'imagemap', $restrictedcount),
            'text-muted small',
            array('style' => 'margin-top:.25rem;')
        );
    }

    $content .= html_writer::end_div();

    if (!$summaryscriptadded) {
        $content .= html_writer::script("require(['mod_imagemap/summary'], function(summary) { if (summary && summary.init) { summary.init(); } });");
        $summaryscriptadded = true;
    }

    $cm->set_after_link($content);

    $PAGE->requires->js_call_amd('mod_imagemap/summary', 'init');
}