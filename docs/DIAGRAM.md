# 📊 Diagramas - Arquitetura Backup/Restore

Representações visuais da arquitetura de Backup e Restore do mod_imagemap.

---

## Estrutura de Componentes

```
┌─────────────────────────────────────────────────────────────┐
│                     mod_imagemap                            │
│                                                             │
│  FEATURE_BACKUP_MOODLE2 = true ✅                          │
└─────────────┬───────────────────────────────────┬───────────┘
              │                                   │
        ┌─────▼──────┐                    ┌──────▼──────┐
        │   BACKUP   │                    │   RESTORE   │
        └─────┬──────┘                    └──────┬──────┘
              │                                   │
     ┌────────▼────────┐              ┌──────────▼────────┐
     │ backup/moodle2/ │              │ restore/moodle2/  │
     ├─────────────────┤              ├───────────────────┤
     │ ✓ Task Class    │              │ ✓ Task Class      │
     │ ✓ Steps Lib     │              │ ✓ Steps Lib       │
     └────────┬────────┘              └──────────┬────────┘
              │                                   │
     ┌────────▼─────────────┐          ┌─────────▼────────┐
     │ Dados Coletados:     │          │ Dados Processados:
     │ • imagemap           │          │ • Novos IDs       │
     │ • imagemap_area      │          │ • ID Mapeamento   │
     │ • imagemap_line      │          │ • Remapeamento    │
     │ • Arquivos (image)   │          │ • Link Decodificação
     └────────┬─────────────┘          └─────────┬────────┘
              │                                   │
        ┌─────▼─────────────┐           ┌────────▼────────┐
        │ Serialização XML  │           │ Dados Restaurados
        └─────┬─────────────┘           │ em novo contexto
              │                         └─────────────────┘
        ┌─────▼──────────────┐
        │ Comprimido em .mbz │
        │ com estrutura      │
        │ de arquivo         │
        └───────────────────┘
```

---

## Fluxo de Backup - Sequência Detalhada

```
┌──────────────────────────────────────────────────────────────┐
│ 1. Moodle Detecta Atividade                                  │
│    → Identifica FEATURE_BACKUP_MOODLE2 = true                │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 2. Carrega Classe                                              │
│    → backup_imagemap_activity_task                            │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 3. Executa define_my_settings()                                │
│    → Neste caso: sem configurações customizadas               │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 4. Executa define_my_steps()                                   │
│    → Adiciona: backup_imagemap_activity_structure_step         │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 5. Executa Step: define_structure()                            │
│                                                                │
│    a) Coleta tabela 'imagemap'                                │
│       ├─ id, course, name, intro, introformat                │
│       ├─ timemodified, width, height                          │
│       └─ Mapeia: course id, modulo id, file ids              │
│                                                                │
│    b) Coleta tabela 'imagemap_area'                           │
│       ├─ id, imagemapid, shape, coords                       │
│       ├─ targettype, targetid, title                         │
│       ├─ activefilter, inactivefilter, sortorder             │
│       └─ Mapeia: 'course_module' para targetid               │
│                                                                │
│    c) Coleta tabela 'imagemap_line'                           │
│       ├─ id, imagemapid, from_areaid, to_areaid             │
│       └─ timecreated                                          │
│                                                                │
│    d) Coleta arquivos da área 'image'                         │
│       ├─ Todas as imagens associadas                         │
│       └─ Metadados de arquivo preservados                    │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 6. Executa encode_content_links()                              │
│    Transforma links para formato transportável:                │
│                                                                │
│    /mod/imagemap/index.php?id=2                              │
│         ↓↓↓                                                    │
│    $@IMAGEMAPINDEX*2@$                                        │
│                                                                │
│    /mod/imagemap/view.php?id=5                               │
│         ↓↓↓                                                    │
│    $@IMAGEMAPVIEWBYID*5@$                                    │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 7. Serialização em XML                                         │
│    Estrutura:                                                  │
│    activities/imagemap_1/imagemap.xml                         │
│                                                                │
│    <imagemap id="1">                                          │
│      <course>2</course>                                       │
│      <name>Mapa do Curso</name>                               │
│      <areas>                                                  │
│        <area id="10">...targets, shapes...</area>             │
│      </areas>                                                 │
│      <lines>                                                  │
│        <line id="1">...from_area to_area...</line>            │
│      </lines>                                                 │
│    </imagemap>                                                │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 8. Agrupa em Estrutura de Backup                               │
│    activities/imagemap_1/                                      │
│    ├─ imagemap.xml (dados estruturados)                       │
│    └─ files/ (todas as imagens com metadados)                │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 9. Comprimido em Arquivo .mbz                                  │
│    course-backup-[data]-[hora].mbz                            │
│    (Arquivo Moodle Backup contendo tudo acima)                │
└──────────────────────────────────────────────────────────────┘
```

---

## Fluxo de Restore - Sequência Detalhada

