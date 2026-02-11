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
 * Admin page for managing CSS examples
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Require admin access
require_login();
if (!is_siteadmin()) {
    throw new moodle_exception('accessdenied', 'admin');
}

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url('/mod/imagemap/admin.php');
$PAGE->set_title(get_string('cssexamples', 'imagemap'));
$PAGE->set_heading(get_string('cssexamples', 'imagemap'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

// Add navigation
$PAGE->navbar->add(get_string('pluginadministration', 'imagemap'), new moodle_url('/admin/category.php', array('category' => 'modsettings')));
$PAGE->navbar->add(get_string('cssexamples', 'imagemap'));

// Include CSS and JS for the admin interface
$PAGE->requires->css('/mod/imagemap/styles/admin.css');
$PAGE->requires->js('/mod/imagemap/css_preview.js');

echo $OUTPUT->header();

if ($action === 'delete' && $id && confirm_sesskey()) {
    $DB->delete_records('imagemap_css_examples', array('id' => $id));
    redirect($PAGE->url, get_string('exampledeleted', 'imagemap'));
}

if ($action === 'edit' || $action === 'add') {
    $example = null;
    if ($action === 'edit' && $id) {
        $example = $DB->get_record('imagemap_css_examples', array('id' => $id), '*', MUST_EXIST);
    }
    
    $form = new \mod_imagemap\form\css_example_form($PAGE->url, array('example' => $example));
    
    if ($form->is_cancelled()) {
        redirect($PAGE->url);
    } elseif ($data = $form->get_data()) {
        $record = new stdClass();
        $record->type = $data->type;
        $record->name = $data->name;
        $record->css_text = $data->css_text;
        $record->sortorder = $data->sortorder;
        
        if ($example) {
            $record->id = $example->id;
            $record->timemodified = time();
            $DB->update_record('imagemap_css_examples', $record);
        } else {
            $record->timecreated = time();
            $record->timemodified = time();
            $DB->insert_record('imagemap_css_examples', $record);
        }
        
        redirect($PAGE->url, get_string('examplesaved', 'imagemap'));
    }
    
    $form->display();
} else {
    // Display list using template

    // Prepare template data
    $template_data = array(
        'heading' => $OUTPUT->heading(get_string('cssexamples', 'imagemap')),
        'addbutton' => html_writer::link(
            new moodle_url($PAGE->url, array('action' => 'add')),
            get_string('addexample', 'imagemap'),
            array('class' => 'btn btn-primary mb-3')
        )
    );

    // Get active examples
    $active_examples = $DB->get_records('imagemap_css_examples', array('type' => 'active'), 'sortorder ASC, name ASC');
    
    // If no examples exist, add some test data
    if (empty($active_examples)) {
        $test_examples = array(
            array('type' => 'active', 'name' => 'Sem Efeito', 'css_text' => 'none', 'sortorder' => 0),
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
    
    if (!empty($active_examples)) {
        $template_data['active_examples'] = array();
        foreach ($active_examples as $example) {
            $template_data['active_examples'][] = array(
                'name' => s($example->name),
                'sortorder' => $example->sortorder,
                'css_text' => $example->css_text,
                'editurl' => new moodle_url($PAGE->url, array('action' => 'edit', 'id' => $example->id)),
                'deleteurl' => new moodle_url($PAGE->url, array('action' => 'delete', 'id' => $example->id, 'sesskey' => sesskey()))
            );
        }
    }

    // Get inactive examples
    $inactive_examples = $DB->get_records('imagemap_css_examples', array('type' => 'inactive'), 'sortorder ASC, name ASC');
    
    // If no examples exist, add some test data
    if (empty($inactive_examples)) {
        $test_examples = array(
            array('type' => 'inactive', 'name' => 'Sem Efeito', 'css_text' => 'none', 'sortorder' => 0),
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
    
    if (!empty($inactive_examples)) {
        $template_data['inactive_examples'] = array();
        foreach ($inactive_examples as $example) {
            $template_data['inactive_examples'][] = array(
                'name' => s($example->name),
                'sortorder' => $example->sortorder,
                'css_text' => $example->css_text,
                'editurl' => new moodle_url($PAGE->url, array('action' => 'edit', 'id' => $example->id)),
                'deleteurl' => new moodle_url($PAGE->url, array('action' => 'delete', 'id' => $example->id, 'sesskey' => sesskey()))
            );
        }
    }

    // Debug: Check if we have examples
    echo "<!-- Debug: Active examples: " . count($active_examples) . " -->\n";
    echo "<!-- Debug: Inactive examples: " . count($inactive_examples) . " -->\n";
    
    echo $OUTPUT->render_from_template('mod_imagemap/admin_css_examples', $template_data);
}

echo $OUTPUT->footer();