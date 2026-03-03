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
 * Backup steps for mod_imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete imagemap structure for backup, with file and id annotations
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_imagemap_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the backup
     */
    protected function define_structure() {

        // To know if we are including all the files.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $imagemap = new backup_nested_element('imagemap', array('id'), array(
            'course', 'name', 'intro', 'introformat', 'timemodified', 'width', 'height'
        ));

        $areas = new backup_nested_element('areas');

        $area = new backup_nested_element('area', array('id'), array(
            'imagemapid', 'shape', 'coords', 'targettype', 'targetid', 'title',
            'activefilter', 'inactivefilter', 'sortorder'
        ));

        $lines = new backup_nested_element('lines');

        $line = new backup_nested_element('line', array('id'), array(
            'imagemapid', 'from_areaid', 'to_areaid', 'timecreated'
        ));

        $css_examples = new backup_nested_element('css_examples');

        $css_example = new backup_nested_element('css_example', array('id'), array(
            'type', 'name', 'css_text', 'sortorder', 'timecreated', 'timemodified'
        ));

        // Build the tree.
        $imagemap->add_child($areas);
        $areas->add_child($area);
        $imagemap->add_child($lines);
        $lines->add_child($line);
        $imagemap->add_child($css_examples);
        $css_examples->add_child($css_example);

        // Define sources.
        $imagemap->set_source_table('imagemap', array('id' => backup::VAR_ACTIVITYID));

        $area->set_source_table('imagemap_area', array('imagemapid' => backup::VAR_PARENTID));

        $line->set_source_table('imagemap_line', array('imagemapid' => backup::VAR_ACTIVITYID));

        // CSS examples are global, but included in backup for portability
        $css_example->set_source_table('imagemap_css_examples', array());

        // Define id annotations for areas:
        // - module links need ID remapping during restore
        // - section and url links don't need remapping (url is literal, section stays same)
        $area->annotate_ids('module', 'targetid');

        // Define file annotations.
        $imagemap->annotate_files('mod_imagemap', 'image', null);

        // Return the root element (imagemap).
        return $imagemap;
    }
}
