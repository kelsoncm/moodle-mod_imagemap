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

// Handle actions
if ($action === 'delete' && $areaid && confirm_sesskey()) {
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

if ($imagefile) {
    $imageurl = moodle_url::make_pluginfile_url(
        $context->id,
        'mod_imagemap',
        'image',
        0,
        $imagefile->get_filepath(),
        $imagefile->get_filename()
    );
    
    // Get existing areas
    $areas = imagemap_get_areas($imagemap->id);
    
    ?>
    <style>
    .imagemap-editor-container {
        max-width: 100%;
        margin: 20px 0;
    }
    .imagemap-canvas-wrapper {
        position: relative;
        display: inline-block;
        border: 2px solid #ddd;
    }
    #imagemap-canvas {
        display: block;
        cursor: crosshair;
    }
    .area-controls {
        margin: 20px 0;
        padding: 15px;
        background: #f5f5f5;
        border-radius: 5px;
    }
    .area-list {
        margin-top: 20px;
    }
    .area-item {
        padding: 10px;
        margin: 5px 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .area-item:hover {
        background: #f9f9f9;
    }
    </style>
    
    <div class="imagemap-editor-container">
        <div class="area-controls">
            <label><strong><?php echo get_string('shape', 'imagemap'); ?>:</strong></label>
            <select id="shape-selector" class="custom-select" style="width: auto; margin-left: 10px;">
                <option value="rect"><?php echo get_string('shape_rect', 'imagemap'); ?></option>
                <option value="circle"><?php echo get_string('shape_circle', 'imagemap'); ?></option>
                <option value="poly"><?php echo get_string('shape_poly', 'imagemap'); ?></option>
            </select>
            <button id="clear-drawing" class="btn btn-secondary ml-2"><?php echo get_string('clear', 'moodle'); ?></button>
            <button id="finish-poly" class="btn btn-primary ml-2" style="display: none;"><?php echo get_string('finish', 'moodle'); ?></button>
        </div>
        
        <div class="imagemap-canvas-wrapper">
            <canvas id="imagemap-canvas"></canvas>
        </div>
        
        <div id="area-form-container" style="display: none; margin-top: 20px;">
            <h4><?php echo get_string('addarea', 'imagemap'); ?></h4>
            <form id="area-form" method="post" action="area_save.php">
                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                <input type="hidden" name="cmid" value="<?php echo $cm->id; ?>">
                <input type="hidden" name="imagemapid" value="<?php echo $imagemap->id; ?>">
                <input type="hidden" name="shape" id="form-shape" value="">
                <input type="hidden" name="coords" id="form-coords" value="">
                
                <div class="form-group">
                    <label for="title"><?php echo get_string('title', 'imagemap'); ?>:</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="linktype"><?php echo get_string('linktype', 'imagemap'); ?>:</label>
                    <select class="form-control" id="linktype" name="linktype" required>
                        <option value="module"><?php echo get_string('linktype_module', 'imagemap'); ?></option>
                        <option value="section"><?php echo get_string('linktype_section', 'imagemap'); ?></option>
                        <option value="url"><?php echo get_string('linktype_url', 'imagemap'); ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="linktarget"><?php echo get_string('linktarget', 'imagemap'); ?>:</label>
                    <input type="text" class="form-control" id="linktarget" name="linktarget" required>
                    <small class="form-text text-muted"><?php echo get_string('linktarget_help', 'imagemap'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="conditioncmid"><?php echo get_string('conditioncmid', 'imagemap'); ?>:</label>
                    <select class="form-control" id="conditioncmid" name="conditioncmid">
                        <option value="0"><?php echo get_string('nocondition', 'imagemap'); ?></option>
                        <?php
                        $modinfo = get_fast_modinfo($course);
                        foreach ($modinfo->get_cms() as $modcm) {
                            if ($modcm->id != $cm->id && $modcm->completion > 0) {
                                echo '<option value="' . $modcm->id . '">' . format_string($modcm->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="activefilter"><?php echo get_string('activefilter', 'imagemap'); ?>:</label>
                    <input type="text" class="form-control" id="activefilter" name="activefilter" value="none">
                    <small class="form-text text-muted"><?php echo get_string('activefilter_help', 'imagemap'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="inactivefilter"><?php echo get_string('inactivefilter', 'imagemap'); ?>:</label>
                    <input type="text" class="form-control" id="inactivefilter" name="inactivefilter" value="grayscale(1) opacity(0.5)">
                    <small class="form-text text-muted"><?php echo get_string('inactivefilter_help', 'imagemap'); ?></small>
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo get_string('savechanges'); ?></button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('area-form-container').style.display='none';">
                    <?php echo get_string('cancel'); ?>
                </button>
            </form>
        </div>
    </div>
    
    <div class="area-list">
        <h4><?php echo get_string('managereas', 'imagemap'); ?></h4>
        <?php if (empty($areas)): ?>
            <p><?php echo get_string('noareas', 'imagemap'); ?></p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th><?php echo get_string('title', 'imagemap'); ?></th>
                        <th><?php echo get_string('shape', 'imagemap'); ?></th>
                        <th><?php echo get_string('linktype', 'imagemap'); ?></th>
                        <th><?php echo get_string('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($areas as $area): ?>
                        <tr>
                            <td><?php echo s($area->title); ?></td>
                            <td><?php echo s($area->shape); ?></td>
                            <td><?php echo get_string('linktype_' . $area->linktype, 'imagemap'); ?></td>
                            <td>
                                <a href="<?php echo new moodle_url('/mod/imagemap/areas.php', 
                                    array('id' => $cm->id, 'action' => 'delete', 'areaid' => $area->id, 'sesskey' => sesskey())); ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('<?php echo get_string('confirmdeletearea', 'imagemap'); ?>');">
                                    <?php echo get_string('delete'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <script>
    (function() {
        var canvas = document.getElementById('imagemap-canvas');
        var ctx = canvas.getContext('2d');
        var img = new Image();
        var drawing = false;
        var startX, startY;
        var polyPoints = [];
        var currentShape = 'rect';
        
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            drawImage();
        };
        img.src = '<?php echo $imageurl; ?>';
        
        function drawImage() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
        }
        
        document.getElementById('shape-selector').addEventListener('change', function() {
            currentShape = this.value;
            clearDrawing();
        });
        
        document.getElementById('clear-drawing').addEventListener('click', clearDrawing);
        
        document.getElementById('finish-poly').addEventListener('click', function() {
            if (polyPoints.length >= 3) {
                finishDrawing();
            }
        });
        
        function clearDrawing() {
            drawing = false;
            polyPoints = [];
            drawImage();
            document.getElementById('finish-poly').style.display = 'none';
            document.getElementById('area-form-container').style.display = 'none';
        }
        
        canvas.addEventListener('mousedown', function(e) {
            var rect = canvas.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            
            if (currentShape === 'poly') {
                polyPoints.push({x: x, y: y});
                drawImage();
                drawPolygon();
                if (polyPoints.length >= 3) {
                    document.getElementById('finish-poly').style.display = 'inline-block';
                }
            } else {
                drawing = true;
                startX = x;
                startY = y;
            }
        });
        
        canvas.addEventListener('mousemove', function(e) {
            if (!drawing || currentShape === 'poly') return;
            
            var rect = canvas.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            
            drawImage();
            
            ctx.strokeStyle = '#FF0000';
            ctx.lineWidth = 2;
            ctx.beginPath();
            
            if (currentShape === 'rect') {
                ctx.rect(startX, startY, x - startX, y - startY);
            } else if (currentShape === 'circle') {
                var radius = Math.sqrt(Math.pow(x - startX, 2) + Math.pow(y - startY, 2));
                ctx.arc(startX, startY, radius, 0, 2 * Math.PI);
            }
            
            ctx.stroke();
        });
        
        canvas.addEventListener('mouseup', function(e) {
            if (!drawing) return;
            
            var rect = canvas.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;
            
            drawing = false;
            finishDrawing(x, y);
        });
        
        function drawPolygon() {
            if (polyPoints.length < 2) return;
            
            ctx.strokeStyle = '#FF0000';
            ctx.fillStyle = 'rgba(255, 0, 0, 0.2)';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(polyPoints[0].x, polyPoints[0].y);
            
            for (var i = 1; i < polyPoints.length; i++) {
                ctx.lineTo(polyPoints[i].x, polyPoints[i].y);
            }
            
            ctx.stroke();
            
            // Draw points
            polyPoints.forEach(function(point) {
                ctx.fillStyle = '#FF0000';
                ctx.beginPath();
                ctx.arc(point.x, point.y, 4, 0, 2 * Math.PI);
                ctx.fill();
            });
        }
        
        function finishDrawing(endX, endY) {
            var coords = '';
            
            if (currentShape === 'rect') {
                coords = Math.round(startX) + ',' + Math.round(startY) + ',' + 
                         Math.round(endX) + ',' + Math.round(endY);
            } else if (currentShape === 'circle') {
                var radius = Math.round(Math.sqrt(Math.pow(endX - startX, 2) + Math.pow(endY - startY, 2)));
                coords = Math.round(startX) + ',' + Math.round(startY) + ',' + radius;
            } else if (currentShape === 'poly') {
                coords = polyPoints.map(function(p) {
                    return Math.round(p.x) + ',' + Math.round(p.y);
                }).join(',');
            }
            
            document.getElementById('form-shape').value = currentShape;
            document.getElementById('form-coords').value = coords;
            document.getElementById('area-form-container').style.display = 'block';
            
            if (currentShape === 'poly') {
                document.getElementById('finish-poly').style.display = 'none';
            }
        }
    })();
    </script>
    <?php
} else {
    echo '<div class="alert alert-warning">' . get_string('error:noimage', 'imagemap') . '</div>';
}

echo '<div class="mt-3">';
echo '<a href="' . new moodle_url('/mod/imagemap/view.php', array('id' => $cm->id)) . '" class="btn btn-secondary">' . 
     get_string('back') . '</a>';
echo '</div>';

echo $OUTPUT->footer();
