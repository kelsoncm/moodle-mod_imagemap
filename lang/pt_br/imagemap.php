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
$string['modulename_help'] = 'O módulo Mapa de Imagem permite que professores criem imagens interativas com áreas clicáveis que direcionam para módulos do curso ou seções. As áreas refletem restrições de acesso e regras de conclusão.';
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
$string['target'] = 'Destino';
$string['target_help'] = 'Escolha uma seção ou atividade para esta área.';
$string['targetmissing'] = 'Destino não encontrado';
$string['title'] = 'Título';
$string['title_help'] = 'Título a ser exibido ao passar o mouse sobre a área';
$string['activefilter'] = 'CSS ativo';
$string['activefilter_help'] = 'CSS a ser aplicado quando a área estiver ativa.';
$string['inactivefilter'] = 'CSS inativo';
$string['inactivefilter_help'] = 'CSS a ser aplicado quando a área estiver inativa.';
$string['arearestricted'] = 'Este item possui restrições de acesso.';

// View strings
$string['clickarea'] = 'Clique em uma área';
$string['areainactive'] = 'Esta área está inativa (complete {$a} primeiro)';
$string['noareas'] = 'Nenhuma área foi definida ainda.';
$string['coursepreviewrestricted'] = '{$a} área(s) com restrição de acesso';

// Admin strings
$string['cssexamples'] = 'Exemplos de CSS';
$string['viewexamples'] = 'Ver Exemplos';
$string['managecssexamples'] = 'Gerenciar exemplos de CSS';
$string['noexamples'] = 'Nenhum exemplo foi criado ainda.';
$string['confirmdeleteexample'] = 'Tem certeza de que deseja excluir este exemplo?';
$string['order'] = 'Ordem';
$string['preview'] = 'Pré-visualização';
$string['edit'] = 'Editar';
$string['delete'] = 'Excluir';

// Error strings
$string['error:invalidshape'] = 'Tipo de forma inválido';
$string['error:invalidtargettype'] = 'Tipo de destino inválido';
$string['error:invalidtarget'] = 'Destino inválido';
$string['error:noimage'] = 'Nenhuma imagem foi enviada';
$string['error:invalidcoords'] = 'Coordenadas inválidas';

// Privacy
$string['privacy:metadata'] = 'O módulo Mapa de Imagem não armazena nenhum dado pessoal.';