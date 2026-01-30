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
$string['activefilter'] = 'Filtro ativo';
$string['activefilter_help'] = 'Filtro CSS a ser aplicado quando a área estiver ativa (ex: none, grayscale(0))';
$string['inactivefilter'] = 'Filtro inativo';
$string['inactivefilter_help'] = 'Filtro CSS a ser aplicado quando a área estiver inativa (ex: grayscale(1), opacity(0.5))';
$string['nocondition'] = 'Sem condição';

// View strings
$string['clickarea'] = 'Clique em uma área';
$string['areainactive'] = 'Esta área está inativa (complete {$a} primeiro)';
$string['noareas'] = 'Nenhuma área foi definida ainda.';

// Error strings
$string['error:invalidshape'] = 'Tipo de forma inválido';
$string['error:invalidlinktype'] = 'Tipo de link inválido';
$string['error:noimage'] = 'Nenhuma imagem foi enviada';
$string['error:invalidcoords'] = 'Coordenadas inválidas';

// Privacy
$string['privacy:metadata'] = 'O módulo Mapa de Imagem não armazena nenhum dado pessoal.';