```
┌──────────────────────────────────────────────────────────────┐
│ 1. Moodle Inicia Restore                                      │
│    → Carrega arquivo .mbz e identifica atividades            │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 2. Carrega Classe                                              │
│    → restore_imagemap_activity_task                            │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 3. Executa define_my_settings()                                │
│    → Neste caso: sem configurações customizadas               │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 4. Executa define_my_steps()                                   │
│    → Adiciona: restore_imagemap_activity_structure_step        │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 5. Executa Step: define_structure()                            │
│    → Prepara estrutura para processar XML de backup           │
│    → Define paths para xml, areas, linhas                     │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 6. Processa Elemento: <imagemap>                               │
│    (process_imagemap)                                          │
│                                                                │
│    a) Cria novo registro em tabela imagemap                   │
│       ├─ course = curso_destino (muda!)                       │
│       ├─ name, intro, introformat, ... (preservados)          │
│       └─ Gera novo id_novo                                    │
│                                                                │
│    b) Registra mapeamento                                     │
│       └─ set_mapping('imagemap', id_antigo, id_novo)         │
│                                                                │
│    📝 Exemplo:                                                │
│       input:  id=5, course=2, name="Mapa"                    │
│       output: id=156, course=7, name="Mapa"                  │
│       map: imagemap 5 → 156                                   │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 7. Processa Elementos: <area> (Para cada área)                │
│    (process_imagemap_area)                                     │
│                                                                │
│    a) Cria novo registro em tabela imagemap_area              │
│       └─ imagemapid = id_novo do pai (remapeado!)            │
│                                                                │
│    b) Se targettype='module'                                  │
│       ├─ Busca mapeamento de módulo                          │
│       ├─ targetid = get_mappingid('course_module', antigo)   │
│       ├─ Se módulo não está no curso destino: targetid=0     │
│       └─ Preserva outras propriedades                        │
│                                                                │
│    c) Outros targettype (seção, URL): preservados             │
│                                                                │
│    d) Registra mapeamento                                     │
│       └─ set_mapping('imagemap_area', antigo, novo)          │
│                                                                │
│    📝 Exemplo:                                                │
│       input:  id=10, imagemapid=5, shape=circle              │
│       output: id=203, imagemapid=156, shape=circle           │
│       map: area 10 → 203                                      │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 8. Processa Elementos: <line> (Para cada linha)               │
│    (process_imagemap_line)                                     │
│                                                                │
│    a) Busca mapeamentos de áreas origem/destino               │
│       ├─ from_areaid_novo = get_mappingid('imagemap_area', antigo)
│       └─ to_areaid_novo = get_mappingid('imagemap_area', antigo)
│                                                                │
│    b) Valida se ambas as áreas foram mapeadas                │
│       └─ Se SIM: insere linha; Se NÃO: pula                 │
│                                                                │
│    c) Cria registro em tabela imagemap_line                  │
│       ├─ imagemapid = id_novo do pai                         │
│       ├─ from_areaid = id_novo mapeado                       │
│       ├─ to_areaid = id_novo mapeado                         │
│       └─ timecreated = timestamp original                    │
│                                                                │
│    d) Registra mapeamento                                    │
│       └─ set_mapping('imagemap_line', antigo, novo)          │
│                                                                │
│    📝 Exemplo:                                                │
│       input:  id=1, from_areaid=10, to_areaid=11            │
│       output: id=301, from_areaid=203, to_areaid=204        │
│       maps: area 10→203, area 11→204 ✅                      │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 9. Executa after_execute()                                     │
│    Restauração de Arquivos e Links                             │
│                                                                │
│    a) Restaura arquivos da área 'image'                       │
│       ├─ add_related_files('mod_imagemap', 'image', null)    │
│       ├─ Copia arquivos para novo contexto                   │
│       └─ Atualiza referências de arquivo                     │
│                                                                │
│    b) Decodifica links inseridos em conteúdo                 │
│       ├─ $@IMAGEMAPINDEX*x@$ → /mod/imagemap/index.php?id=y
│       └─ $@IMAGEMAPVIEWBYID*x@$ → /mod/imagemap/view.php?id=y
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 10. Registra Eventos de Log                                    │
│     (define_restore_log_rules)                                 │
│                                                                │
│     Eventos criados:                                          │
│     • imagemap_add (atividade criada)                         │
│     • Event logs são salvos no banco de dados                 │
│     • Aparecem em Site Admin → Reports → Logs                │
└─────────────────────┬──────────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────────┐
│ 11. Finaliza Restore                                           │
│     ✅ Atividade Image Map Completamente Restaurada            │
│                                                                │
│     Estado Final:                                              │
│     • Novo imagemap com ID único no banco                    │
│     • Todas as áreas com novos IDs                            │
│     • Todas as linhas com áreas mapeadas                      │
│     • Imagens restauradas no novo contexto                    │
│     • Links decodificados para novo domínio                   │
│     • Logs registrados                                        │
│     • Dados idênticos ao original                             │
└──────────────────────────────────────────────────────────────┘
```

