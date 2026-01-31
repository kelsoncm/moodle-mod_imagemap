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
 * Prints a particular instance of imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course module ID

if ($id) {
    $cm = get_coursemodule_from_id('imagemap', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $imagemap = $DB->get_record('imagemap', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('missingparameter');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/imagemap:view', $context);

$event = \mod_imagemap\event\course_module_viewed::create(array(
    'objectid' => $imagemap->id,
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('imagemap', $imagemap);
$event->trigger();

$PAGE->set_url('/mod/imagemap/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($imagemap->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Get the image file
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_imagemap', 'image', 0, 'itemid, filepath, filename', false);
$imagefile = reset($files);

echo $OUTPUT->header();

if ($imagefile) {
    $imageurl = moodle_url::make_pluginfile_url(
        $context->id,
        'mod_imagemap',
        'image',
        $imagefile->get_itemid(),
        $imagefile->get_filepath(),
        $imagefile->get_filename()
    );
    
    // Get areas
    $areas = imagemap_get_areas($imagemap->id);
    
    // Prepare area data for JavaScript
    $areadata = array();
    foreach ($areas as $area) {
        $isactive = imagemap_is_area_active($area, $USER->id);
        $url = imagemap_get_area_url($area, $course->id);
        
        $areadata[] = array(
            'shape' => $area->shape,
            'coords' => $area->coords,
            'title' => $area->title,
            'url' => $url ? $url->out() : '',
            'active' => $isactive,
            'activefilter' => $area->activefilter ?: 'none',
            'inactivefilter' => $area->inactivefilter ?: 'grayscale(1) opacity(0.5)'
        );
    }
    
    // Output the canvas-based imagemap with CSS overlays
    echo '<div class="imagemap-container" style="position: relative; display: inline-block;">';
    echo '<canvas id="imagemap-canvas-' . $imagemap->id . '" style="display: block;"></canvas>';
    echo '<div id="imagemap-overlays-' . $imagemap->id . '" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></div>';
    echo '</div>';
    
    // Add JavaScript to render the canvas imagemap with CSS overlays
    $PAGE->requires->js_init_code("
        (function() {
            var canvas = document.getElementById('imagemap-canvas-{$imagemap->id}');
            var overlaysContainer = document.getElementById('imagemap-overlays-{$imagemap->id}');
            if (!canvas || !overlaysContainer) return;
            
            var ctx = canvas.getContext('2d');
            var img = new Image();
            var areas = " . json_encode($areadata) . ";
            
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                overlaysContainer.style.width = img.width + 'px';
                overlaysContainer.style.height = img.height + 'px';
                drawImageMap();
            };
            
            img.src = '" . $imageurl->out() . "';
            
            function drawImageMap() {
                // Draw base image
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
                
                // Clear existing overlays
                overlaysContainer.innerHTML = '';
                
                // Create CSS overlays for each area
                areas.forEach(function(area, index) {
                    var coords = area.coords.split(',').map(function(v) { return parseFloat(v); });
                    var overlay = document.createElement('div');
                    overlay.className = 'imagemap-area-overlay';
                    overlay.style.position = 'absolute';
                    overlay.style.pointerEvents = area.active && area.url ? 'auto' : 'none';
                    overlay.style.cursor = area.active && area.url ? 'pointer' : 'not-allowed';
                    overlay.title = area.title || '';
                    overlay.dataset.areaIndex = index;
                    
                    // Apply custom CSS
                    var cssText = area.active ? (area.activefilter || '') : (area.inactivefilter || 'filter: grayscale(1) opacity(0.5);');
                    if (cssText && cssText !== 'none') {
                        // Check if it's a filter or full CSS
                        if (cssText.indexOf(':') === -1 && cssText.indexOf('(') !== -1) {
                            // It's a filter value without property name
                            overlay.style.filter = cssText;
                        } else {
                            // It's full CSS
                            cssText.split(';').forEach(function(rule) {
                                if (!rule.trim()) return;
                                var parts = rule.split(':');
                                if (parts.length === 2) {
                                    var prop = parts[0].trim();
                                    var value = parts[1].trim();
                                    overlay.style.setProperty(prop, value);
                                }
                            });
                        }
                    }
                    
                    // Position and clip the overlay based on shape
                    if (area.shape === 'rect' && coords.length >= 4) {
                        var x1 = Math.min(coords[0], coords[2]);
                        var y1 = Math.min(coords[1], coords[3]);
                        var w = Math.abs(coords[2] - coords[0]);
                        var h = Math.abs(coords[3] - coords[1]);
                        overlay.style.left = x1 + 'px';
                        overlay.style.top = y1 + 'px';
                        overlay.style.width = w + 'px';
                        overlay.style.height = h + 'px';
                    } else if (area.shape === 'circle' && coords.length >= 3) {
                        var cx = coords[0], cy = coords[1], r = coords[2];
                        overlay.style.left = (cx - r) + 'px';
                        overlay.style.top = (cy - r) + 'px';
                        overlay.style.width = (r * 2) + 'px';
                        overlay.style.height = (r * 2) + 'px';
                        overlay.style.borderRadius = '50%';
                    } else if (area.shape === 'poly' && coords.length >= 6) {
                        var minX = Math.min.apply(null, coords.filter(function(v, i) { return i % 2 === 0; }));
                        var maxX = Math.max.apply(null, coords.filter(function(v, i) { return i % 2 === 0; }));
                        var minY = Math.min.apply(null, coords.filter(function(v, i) { return i % 2 === 1; }));
                        var maxY = Math.max.apply(null, coords.filter(function(v, i) { return i % 2 === 1; }));
                        
                        overlay.style.left = minX + 'px';
                        overlay.style.top = minY + 'px';
                        overlay.style.width = (maxX - minX) + 'px';
                        overlay.style.height = (maxY - minY) + 'px';
                        
                        // Create polygon clip path
                        var clipPath = 'polygon(';
                        for (var i = 0; i < coords.length; i += 2) {
                            if (i > 0) clipPath += ', ';
                            clipPath += ((coords[i] - minX) / (maxX - minX) * 100) + '% ';
                            clipPath += ((coords[i + 1] - minY) / (maxY - minY) * 100) + '%';
                        }
                        clipPath += ')';
                        overlay.style.clipPath = clipPath;
                        overlay.style.webkitClipPath = clipPath;
                    }
                    
                    // Create inner element for background if CSS includes background
                    var inner = document.createElement('div');
                    inner.style.width = '100%';
                    inner.style.height = '100%';
                    inner.style.pointerEvents = 'none';
                    overlay.appendChild(inner);
                    
                    // Add click handler
                    if (area.active && area.url) {
                        overlay.addEventListener('click', function() {
                            window.location.href = area.url;
                        });
                    }
                    
                    overlaysContainer.appendChild(overlay);
                });
            }
        })();
    ");
    
    if (has_capability('mod/imagemap:manage', $context)) {
        echo '<div class="imagemap-manage">';
        echo '<a href="' . new moodle_url('/mod/imagemap/areas.php', array('id' => $cm->id)) . '" class="btn btn-secondary">' . 
             get_string('managereas', 'imagemap') . '</a>';
        echo '</div>';
    }
} else {
    echo '<div class="alert alert-warning">' . get_string('error:noimage', 'imagemap') . '</div>';
    
    if (has_capability('mod/imagemap:manage', $context)) {
        echo '<p><a href="' . new moodle_url('/course/modedit.php', array('update' => $cm->id, 'return' => 1)) . '">' . 
             get_string('editsettings', 'moodle') . '</a></p>';
    }
}

echo $OUTPUT->footer();
