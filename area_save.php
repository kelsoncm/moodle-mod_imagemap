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
 * Save imagemap area
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

require_sesskey();

$cmid = required_param('cmid', PARAM_INT);
$imagemapid = required_param('imagemapid', PARAM_INT);
$areaid = optional_param('areaid', 0, PARAM_INT);
$shape = required_param('shape', PARAM_ALPHA);
$coords = required_param('coords', PARAM_TEXT);
$title = required_param('title', PARAM_TEXT);
$linktype = required_param('linktype', PARAM_ALPHA);
$linktarget = required_param('linktarget', PARAM_TEXT);
$conditioncmid = optional_param('conditioncmid', 0, PARAM_INT);
$activefilter = optional_param('activefilter', 'none', PARAM_RAW);
$inactivefilter = optional_param('inactivefilter', 'grayscale(1) opacity(0.5)', PARAM_RAW);

$cm = get_coursemodule_from_id('imagemap', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$imagemap = $DB->get_record('imagemap', array('id' => $imagemapid), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/imagemap:manage', $context);

// Validate shape
if (!in_array($shape, array('circle', 'rect', 'poly'))) {
    print_error('error:invalidshape', 'imagemap');
}

// Validate linktype
if (!in_array($linktype, array('module', 'section', 'url'))) {
    print_error('error:invalidlinktype', 'imagemap');
}

$area = new stdClass();
$area->imagemapid = $imagemapid;
$area->shape = $shape;
$area->coords = $coords;
$area->linktype = $linktype;
$area->linktarget = $linktarget;
$area->title = $title;
$area->conditioncmid = $conditioncmid ?: null;
$area->activefilter = $activefilter;
$area->inactivefilter = $inactivefilter;

if ($areaid) {
    $existing = $DB->get_record('imagemap_area', array('id' => $areaid, 'imagemapid' => $imagemapid), '*', MUST_EXIST);
    $area->id = $areaid;
    $area->sortorder = $existing->sortorder;
    $DB->update_record('imagemap_area', $area);
} else {
    // Get next sort order
    $maxsortorder = $DB->get_field_sql(
        'SELECT COALESCE(MAX(sortorder), -1) FROM {imagemap_area} WHERE imagemapid = ?',
        array($imagemapid)
    );
    $area->sortorder = $maxsortorder + 1;
    $DB->insert_record('imagemap_area', $area);
}

redirect(new moodle_url('/mod/imagemap/areas.php', array('id' => $cmid)),
         get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
