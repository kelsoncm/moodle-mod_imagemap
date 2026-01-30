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
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_imagemap/$filearea/0/$relativepath";

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

    if (empty($area->conditioncmid)) {
        return true;
    }

    $cm = get_coursemodule_from_id(null, $area->conditioncmid);
    
    if (!$cm) {
        return true;
    }

    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $completion = new completion_info($course);
    
    $data = $completion->get_data($cm, false, $userid);
    return $data->completionstate != COMPLETION_INCOMPLETE;
}

/**
 * Get the target URL for an area
 *
 * @param stdClass $area
 * @param int $courseid
 * @return moodle_url|null
 */
function imagemap_get_area_url($area, $courseid) {
    global $CFG;

    switch ($area->linktype) {
        case 'module':
            $cm = get_coursemodule_from_id(null, $area->linktarget);
            if ($cm) {
                return new moodle_url('/mod/' . $cm->modname . '/view.php', array('id' => $area->linktarget));
            }
            return null;
        case 'section':
            return new moodle_url('/course/view.php', array('id' => $courseid, 'section' => $area->linktarget));
        case 'url':
            return new moodle_url($area->linktarget);
        default:
            return null;
    }
}
