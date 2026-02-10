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
 * Manage imagemap areas
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT); // Course module ID
$action = optional_param('action', '', PARAM_ALPHA);
$areaid = optional_param('areaid', 0, PARAM_INT);

$cm = get_coursemodule_from_id('imagemap', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$imagemap = $DB->get_record('imagemap', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/imagemap:manage', $context);

$PAGE->set_url('/mod/imagemap/areas.php', array('id' => $cm->id));
$PAGE->set_title(format_string($imagemap->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/imagemap/editor.css');

// Handle actions
if ($action === 'delete' && $areaid && confirm_sesskey()) {
    // Delete related lines first
    $dbman_check = $DB->get_manager();
    $linetable_check = new xmldb_table('imagemap_line');
    if ($dbman_check->table_exists($linetable_check)) {
        $DB->delete_records_select('imagemap_line',
            'from_areaid = :from OR to_areaid = :to',
            array('from' => $areaid, 'to' => $areaid)
        );
    }
    $DB->delete_records('imagemap_area', array('id' => $areaid, 'imagemapid' => $imagemap->id));
    redirect(new moodle_url('/mod/imagemap/areas.php', array('id' => $cm->id)), 
             get_string('deletearea', 'imagemap'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managereas', 'imagemap'));

// Get the image file
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_imagemap', 'image', 0, 'itemid, filepath, filename', false);
$imagefile = reset($files);

$templatedata = array();

if ($imagefile) {
    $imageurl = moodle_url::make_pluginfile_url(
        $context->id,
        'mod_imagemap',
        'image',
        $imagefile->get_itemid(),
        $imagefile->get_filepath(),
        $imagefile->get_filename()
    );
    
    // Get existing areas
    $areas = imagemap_get_areas($imagemap->id);
    $areasdata = array();
    $areasfortemplate = array();
    
    foreach ($areas as $area) {
        $areasdata[] = array(
            'id' => (int)$area->id,
            'shape' => $area->shape,
            'coords' => $area->coords,
            'title' => $area->title,
            'linktype' => $area->linktype,
            'linktarget' => $area->linktarget,
            'conditioncmid' => (int)$area->conditioncmid,
            'activefilter' => $area->activefilter,
            'inactivefilter' => $area->inactivefilter
        );
        
        $deleteurl = new moodle_url('/mod/imagemap/areas.php', array(
            'id' => $cm->id,
            'action' => 'delete',
            'areaid' => $area->id,
            'sesskey' => sesskey()
        ));
        
        $areasfortemplate[] = array(
            'id' => (int)$area->id,
            'title' => s($area->title),
            'shape' => s($area->shape),
            'linktype_display' => get_string('linktype_' . $area->linktype, 'imagemap'),
            'delete_url' => $deleteurl->out(),
            'delete' => get_string('delete'),
            'lines_count' => 0
        );
    }
    
    // Fetch CSS examples for active and inactive filters
    $active_examples = $DB->get_records('imagemap_css_examples', array('type' => 'active'), 'sortorder ASC, name ASC');
    $inactive_examples = $DB->get_records('imagemap_css_examples', array('type' => 'inactive'), 'sortorder ASC, name ASC');
    
    $active_examples_template = array();
    $inactive_examples_template = array();
    
    foreach ($active_examples as $example) {
        $active_examples_template[] = array(
            'id' => $example->id,
            'name' => $example->name,
            'css' => $example->css
        );
    }
    
    foreach ($inactive_examples as $example) {
        $inactive_examples_template[] = array(
            'id' => $example->id,
            'name' => $example->name,
            'css' => $example->css
        );
    }
    
    // Get completion modules organized by sections
    $modinfo = get_fast_modinfo($course);
    $sections_with_modules = array();
    $all_modules_flat = array(); // For autocomplete search
    
    foreach ($modinfo->get_section_info_all() as $section) {
        $section_modules = array();
        
        foreach ($modinfo->get_cms() as $modcm) {
            if ($modcm->sectionnum == $section->section && 
                $modcm->id != $cm->id && 
                $modcm->completion > 0 &&
                !$modcm->deletioninprogress) {
                
                $module_data = array(
                    'id' => $modcm->id,
                    'name' => format_string($modcm->name),
                    'modname' => get_string('modulename', $modcm->modname)
                );
                
                $section_modules[] = $module_data;
                $all_modules_flat[] = $module_data;
            }
        }
        
        if (!empty($section_modules)) {
            $sectionname = get_section_name($course, $section);
            $sections_with_modules[] = array(
                'sectionname' => $sectionname,
                'modules' => $section_modules
            );
        }
    }
    
    // Also get all sections for section links
    $all_sections = array();
    foreach ($modinfo->get_section_info_all() as $section) {
        if ($section->section > 0) { // Skip section 0
            $all_sections[] = array(
                'id' => $section->id,
                'name' => get_section_name($course, $section)
            );
        }
    }
    
    // Load lines between areas
    $linesdata = array();
    if ($dbman = $DB->get_manager()) {
        $table = new xmldb_table('imagemap_line');
        if ($dbman->table_exists($table)) {
            $lines = $DB->get_records('imagemap_line', array('imagemapid' => $imagemap->id));
            foreach ($lines as $line) {
                $linesdata[] = array(
                    'id' => (int)$line->id,
                    'from_areaid' => (int)$line->from_areaid,
                    'to_areaid' => (int)$line->to_areaid
                );
            }
            // Count lines per area
            foreach ($areasfortemplate as &$aft) {
                $count = 0;
                foreach ($linesdata as $ld) {
                    if ($ld['from_areaid'] == $aft['id'] || $ld['to_areaid'] == $aft['id']) {
                        $count++;
                    }
                }
                $aft['lines_count'] = $count;
            }
            unset($aft);
        }
    }
    
    $templatedata = array(
        'has_image' => true,
        'imageUrl' => $imageurl->out(),
        'areasData' => $areasdata,
        'cmid' => $cm->id,
        'imagemapid' => $imagemap->id,
        'sesskey' => sesskey(),
        'area_save_url' => new moodle_url('/mod/imagemap/area_save.php'),
        'has_areas' => !empty($areas),
        'areas' => $areasfortemplate,
        'sections_with_modules' => $sections_with_modules,
        'all_modules_flat' => json_encode($all_modules_flat),
        'all_sections' => $all_sections,
        'active_examples' => $active_examples_template,
        'inactive_examples' => $inactive_examples_template,
        // Language strings
        'shape_label' => get_string('shape', 'imagemap'),
        'shape_rect' => get_string('shape_rect', 'imagemap'),
        'shape_circle' => get_string('shape_circle', 'imagemap'),
        'shape_poly' => get_string('shape_poly', 'imagemap'),
        'clear' => get_string('clear', 'moodle'),
        'finish' => get_string('finish', 'moodle'),
        'addarea' => get_string('addarea', 'imagemap'),
        'title_label' => get_string('title', 'imagemap'),
        'linktype_label' => get_string('linktype', 'imagemap'),
        'linktype_module' => get_string('linktype_module', 'imagemap'),
        'linktype_section' => get_string('linktype_section', 'imagemap'),
        'linktype_url' => get_string('linktype_url', 'imagemap'),
        'linktarget_label' => get_string('linktarget', 'imagemap'),
        'linktarget_help' => get_string('linktarget_help', 'imagemap'),
        'conditioncmid_label' => get_string('conditioncmid', 'imagemap'),
        'nocondition' => get_string('nocondition', 'imagemap'),
        'activefilter_label' => get_string('activefilter', 'imagemap'),
        'activefilter_help' => get_string('activefilter_help', 'imagemap'),
        'inactivefilter_label' => get_string('inactivefilter', 'imagemap'),
        'inactivefilter_help' => get_string('inactivefilter_help', 'imagemap'),
        'savechanges' => get_string('savechanges'),
        'cancel' => get_string('cancel'),
        'managereas' => get_string('managereas', 'imagemap'),
        'actions' => get_string('actions'),
        'delete' => get_string('delete'),
        'confirmdeletearea' => get_string('confirmdeletearea', 'imagemap'),
        'noareas' => get_string('noareas', 'imagemap'),
        'error_noimage' => get_string('error:noimage', 'imagemap'),
        // Toolbar strings
        'tool_hand' => get_string('tool_hand', 'imagemap'),
        'tool_line' => get_string('tool_line', 'imagemap'),
        'tool_eraser' => get_string('tool_eraser', 'imagemap'),
        'connections_label' => get_string('connections', 'imagemap')
    );
    
    // Load editor script inline instead of external to avoid loading issues
    $jscode = file_get_contents(__DIR__ . '/editor.js');
    $PAGE->requires->js_init_code('
        window.imagemapEditorData = ' . json_encode(array(
            'imageUrl' => $imageurl->out(),
            'areasData' => $areasdata,
            'linesData' => $linesdata,
            'sesskey' => sesskey(),
            'cmid' => $cm->id,
            'imagemapid' => $imagemap->id,
            'strings' => array(
                'addarea' => get_string('addarea', 'imagemap'),
                'editarea' => get_string('editarea', 'imagemap'),
                'confirmdeletearea' => get_string('confirmdeletearea', 'imagemap'),
                'line_select_source' => get_string('line_select_source', 'imagemap'),
                'line_select_dest' => get_string('line_select_dest', 'imagemap'),
                'line_same_area' => get_string('line_same_area', 'imagemap'),
                'line_duplicate' => get_string('line_duplicate', 'imagemap'),
                'line_saved' => get_string('line_saved', 'imagemap'),
                'line_deleted' => get_string('line_deleted', 'imagemap'),
                'eraser_hint' => get_string('eraser_hint', 'imagemap'),
                'confirm_delete_line' => get_string('confirm_delete_line', 'imagemap')
            )
        )) . ';
        ' . $jscode . '
        // Initialize when DOM is ready
        if (typeof ImageMapEditor !== "undefined") {
            ImageMapEditor.init();
        }
    ');
} else {
    $templatedata = array(
        'has_image' => false,
        'error_noimage' => get_string('error:noimage', 'imagemap')
    );
}

echo $OUTPUT->render_from_template('mod_imagemap/editor', $templatedata);

echo '<div class="mt-3">';
echo '<a href="' . new moodle_url('/mod/imagemap/view.php', array('id' => $cm->id)) . '" class="btn btn-secondary">' . 
     get_string('back') . '</a>';
echo '</div>';

echo $OUTPUT->footer();