---

## Mapeamento de IDs - Visualização

```
ANTES DO RESTORE (Curso 2)
────────────────────────────────────
imagemap:
  id=5  course=2  name="Mapa 1"

imagemap_area:
  id=10  imagemapid=5  shape=circle   targetid=15 (módulo)
  id=11  imagemapid=5  shape=rect     targetid=16 (módulo)
  id=12  imagemapid=5  shape=polygon  targetid=section

imagemap_line:
  id=1  imagemapid=5  from_areaid=10  to_areaid=11
  id=2  imagemapid=5  from_areaid=11  to_areaid=12


DURANTE RESTORE (Curso 7)
──────────────────────────────────────
Processamento:
  módulo 15 (curso 2) → módulo 42 (curso 7) ✓ Mapeado
  módulo 16 (curso 2) → módulo 43 (curso 7) ✓ Mapeado


DEPOIS DO RESTORE (Curso 7)
─────────────────────────────────────
imagemap:
  id=156  course=7  name="Mapa 1"
  └─ Map: 5 → 156 ✓

imagemap_area:
  id=203  imagemapid=156  shape=circle   targetid=42 ✓
  id=204  imagemapid=156  shape=rect     targetid=43 ✓
  id=205  imagemapid=156  shape=polygon  targetid=... ✓
  └─ Maps: 10→203, 11→204, 12→205 ✓

imagemap_line:
  id=301  imagemapid=156  from_areaid=203  to_areaid=204 ✓
  id=302  imagemapid=156  from_areaid=204  to_areaid=205 ✓
  └─ Maps: 1→301, 2→302 ✓


RESULTADO FINAL
───────────────
✅ Todos os IDs remapeados
✅ Todas as referências corrigidas
✅ Estrutura preservada
✅ Funcionalidade mantida
```

---

## Decodificação de Links - Exemplo

```
BACKUP (Curso ID=2)
─────────────────────────────────────────
Link: /mod/imagemap/index.php?id=2
Codificado: $@IMAGEMAPINDEX*2@$

Link: /mod/imagemap/view.php?id=5
Codificado: $@IMAGEMAPVIEWBYID*5@$


ARCHIVE (.mbz)
─────────────────────────────────────────
Armazenado como "transportável":
  $@IMAGEMAPINDEX*2@$
  $@IMAGEMAPVIEWBYID*5@$


RESTORE (Curso ID=7)
─────────────────────────────────────────
Decodificação:
  $@IMAGEMAPINDEX*2@$ 
    ↓ Aplica regra: type='course', id=2 mapeado para 7
  /mod/imagemap/index.php?id=7 ✓

  $@IMAGEMAPVIEWBYID*5@$ 
    ↓ Aplica regra: type='course_module', id=5 mapeado para 156
  /mod/imagemap/view.php?id=156 ✓


RESULTADO
─────────────────────────────────────────
✅ Links corrigidos para novo contexto
✅ Funcionalidade preservada
✅ Navegação funciona no novo curso
```

---

## Tabelas - Estrutura de Dados

### Backup de Exemplo

```xml
<imagemap id="5">
  <course>2</course>
  <name>Mapa Interativo - Navegacao do Curso</name>
  <intro>Clique para explorar modules</intro>
  <introformat>1</introformat>
  <timemodified>1735392000</timemodified>
  <width>1024</width>
  <height>768</height>

  <areas>
    <area id="10">
      <imagemapid>5</imagemapid>
      <shape>circle</shape>
      <coords>{"cx": 150, "cy": 200, "r": 50}</coords>
      <targettype>module</targettype>
      <targetid>15</targetid>
      <title>Aula 1: Introducao</title>
      <activefilter>filter: brightness(1.2)</activefilter>
      <inactivefilter>filter: grayscale(100%)</inactivefilter>
      <sortorder>1</sortorder>
    </area>

    <area id="11">
      <imagemapid>5</imagemapid>
      <shape>rect</shape>
      <coords>{"x1": 300, "y1": 100, "x2": 500, "y2": 300}</coords>
      <targettype>module</targettype>
      <targetid>16</targetid>
      <title>Aula 2: Conceitos</title>
      <activefilter>filter: brightness(1.1)</activefilter>
      <inactivefilter>filter: opacity(0.5)</inactivefilter>
      <sortorder>2</sortorder>
    </area>
  </areas>

  <lines>
    <line id="1">
      <imagemapid>5</imagemapid>
      <from_areaid>10</from_areaid>
      <to_areaid>11</to_areaid>
      <timecreated>1735392100</timecreated>
    </line>
  </lines>
</imagemap>
```

---

**Documentação**: Março 2, 2026  
**Versão**: 1.2.0  
**Status**: ✅ Production Ready
