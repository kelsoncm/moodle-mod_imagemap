# 💾 Backup e Restore - mod_imagemap

Documentação completa do sistema de Backup e Restore para o módulo Image Map do Moodle.

**Versão**: 1.2.0  
**Status**: Implementado e Testado ✅  
**Última Atualização**: Março 2, 2026

---

## 📋 Índice

- [Visão Geral](#visão-geral)
- [O Problema](#o-problema)
- [A Solução](#a-solução)
- [Arquitetura](#arquitetura)
- [Dados Feitos Backup](#dados-feitos-backup)
- [Mapeamento de IDs](#mapeamento-de-ids)
- [Decodificação de Links](#decodificação-de-links)
- [Logs e Eventos](#logs-e-eventos)
- [Testagem](#testagem)
- [Troubleshooting](#troubleshooting)
- [Referências](#referências)

---

## Visão Geral

Este documento descreve a implementação completa das funcionalidades de Backup e Restore para o módulo `mod_imagemap`, garantindo que todas as atividades Image Map possam ser portadas entre cursos/instâncias de Moodle com fidedignidade total e integridade de dados.

### Características Principais

✅ **Suporte Completo a Backup/Restore** - Implementação padrão do Moodle 4.1+  
✅ **Preservação de Dados** - Todos os dados e arquivos são preservados  
✅ **Mapeamento Inteligente de IDs** - Referências são remapeadas corretamente  
✅ **Conformidade com Padrões** - Segue as guidelines oficiais do Moodle  
✅ **Sem Dependências Externas** - Usa apenas APIs nativas do Moodle  

---

## O Problema

O módulo `mod_imagemap` foi desenvolvido com suporte declarado a `FEATURE_BACKUP_MOODLE2`, porém as classes necessárias para implementar o processo de backup não foram criadas.

### Erro Observado

```
Exception - Class "backup_imagemap_activity_task" not found
```

### Impacto

- ❌ Impossível fazer backup de cursos contendo atividades Image Map
- ❌ Impossível restaurar atividades Image Map em outro contexto
- ❌ Funcionalidade de backup completamente bloqueada
- ⚠️ Feature advertida como suportada mas não implementada

---

## A Solução

Foram implementadas as classes de backup e restore seguindo rigorosamente os padrões do Moodle 4.1+, permitindo que o módulo funcione completamente com o sistema integrado de backup/restore.

### Estrutura Criada

#### Arquivos de Backup - `/backup/moodle2/`

| Arquivo | Propósito |
|---------|----------|
| `backup_imagemap_activity_task.class.php` | Classe de tarefa de backup |
| `backup_imagemap_stepslib.php` | Definição estrutural dos dados |

#### Arquivos de Restore - `/restore/moodle2/`

| Arquivo | Propósito |
|---------|----------|
| `restore_imagemap_activity_task.class.php` | Classe de tarefa de restore |
| `restore_imagemap_stepslib.php` | Processamento de dados restaurados |

---

## Arquitetura

### Estrutura de Componentes

```
mod_imagemap
│
├─ FEATURE_BACKUP_MOODLE2 ✅
│
├─ backup/moodle2/
│  ├─ backup_imagemap_activity_task.class.php
│  │  ├─ define_my_settings()
│  │  ├─ define_my_steps()
│  │  └─ encode_content_links()
│  │
│  └─ backup_imagemap_stepslib.php
│     └─ backup_imagemap_activity_structure_step
│        ├─ define_structure()
│        ├─ imagemap element
│        ├─ imagemap_area elements
│        ├─ imagemap_line elements
│        ├─ annotate_files('image')
│        └─ annotate_ids('module')
│
├─ restore/moodle2/
│  ├─ restore_imagemap_activity_task.class.php
│  │  ├─ define_my_settings()
│  │  ├─ define_my_steps()
│  │  ├─ define_decode_contents()
│  │  ├─ define_decode_rules()
│  │  └─ define_restore_log_rules()
│  │
│  └─ restore_imagemap_stepslib.php
│     └─ restore_imagemap_activity_structure_step
│        ├─ define_structure()
│        ├─ process_imagemap()
│        ├─ process_imagemap_area()
│        ├─ process_imagemap_line()
│        └─ after_execute()
│
└─ Resultado → activities/imagemap_1/imagemap.xml (+ files)
```

### Fluxo de Backup

```
1. Moodle inicia backup de curso
   ↓
2. Detecta atividade imagemap com FEATURE_BACKUP_MOODLE2=true
   ↓
3. Carrega backup_imagemap_activity_task
   ↓
4. Executa define_my_steps() para adicionar structure step
   ↓
5. Executa backup_imagemap_activity_structure_step
   ├─ Coleta dados da tabela imagemap
   ├─ Coleta dados da tabela imagemap_area
   ├─ Coleta dados da tabela imagemap_line
   ├─ Coleta arquivos da área 'image'
   └─ Codifica links para formato $@NOME*id@$
   ↓
6. Serializa em XML estruturado
   ↓
7. Comprime em arquivo .mbz
   └─ Estrutura interna:
      └─ activities/imagemap_1/
         ├─ imagemap.xml (dados estruturados)
         └─ files/ (imagens com metadados)
```

### Fluxo de Restore

```
1. Moodle inicia restore
   ↓
2. Detecta atividade imagemap
   ↓
3. Carrega restore_imagemap_activity_task
   ↓
4. Executa define_restore_log_rules() para registrar eventos
   ↓
5. Executa restore_imagemap_activity_structure_step
   ├─ Processa imagemap
   │  ├─ Insere novo registro com course=curso_destino
   │  └─ Mapeia: id_antigo → id_novo
   │
   ├─ Processa imagemap_area (para cada área)
   │  ├─ Insere novo registro
   │  ├─ Se targettype='module': mapeia targetid (course_module)
   │  ├─ Se targettype='section': mapeia targetid (course_section)
   │  ├─ Se targettype='url': mantém targetid literal (URL não muda)
   │  └─ Mapeia: id_antigo → id_novo
   │
   ├─ Processa imagemap_line (para cada linha)
   │  ├─ Mapeia from_areaid
   │  ├─ Mapeia to_areaid
   │  └─ Insere novo registro (se áreas mapeadas com sucesso)
   │
   └─ Processa imagemap_css_example (exemplos CSS globais)
      ├─ Verifica se já existe (por type + name)
      ├─ Insere se novo para evitar duplicação
      └─ Disponibiliza para uso em outros cursos
   ↓
6. Executa after_execute()
   ├─ Restaura arquivos de imagem
   └─ Atualiza contexto de arquivo
   ↓
7. Decodifica links
   ├─ $@IMAGEMAPINDEX*x@$ → /mod/imagemap/index.php?id=y
   └─ $@IMAGEMAPVIEWBYID*x@$ → /mod/imagemap/view.php?id=y
   ↓
8. Registra eventos de log
```

---

## Dados Feitos Backup

### Tabela: imagemap

| Campo | Backup | Descrição |
|-------|--------|-----------|
| `id` | ✅ | ID (remapeado no restore) |
| `course` | ✅ | ID do curso (muda no restore) |
| `name` | ✅ | Nome da atividade |
| `intro` | ✅ | Descrição |
| `introformat` | ✅ | Formato de texto |
| `timemodified` | ✅ | Timestamp |
| `width` | ✅ | Largura da imagem |
| `height` | ✅ | Altura da imagem |

### Tabela: imagemap_area

| Campo | Backup | Descrição |
|-------|--------|-----------|
| `id` | ✅ | ID (remapeado) |
| `imagemapid` | ✅ | FK imagemap (muda) |
| `shape` | ✅ | circle, rect, poly |
| `coords` | ✅ | JSON com coordenadas |
| `targettype` | ✅ | module, section, url |
| `targetid` | ✅ | ID do alvo (remapeado conforme tipo) |
| `title` | ✅ | Título da área |
| `activefilter` | ✅ | CSS para ativo |
| `inactivefilter` | ✅ | CSS para inativo |
| `sortorder` | ✅ | Ordem de exibição |

**Notas sobre remapeamento de targetid:**
- `module` - Remapeado de course_module ID para novo curso ✅
- `section` - Remapeado de course_section ID para novo curso ✅
- `url` - Mantido literal (é um URL externo) ✅

### Tabela: imagemap_line

| Campo | Backup | Descrição |
|-------|--------|-----------|
| `id` | ✅ | ID (remapeado) |
| `imagemapid` | ✅ | FK imagemap (muda) |
| `from_areaid` | ✅ | FK área origem (remapeada) |
| `to_areaid` | ✅ | FK área destino (remapeada) |
| `timecreated` | ✅ | Timestamp |

### Tabela: imagemap_css_examples

✅ **Novo em v1.2.1**: Exemplos de CSS para filtros são incluídos no backup/restore

| Campo | Backup | Descrição |
|-------|--------|-----------|
| `id` | ✅ | ID (não precisa remapeamento) |
| `type` | ✅ | 'active' ou 'inactive' |
| `name` | ✅ | Nome do exemplo (ex: "Grayscale") |
| `css_text` | ✅ | Código CSS do filtro |
| `sortorder` | ✅ | Ordem de exibição |
| `timecreated` | ✅ | Timestamp |
| `timemodified` | ✅ | Timestamp |

**Notas sobre CSS examples:**
- Exemplos duplicados são evitados (verifica por type + name)
- Disponibilizado globalmente em todo sistema
- Não vinculado especificamente a cada atividade (compartilhado)

### Arquivos: Imagens

| Aspecto | Detalhes |
|--------|----------|
| **Área** | 'image' |
| **itemid** | 0 |
| **Formatos** | PNG, JPG, GIF, WebP, etc |
| **Backup** | ✅ Arquivo completo preservado |

---

## Mapeamento de IDs

### Como Funciona

Cada tabela/tipo de objeto tem um "mapa" que relaciona IDs antigos com IDs novos:

```
Backup:  id_antigo (ex: 1)
         ↓
Restore: Cria novo registro com id_novo (ex: 47)
         ↓
Map:     1 → 47
         ↓
Usa: get_mappingid('tipo', id_antigo) → id_novo
```

### Mappings Utilizados

| Tipo | Uso | Função |
|------|-----|--------|
| `imagemap` | ID da atividade | `set_mapping('imagemap', old, new)` |
| `imagemap_area` | ID de cada área | `set_mapping('imagemap_area', old, new)` |
| `imagemap_line` | ID de cada linha | `set_mapping('imagemap_line', old, new)` |
| `imagemap_css_example` | ID de exemplos CSS | `set_mapping('imagemap_css_example', old, new)` |
| `course_module` | Módulos referenciados (targettype='module') | `get_mappingid('course_module', old)` |
| `course_section` | Seções referenciadas (targettype='section') | `get_mappingid('course_section', old)` |

### Exemplo Prático

```
BACKUP (Curso 2):
  imagemap.id = 5
  imagemap_area.id = [10, 11, 12]
  imagemap_line: 10→11

RESTORE (Curso 7):
  Cria imagemap com id = 156
  Map: imagemap 5 → 156
  
  Cria areas com ids = [203, 204, 205]
  Map: area 10 → 203
  Map: area 11 → 204
  Map: area 12 → 205
  
  linha.from_areaid: 10 → 203 ✅
  linha.to_areaid: 11 → 204 ✅
  Cria linha mapeada
```

---

## Decodificação de Links

### Links Codificados no Backup

Durante backup, URLs são codificadas para formato "transportável":

```
Original: /mod/imagemap/index.php?id=2
Codificado: $@IMAGEMAPINDEX*2@$

Original: /mod/imagemap/view.php?id=5
Codificado: $@IMAGEMAPVIEWBYID*5@$
```

### Decodificação no Restore

Regras de decodificação mapeiam para novo contexto:

```php
new restore_decode_rule('IMAGEMAPINDEX', 
    '/mod/imagemap/index.php?id=$1', 
    'course');

new restore_decode_rule('IMAGEMAPVIEWBYID', 
    '/mod/imagemap/view.php?id=$1', 
    'course_module');
```

**Exemplo:**
```
Encoded: $@IMAGEMAPINDEX*2@$
Decode type: 'course' (significa: $1 é um course_id)
Mapping: 2 → 7 (novo curso)
Resultado: /mod/imagemap/index.php?id=7
```

---

## Logs e Eventos

### Eventos Registrados Automaticamente

| Evento | Descrição | Contexto |
|--------|-----------|----------|
| `imagemap_add` | Atividade criada | `view.php?id={course_module}` |
| `imagemap_update` | Atividade modificada | `view.php?id={course_module}` |
| `imagemap_view` | Atividade visualizada | `view.php?id={course_module}` |
| `imagemap_delete` | Atividade removida | `index.php?id={course}` |

Estes eventos aparecem em:
- **Site Admin → Logs** (visualização padrão Moodle)
- **Reports** que usam estes eventos

---

## Testagem

### Checklist Completo

**Preparação:**
- [ ] Módulo imagemap instalado
- [ ] Curso teste criado
- [ ] Atividade imagemap criada
- [ ] Imagem carregada
- [ ] 2+ áreas desenhadas
- [ ] 1+ linha conectando áreas

**Backup:**
- [ ] Site Admin → Cursos → Backup
- [ ] Selecionar curso com imagemap
- [ ] Iniciar backup
- [ ] ✅ Sem erro "Class not found"
- [ ] ✅ Arquivo .mbz criado

**Restore:**
- [ ] Site Admin → Cursos → Restore
- [ ] Selecionar arquivo .mbz
- [ ] Selecionar curso destino
- [ ] Restore toda atividade
- [ ] ✅ Sem erros

**Validação:**
- [ ] Imagemap aparece no curso destino
- [ ] Imagem restaurada
- [ ] Todas as áreas presentes e com coordenadas corretas
- [ ] Linhas conectam áreas corretamente
- [ ] Títulos das áreas preservados
- [ ] Filtros CSS aplicados

---

## Troubleshooting

### "Class not found: backup_imagemap_activity_task"

**Solução:**
1. Verificar `/backup/moodle2/backup_imagemap_activity_task.class.php` existe
2. Verificar `/backup/moodle2/backup_imagemap_stepslib.php` existe
3. Limpar cache: Site Admin → Development → Purge caches
4. Tentar novamente

### "Class not found: restore_imagemap_activity_task"

**Solução:**
1. Verificar `/restore/moodle2/restore_imagemap_activity_task.class.php` existe
2. Verificar `/restore/moodle2/restore_imagemap_stepslib.php` existe
3. Limpar cache Moodle
4. Tentar novamente

### Imagens não restauram

**Solução:**
1. Verificar `after_execute()` em `restore_imagemap_stepslib.php`
2. Verificar permissões de `/moodledata/`
3. Verificar `file_storage` tem espaço
4. Verificar logs: Site Admin → Logs

---

## Referências

- [Documentação Backup API - Moodle](https://docs.moodle.org/dev/Backup_API)
- [Documentação Restore API - Moodle](https://docs.moodle.org/dev/Restore_2.0)
- [File API and Backup - Moodle](https://docs.moodle.org/dev/File_API#Backup)
- [IMPLEMENTATION.md](IMPLEMENTATION.md) - Arquitetura técnica
- [docs/INDEX.md](docs/INDEX.md) - Índice de toda documentação

---

**Status**: ✅ Production Ready  
**Desenvolvido por**: Kelson C. M.  
**Licença**: GNU GPL v3 ou posterior
