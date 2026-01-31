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
 * Upgrade script for mod_imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute imagemap upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_imagemap_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2026013001) {
        // Define field activefilter to be changed to text.
        $table = new xmldb_table('imagemap_area');
        $field = new xmldb_field('activefilter', XMLDB_TYPE_TEXT, null, null, null, null, null, 'conditioncmid');

        // Launch change of type for field activefilter.
        $dbman->change_field_type($table, $field);

        // Define field inactivefilter to be changed to text.
        $field = new xmldb_field('inactivefilter', XMLDB_TYPE_TEXT, null, null, null, null, null, 'activefilter');

        // Launch change of type for field inactivefilter.
        $dbman->change_field_type($table, $field);

        // Imagemap savepoint reached.
        upgrade_mod_savepoint(true, 2026013001, 'imagemap');
    }

    if ($oldversion < 2026013101) {
        // Define table imagemap_css_examples to be created.
        $table = new xmldb_table('imagemap_css_examples');

        // Adding fields to table imagemap_css_examples.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('css_text', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table imagemap_css_examples.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table imagemap_css_examples.
        $table->add_index('type', XMLDB_INDEX_NOTUNIQUE, array('type'));
        $table->add_index('sortorder', XMLDB_INDEX_NOTUNIQUE, array('sortorder'));

        // Conditionally launch create table for imagemap_css_examples.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
            
            // Add default examples
            $examples = array(
                // Active examples
                array('type' => 'active', 'name' => 'Sem Efeito', 'css_text' => 'none', 'sortorder' => 0),
                array('type' => 'active', 'name' => 'Bright Glow', 'css_text' => 'filter: brightness(1.2) drop-shadow(0 0 10px rgba(255,255,0,0.8));', 'sortorder' => 1),
                array('type' => 'active', 'name' => 'Green Border', 'css_text' => 'border: 3px solid #00ff00; background: rgba(0,255,0,0.2);', 'sortorder' => 2),
                array('type' => 'active', 'name' => 'Golden Glow', 'css_text' => 'box-shadow: 0 0 20px rgba(255,215,0,0.8);', 'sortorder' => 3),
                array('type' => 'active', 'name' => 'Blue Highlight', 'css_text' => 'filter: brightness(1.1) saturate(1.3) hue-rotate(200deg);', 'sortorder' => 4),
                array('type' => 'active', 'name' => 'Rainbow Border', 'css_text' => 'border: 4px solid; border-image: linear-gradient(45deg, red, orange, yellow, green, blue, indigo, violet) 1;', 'sortorder' => 5),
                array('type' => 'active', 'name' => 'Neon Effect', 'css_text' => 'filter: brightness(1.5) contrast(1.2) drop-shadow(0 0 5px #00ffff) drop-shadow(0 0 10px #00ffff);', 'sortorder' => 6),
                array('type' => 'active', 'name' => 'Warm Glow', 'css_text' => 'filter: sepia(0.3) brightness(1.1) contrast(1.1) saturate(1.2);', 'sortorder' => 7),
                array('type' => 'active', 'name' => 'Crystal Clear', 'css_text' => 'filter: brightness(1.3) contrast(1.2) saturate(1.5);', 'sortorder' => 8),
                
                // Inactive examples
                array('type' => 'inactive', 'name' => 'Sem Efeito', 'css_text' => 'none', 'sortorder' => 0),
                array('type' => 'inactive', 'name' => 'Grayed Out', 'css_text' => 'filter: grayscale(1) opacity(0.5);', 'sortorder' => 1),
                array('type' => 'inactive', 'name' => 'Dark Overlay', 'css_text' => 'background: rgba(0,0,0,0.6);', 'sortorder' => 2),
                array('type' => 'inactive', 'name' => 'Blurred', 'css_text' => 'filter: blur(3px);', 'sortorder' => 3),
                array('type' => 'inactive', 'name' => 'Desaturated', 'css_text' => 'filter: brightness(0.4) grayscale(0.5);', 'sortorder' => 4),
                array('type' => 'inactive', 'name' => 'Low Contrast', 'css_text' => 'filter: contrast(0.5) brightness(0.7);', 'sortorder' => 5),
                array('type' => 'inactive', 'name' => 'Muted Colors', 'css_text' => 'filter: saturate(0.2) brightness(0.8);', 'sortorder' => 6),
                array('type' => 'inactive', 'name' => 'Foggy', 'css_text' => 'filter: blur(2px) brightness(0.9) contrast(0.8);', 'sortorder' => 7),
                array('type' => 'inactive', 'name' => 'Shadowed', 'css_text' => 'filter: brightness(0.5) drop-shadow(0 0 5px rgba(0,0,0,0.5));', 'sortorder' => 8),
            );
            
            foreach ($examples as $example) {
                $record = new stdClass();
                $record->type = $example['type'];
                $record->name = $example['name'];
                $record->css_text = $example['css_text'];
                $record->sortorder = $example['sortorder'];
                $record->timecreated = time();
                $record->timemodified = time();
                $DB->insert_record('imagemap_css_examples', $record);
            }
        }

        // Imagemap savepoint reached.
        upgrade_mod_savepoint(true, 2026013102, 'imagemap');
    }

    return true;
}
