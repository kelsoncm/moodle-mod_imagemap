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
$PAGE->requires->css('/mod/imagemap/styles.css');
$PAGE->requires->js('/mod/imagemap/css_preview.js');

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
    
    $areasbyid = array();
    foreach ($areas as $area) {
        $areasbyid[$area->id] = $area;
        $areasdata[] = array(
            'id' => (int)$area->id,
            'shape' => $area->shape,
            'coords' => $area->coords,
            'title' => $area->title,
            'targettype' => $area->targettype,
            'targetid' => (int)$area->targetid,
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
            'target_label' => '',
            'delete_url' => $deleteurl->out(),
            'delete' => get_string('delete'),
            'lines_count' => 0
        );
    }
    
    // Fetch CSS examples for active and inactive filters
    $active_examples = $DB->get_records('imagemap_css_examples', array('type' => 'active'), 'sortorder ASC, name ASC');
    $inactive_examples = $DB->get_records('imagemap_css_examples', array('type' => 'inactive'), 'sortorder ASC, name ASC');
    
    // If no examples exist, create default ones (same as admin.php)
    if (empty($active_examples)) {
        $test_examples = array(
            array('type' => 'active', 'name' => 'No Effect', 'css_text' => 'none', 'sortorder' => 0),
            array('type' => 'active', 'name' => 'Grayscale', 'css_text' => 'filter: grayscale(100%)', 'sortorder' => 1),
            array('type' => 'active', 'name' => 'Opacity 50%', 'css_text' => 'filter: opacity(0.5)', 'sortorder' => 2),
            array('type' => 'active', 'name' => 'Blur', 'css_text' => 'filter: blur(2px)', 'sortorder' => 3),
        );
        
        foreach ($test_examples as $example) {
            $record = new stdClass();
            $record->type = $example['type'];
            $record->name = $example['name'];
            $record->css_text = $example['css_text'];
            $record->sortorder = $example['sortorder'];
            $record->timecreated = time();
            $record->timemodified = time();
            $DB->insert_record('imagemap_css_examples', $record);
        }
        
        // Reload examples
        $active_examples = $DB->get_records('imagemap_css_examples', array('type' => 'active'), 'sortorder ASC, name ASC');
    }
    
    if (empty($inactive_examples)) {
        $test_examples = array(
            array('type' => 'inactive', 'name' => 'No Effect', 'css_text' => 'none', 'sortorder' => 0),
            array('type' => 'inactive', 'name' => 'Grayed Out', 'css_text' => 'filter: grayscale(1) opacity(0.5)', 'sortorder' => 1),
            array('type' => 'inactive', 'name' => 'Blurred', 'css_text' => 'filter: blur(3px)', 'sortorder' => 2),
        );
        
        foreach ($test_examples as $example) {
            $record = new stdClass();
            $record->type = $example['type'];
            $record->name = $example['name'];
            $record->css_text = $example['css_text'];
            $record->sortorder = $example['sortorder'];
            $record->timecreated = time();
            $record->timemodified = time();
            $DB->insert_record('imagemap_css_examples', $record);
        }
        
        // Reload examples
        $inactive_examples = $DB->get_records('imagemap_css_examples', array('type' => 'inactive'), 'sortorder ASC, name ASC');
    }
    
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
    
    // Build target options with hierarchy (sections, subsections, modules).
    $modinfo = get_fast_modinfo($course);
    $allsections = $modinfo->get_section_info_all();
    $sectionwithchildren = array();
    $delegatedchildren = array();

    foreach ($allsections as $section) {
        $sectiondelegated = $section->get_component_instance();
        if ($sectiondelegated) {
            $parentsection = $sectiondelegated->get_parent_section();
            if ($parentsection) {
                $sectionwithchildren[$parentsection->section][] = $section;
                $delegatedchildren[$section->section] = true;
            }
        }
    }

    $modulelabels = array();
    foreach ($modinfo->get_cms() as $modcm) {
        if ($modcm->deletioninprogress) {
            continue;
        }
        $modulelabels[$modcm->id] = format_string($modcm->name) .
            ' (' . get_string('modulename', $modcm->modname) . ')';
    }

    $sectionlabels = array();
    foreach ($allsections as $section) {
        $sectionlabels[$section->id] = get_section_name($course, $section);
    }

    $targetoptions = array();
    $indent = function(int $level): string {
        return str_repeat('-- ', $level);
    };
    $add_section_option = function($section, int $level) use (&$targetoptions, $indent, $course) {
        $targetoptions[] = array(
            'value' => 'section:' . $section->id,
            'label' => $indent($level) . get_section_name($course, $section)
        );
    };
    $add_module_options = function($section, int $level) use (&$targetoptions, $indent, $modinfo, $cm) {
        foreach ($modinfo->get_cms() as $modcm) {
            if ($modcm->sectionnum == $section->section &&
                $modcm->id != $cm->id &&
                !$modcm->deletioninprogress) {
                $targetoptions[] = array(
                    'value' => 'module:' . $modcm->id,
                    'label' => $indent($level) . format_string($modcm->name) .
                        ' (' . get_string('modulename', $modcm->modname) . ')'
                );
            }
        }
    };

    foreach ($allsections as $section) {
        if (isset($delegatedchildren[$section->section])) {
            continue;
        }
        $add_section_option($section, 0);
        $add_module_options($section, 1);
        if (isset($sectionwithchildren[$section->section])) {
            foreach ($sectionwithchildren[$section->section] as $subsection) {
                $add_section_option($subsection, 1);
                $add_module_options($subsection, 2);
            }
        }
    }

    foreach ($areasfortemplate as &$aft) {
        $targetlabel = '';
        $area = $areasbyid[$aft['id']] ?? null;
        if ($area) {
            if ($area->targettype === 'module' && isset($modulelabels[$area->targetid])) {
                $targetlabel = $modulelabels[$area->targetid];
            } else if ($area->targettype === 'section' && isset($sectionlabels[$area->targetid])) {
                $targetlabel = $sectionlabels[$area->targetid];
            }
        }
        $aft['target_label'] = $targetlabel ?: get_string('targetmissing', 'imagemap');
    }
    unset($aft);
    
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
    
    // Prepare CSS examples modal
    $active_examples_modal = array();
    $inactive_examples_modal = array();
    
    foreach ($active_examples as $example) {
        $active_examples_modal[] = array(
            'name' => s($example->name),
            'css_text' => s($example->css_text),
        );
    }
    
    foreach ($inactive_examples as $example) {
        $inactive_examples_modal[] = array(
            'name' => s($example->name),
            'css_text' => s($example->css_text),
        );
    }
    
    $modal_data = array(
        'active_examples' => $active_examples_modal,
        'inactive_examples' => $inactive_examples_modal,
    );
    $css_examples_modal = $OUTPUT->render_from_template('mod_imagemap/css_examples_modal', $modal_data);
    
    // Debug: Check examples count
    echo "<!-- Debug: Active examples: " . count($active_examples) . " | Inactive examples: " . count($inactive_examples) . " -->\n";
    
    $templatedata = array(
        'has_image' => true,
        'imageUrl' => $imageurl->out(),
        'areasData' => $areasdata,
        'cmid' => $cm->id,
        'imagemapid' => $imagemap->id,
        'sesskey' => sesskey(),
        'area_save_url' => new moodle_url('/mod/imagemap/area_save.php'),
        'css_examples_modal' => $css_examples_modal,
        'has_areas' => !empty($areas),
        'areas' => $areasfortemplate,
        'target_options' => $targetoptions,
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
        'target_label' => get_string('target', 'imagemap'),
        'target_help' => get_string('target_help', 'imagemap'),
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
