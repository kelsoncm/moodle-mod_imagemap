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
 * English language strings for mod_imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Image Map';
$string['modulename'] = 'Image Map';
$string['modulenameplural'] = 'Image Maps';
$string['modulename_help'] = 'The Image Map module allows teachers to create interactive images with clickable areas that link to course modules or sections. Areas reflect access restrictions and completion rules.';
$string['imagemap:addinstance'] = 'Add a new Image Map';
$string['imagemap:view'] = 'View Image Map';
$string['imagemap:manage'] = 'Manage Image Map areas';
$string['pluginadministration'] = 'Image Map administration';

// Form strings
$string['imagemapname'] = 'Name';
$string['imagemapimage'] = 'Image';
$string['managereas'] = 'Manage areas';
$string['addarea'] = 'Add area';
$string['editarea'] = 'Edit area';
$string['deletearea'] = 'Delete area';
$string['confirmdeletearea'] = 'Are you sure you want to delete this area?';

// Area form strings
$string['shape'] = 'Shape';
$string['shape_circle'] = 'Circle';
$string['shape_rect'] = 'Rectangle';
$string['shape_poly'] = 'Polygon';
$string['coords'] = 'Coordinates';
$string['coords_help'] = 'Coordinates for the shape (e.g., for circle: x,y,radius; for rectangle: x1,y1,x2,y2; for polygon: x1,y1,x2,y2,x3,y3,...)';
$string['target'] = 'Target';
$string['target_help'] = 'Choose a section or activity for this area.';
$string['targetmissing'] = 'Target not found';
$string['title'] = 'Title';
$string['title_help'] = 'Title to display when hovering over the area';
$string['activefilter'] = 'Active CSS';
$string['activefilter_help'] = 'CSS to apply when the area is active.';
$string['inactivefilter'] = 'Inactive CSS';
$string['inactivefilter_help'] = 'CSS to apply when the area is inactive.';
$string['arearestricted'] = 'This item has access restrictions.';

// View strings
$string['clickarea'] = 'Click on an area';
$string['areainactive'] = 'This area is inactive (complete {$a} first)';
$string['noareas'] = 'No areas have been defined yet.';
$string['coursepreviewrestricted'] = '{$a} area(s) with access restriction';

// Error strings
$string['error:invalidshape'] = 'Invalid shape type';
$string['error:invalidtargettype'] = 'Invalid target type';
$string['error:invalidtarget'] = 'Invalid target';
$string['error:noimage'] = 'No image has been uploaded';
$string['error:invalidcoords'] = 'Invalid coordinates';

// Privacy
$string['privacy:metadata'] = 'The Image Map module does not store any personal data.';

// CSS Examples
$string['cssexamples'] = 'CSS Examples';
$string['viewexamples'] = 'View Examples';
$string['managecssexamples'] = 'Manage CSS Examples';
$string['addexample'] = 'Add Example';
$string['css_text'] = 'CSS Code';
$string['type'] = 'Type';
$string['sortorder'] = 'Sort Order';
$string['exampledeleted'] = 'Example deleted successfully';
$string['examplesaved'] = 'Example saved successfully';
$string['noexamples'] = 'No examples defined yet';
$string['confirmdeleteexample'] = 'Are you sure you want to delete this example?';

// Toolbar / tools
$string['tool_hand'] = 'Select';
$string['tool_hand_help'] = 'Click to select, drag to move areas. Right-click to edit.';
$string['tool_line'] = 'Line';
$string['tool_line_help'] = 'Click on a source shape, then on a destination shape to create a connecting line.';
$string['tool_eraser'] = 'Eraser';
$string['tool_eraser_help'] = 'Click on a shape or line to delete it.';
$string['line_select_source'] = 'Click on the source shape';
$string['line_select_dest'] = 'Now click on the destination shape';
$string['line_same_area'] = 'Cannot connect a shape to itself';
$string['line_duplicate'] = 'This connection already exists';
$string['line_saved'] = 'Line saved';
$string['line_deleted'] = 'Line deleted';
$string['eraser_hint'] = 'Click a shape or line to delete';
$string['confirm_delete_line'] = 'Delete this connection line?';
$string['connections'] = 'Connections';