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
// $PAGE->requires->js_call_amd('mod_imagemap/admin', 'init');

// Include JavaScript inline for now
$js = <<<JS
setTimeout(function() {
    console.log('Inicializando canvases inline...');
    var canvases = document.querySelectorAll('.css-preview-canvas');
    console.log('Encontrados', canvases.length, 'canvases');
    
    canvases.forEach(function (canvas, index) {
        console.log('Processando canvas', index);
        var ctx = canvas.getContext('2d');
        var css = canvas.getAttribute('data-css') || '';
        console.log('CSS para canvas', index, ':', css);
        
        // Limpar canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Criar duas zonas: esquerda escura, direita clara
        var halfWidth = canvas.width / 2;
        
        // Zona escura (esquerda)
        ctx.fillStyle = '#2c3e50'; // Cinza escuro
        ctx.fillRect(0, 0, halfWidth, canvas.height);
        
        // Zona clara (direita)
        ctx.fillStyle = '#ecf0f1'; // Cinza claro
        ctx.fillRect(halfWidth, 0, halfWidth, canvas.height);
        
        // Linha divisória
        ctx.strokeStyle = '#34495e';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(halfWidth, 0);
        ctx.lineTo(halfWidth, canvas.height);
        ctx.stroke();
        
        // Função para aplicar filtros às formas individuais
        function applyFilterToShape(ctx, filterCSS, drawFunction) {
            ctx.save();
            
            // Aplicar filtros CSS às formas individuais
            if (filterCSS && filterCSS.trim()) {
                var filterValue = filterCSS;
                
                // Se for CSS completo com filter:, extrair apenas o valor do filter
                if (filterCSS.includes('filter:')) {
                    var match = filterCSS.match(/filter:\s*([^;]+)/);
                    if (match) {
                        filterValue = match[1].trim();
                    }
                }
                
                // Tentar aplicar o filtro diretamente ao contexto 2D
                try {
                    ctx.filter = filterValue;
                    console.log('Canvas', index, '- Aplicando filtro ao contexto 2D:', filterValue);
                } catch (e) {
                    console.log('Canvas', index, '- Filtro não suportado no contexto 2D:', filterValue, '- Erro:', e.message);
                    
                    // Fallback: aplicar filtros simples manualmente
                    if (filterValue.includes('opacity')) {
                        var opacityMatch = filterValue.match(/opacity\((\d+(?:\.\d+)?)\)/);
                        if (opacityMatch) {
                            ctx.globalAlpha = parseFloat(opacityMatch[1]);
                            console.log('Canvas', index, '- Aplicando opacity fallback:', ctx.globalAlpha);
                        }
                    }
                }
            }
            
            drawFunction();
            ctx.restore();
        }
        
        // Desenhar formas na zona escura (esquerda) COM filtros aplicados
        var leftX = halfWidth / 2;
        var shapeSpacing = canvas.height / 4;
        
        // Retângulo na zona escura
        applyFilterToShape(ctx, css, function () {
            ctx.fillStyle = '#e74c3c'; // Vermelho
            ctx.fillRect(leftX - 15, shapeSpacing - 8, 30, 16);
        });
        
        // Círculo na zona escura
        applyFilterToShape(ctx, css, function () {
            ctx.beginPath();
            ctx.arc(leftX, shapeSpacing * 2, 12, 0, 2 * Math.PI);
            ctx.fillStyle = '#f39c12'; // Laranja
            ctx.fill();
        });
        
        // Triângulo na zona escura
        applyFilterToShape(ctx, css, function () {
            ctx.beginPath();
            ctx.moveTo(leftX, shapeSpacing * 3 - 12);
            ctx.lineTo(leftX - 12, shapeSpacing * 3 + 8);
            ctx.lineTo(leftX + 12, shapeSpacing * 3 + 8);
            ctx.closePath();
            ctx.fillStyle = '#27ae60'; // Verde
            ctx.fill();
        });
        
        // Desenhar formas na zona clara (direita) COM filtros aplicados
        var rightX = halfWidth + (halfWidth / 2);
        
        // Retângulo na zona clara
        applyFilterToShape(ctx, css, function () {
            ctx.fillStyle = '#3498db'; // Azul
            ctx.fillRect(rightX - 15, shapeSpacing - 8, 30, 16);
        });
        
        // Círculo na zona clara
        applyFilterToShape(ctx, css, function () {
            ctx.beginPath();
            ctx.arc(rightX, shapeSpacing * 2, 12, 0, 2 * Math.PI);
            ctx.fillStyle = '#9b59b6'; // Roxo
            ctx.fill();
        });
        
        // Triângulo na zona clara
        applyFilterToShape(ctx, css, function () {
            ctx.beginPath();
            ctx.moveTo(rightX, shapeSpacing * 3 - 12);
            ctx.lineTo(rightX - 12, shapeSpacing * 3 + 8);
            ctx.lineTo(rightX + 12, shapeSpacing * 3 + 8);
            ctx.closePath();
            ctx.fillStyle = '#e67e22'; // Laranja escuro
            ctx.fill();
        });
        
        // Adicionar texto do filtro aplicado no canvas
        ctx.fillStyle = '#ffffff';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        
        var filterText = 'Sem filtro';
        if (css && css.trim()) {
            filterText = css.length > 20 ? css.substring(0, 17) + '...' : css;
        }
        
        ctx.fillText(filterText, canvas.width / 2, canvas.height - 10);
    });
}, 500);
JS;

$PAGE->requires->js_init_code($js);

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