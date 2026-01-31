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
$string['modulename_help'] = 'The Image Map module allows teachers to create interactive images with clickable areas that link to course modules, sections, or external URLs. Areas can be conditionally displayed based on completion of other modules.';
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
$string['linktype'] = 'Link type';
$string['linktype_module'] = 'Course module';
$string['linktype_section'] = 'Course section';
$string['linktype_url'] = 'External URL';
$string['linktarget'] = 'Link target';
$string['linktarget_help'] = 'The target of the link: module ID, section number, or URL';
$string['title'] = 'Title';
$string['title_help'] = 'Title to display when hovering over the area';
$string['conditioncmid'] = 'Completion condition';
$string['conditioncmid_help'] = 'Optional: Select a course module. The area will only be active if this module is completed.';
$string['activefilter'] = 'Active CSS';
$string['activefilter_help'] = 'CSS to apply when the area is active. Examples:<br>
<strong>CSS Filters:</strong><br>
• <strong>filter: brightness(1.2);</strong> - Brighter area<br>
• <strong>filter: saturate(1.5);</strong> - More saturated colors<br>
• <strong>filter: drop-shadow(0 0 10px rgba(0,255,0,0.8));</strong> - Green glow<br>
<strong>Borders and backgrounds:</strong><br>
• <strong>border: 3px solid #00ff00; background: rgba(0,255,0,0.2);</strong> - Green border with background<br>
• <strong>box-shadow: 0 0 20px rgba(255,215,0,0.8);</strong> - Golden glow<br>
• <strong>background: linear-gradient(45deg, rgba(255,0,0,0.3), rgba(0,0,255,0.3));</strong> - Gradient';
$string['inactivefilter'] = 'Inactive CSS';
$string['inactivefilter_help'] = 'CSS to apply when the area is inactive. Examples:<br>
<strong>CSS Filters:</strong><br>
• <strong>filter: grayscale(1) opacity(0.5);</strong> - Gray and transparent (default)<br>
• <strong>filter: blur(3px);</strong> - Blurred area<br>
• <strong>filter: brightness(0.4) grayscale(0.5);</strong> - Darkened<br>
<strong>Backgrounds and overlays:</strong><br>
• <strong>background: rgba(0,0,0,0.6);</strong> - Dark overlay<br>
• <strong>background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,0,0,0.3) 10px, rgba(0,0,0,0.3) 20px);</strong> - Diagonal stripes';
$string['nocondition'] = 'No condition';

// View strings
$string['clickarea'] = 'Click on an area';
$string['areainactive'] = 'This area is inactive (complete {$a} first)';
$string['noareas'] = 'No areas have been defined yet.';

// Error strings
$string['error:invalidshape'] = 'Invalid shape type';
$string['error:invalidlinktype'] = 'Invalid link type';
$string['error:noimage'] = 'No image has been uploaded';
$string['error:invalidcoords'] = 'Invalid coordinates';

// Privacy
$string['privacy:metadata'] = 'The Image Map module does not store any personal data.';
