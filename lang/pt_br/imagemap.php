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
 * Portuguese (Brazil) language strings for mod_imagemap
 *
 * @package    mod_imagemap
 * @copyright  2026 Kelson C. M.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Mapa de Imagem';
$string['modulename'] = 'Mapa de Imagem';
$string['modulenameplural'] = 'Mapas de Imagem';
$string['modulename_help'] = 'O módulo Mapa de Imagem permite que professores criem imagens interativas com áreas clicáveis que direcionam para módulos do curso, seções ou URLs externas. As áreas podem ser exibidas condicionalmente com base na conclusão de outros módulos.';
$string['imagemap:addinstance'] = 'Adicionar um novo Mapa de Imagem';
$string['imagemap:view'] = 'Visualizar Mapa de Imagem';
$string['imagemap:manage'] = 'Gerenciar áreas do Mapa de Imagem';
$string['pluginadministration'] = 'Administração do Mapa de Imagem';

// Form strings
$string['imagemapname'] = 'Nome';
$string['imagemapimage'] = 'Imagem';
$string['managereas'] = 'Gerenciar áreas';
$string['addarea'] = 'Adicionar área';
$string['editarea'] = 'Editar área';
$string['deletearea'] = 'Excluir área';
$string['confirmdeletearea'] = 'Tem certeza de que deseja excluir esta área?';

// Area form strings
$string['shape'] = 'Forma';
$string['shape_circle'] = 'Círculo';
$string['shape_rect'] = 'Retângulo';
$string['shape_poly'] = 'Polígono';
$string['coords'] = 'Coordenadas';
$string['coords_help'] = 'Coordenadas para a forma (ex: para círculo: x,y,raio; para retângulo: x1,y1,x2,y2; para polígono: x1,y1,x2,y2,x3,y3,...)';
$string['linktype'] = 'Tipo de link';
$string['linktype_module'] = 'Módulo do curso';
$string['linktype_section'] = 'Seção do curso';
$string['linktype_url'] = 'URL externa';
$string['linktarget'] = 'Destino do link';
$string['linktarget_help'] = 'O destino do link: ID do módulo, número da seção ou URL';
$string['title'] = 'Título';
$string['title_help'] = 'Título a ser exibido ao passar o mouse sobre a área';
$string['conditioncmid'] = 'Condição de conclusão';
$string['conditioncmid_help'] = 'Opcional: Selecione um módulo do curso. A área só estará ativa se este módulo estiver concluído.';
$string['activefilter'] = 'CSS ativo';
$string['activefilter_help'] = 'CSS a ser aplicado quando a área estiver ativa. Exemplos:<br>
<strong>Filtros CSS:</strong><br>
• <strong>filter: brightness(1.2);</strong> - Área mais brilhante<br>
• <strong>filter: saturate(1.5);</strong> - Cores mais saturadas<br>
• <strong>filter: drop-shadow(0 0 10px rgba(0,255,0,0.8));</strong> - Brilho verde<br>
<strong>Bordas e fundos:</strong><br>
• <strong>border: 3px solid #00ff00; background: rgba(0,255,0,0.2);</strong> - Borda verde com fundo<br>
• <strong>box-shadow: 0 0 20px rgba(255,215,0,0.8);</strong> - Brilho dourado<br>
• <strong>background: linear-gradient(45deg, rgba(255,0,0,0.3), rgba(0,0,255,0.3));</strong> - Gradiente';
$string['inactivefilter'] = 'CSS inativo';
$string['inactivefilter_help'] = 'CSS a ser aplicado quando a área estiver inativa. Exemplos:<br>
<strong>Filtros CSS:</strong><br>
• <strong>filter: grayscale(1) opacity(0.5);</strong> - Cinza e transparente (padrão)<br>
• <strong>filter: blur(3px);</strong> - Área borrada<br>
• <strong>filter: brightness(0.4) grayscale(0.5);</strong> - Escurecida<br>
<strong>Fundos e overlays:</strong><br>
• <strong>background: rgba(0,0,0,0.6);</strong> - Overlay escuro<br>
• <strong>background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,0,0,0.3) 10px, rgba(0,0,0,0.3) 20px);</strong> - Listras diagonais';
$string['nocondition'] = 'Sem condição';

// View strings
$string['clickarea'] = 'Clique em uma área';
$string['areainactive'] = 'Esta área está inativa (complete {$a} primeiro)';
$string['noareas'] = 'Nenhuma área foi definida ainda.';

// Admin strings
$string['cssexamples'] = 'Exemplos de CSS';
$string['managecssexamples'] = 'Gerenciar exemplos de CSS';
$string['noexamples'] = 'Nenhum exemplo foi criado ainda.';
$string['confirmdeleteexample'] = 'Tem certeza de que deseja excluir este exemplo?';
$string['order'] = 'Ordem';
$string['preview'] = 'Pré-visualização';
$string['edit'] = 'Editar';
$string['delete'] = 'Excluir';

// Error strings
$string['error:invalidshape'] = 'Tipo de forma inválido';
$string['error:invalidlinktype'] = 'Tipo de link inválido';
$string['error:noimage'] = 'Nenhuma imagem foi enviada';
$string['error:invalidcoords'] = 'Coordenadas inválidas';

// Privacy
$string['privacy:metadata'] = 'O módulo Mapa de Imagem não armazena nenhum dado pessoal.';
