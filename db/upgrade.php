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

    return true;
}
