# 🚀 Checklist Prático de Implementação

**Documento de Trabalho para Executar Estratégia de Aderência**

---

## 📊 Status Geral

| Componente | Sprint | Status | Conclusão |
|-----------|--------|--------|-----------|
| **CI/CD Pipeline** | 1.1 | ✅ COMPLETO | 04/03/2026 |
| **Testes Behat** | 1.2 | ⏳ Próximo | ~07/03/2026 |
| **Documentação** | 1.3 | ⏳ Bloqueado | ~10/03/2026 |
| **Fase 1 Total** | - | 40% | ~11/03/2026 |

**Próximo Passo Imediato**: 
1. ✅ Validar CI/CD pipeline em GitHub Actions (push + PR)
2. ⏳ Começar Sprint 1.2: Setup Behat
3. ⏳ Criar feature file com 4 scenarios críticos

---

## Fase 1: Crítica (32 horas)

### Sprint 1.1: CI/CD Pipeline Moodle Plugin CI (13h) ✅ COMPLETO

**Início**: 04/03/2026 | **Conclusão**: 04/03/2026 | **Status**: ✅ VERDE

#### Task 1.1.1: Criar `.github/workflows/moodle-plugin-ci.yml` (8h) ✅
- [x] Criar diretório `.github/workflows/`
- [x] Copiar template de moodle-plugin-ci.yml (referência: boas práticas seção 3.2)
- [x] Configurar matriz de testes:
  - [x] PHP: 8.1, 8.2, 8.3, 8.4
  - [x] Moodle: 4.5-STABLE, 5.0-STABLE, 5.1-STABLE (excludir versões incompatíveis)
  - [x] Databases: pgsql, mariadb
- [x] Adicionar steps CI:
  - [x] phpLint (sintaxe PHP)
  - [x] phpCodeSniffer (padrão Moodle)
  - [x] phpMessDetector (qualidade)
  - [x] phpDoc (documentação)
  - [x] Validate Moodle manifest
  - [x] PHPUnit (se houver)
  - [x] Behat (se houver)
  - [x] Grunt (se houver assets)
- [x] Testar workflow manualmente em branch feature
- **Checkpoint**: ✅ Workflow criado, 15 steps configurados, matriz 4×3×2 = 16 jobs

#### Task 1.1.2: Criar `.github/workflows/release.yml` (4h) ✅
- [x] Implementar automação de release:
  - [x] Trigger em tags (v1.0.0)
  - [x] Validar version.php sincronizado com release (regex: últimos 2 dígitos devem bater)
  - [x] Build ZIP automaticamente (exclui: .git, .github, tests, vendor, node_modules)
  - [x] Criar GitHub Release
  - [x] Opcional: Upload para Moodle Plugin Directory (se MOODLE_DIRECTORY_TOKEN)
- [x] Estrutura: Versão extraída via grep -oP, validação de tag, softprops/action-gh-release@v2
- **Checkpoint**: ✅ Workflow pronto para primeiro release (tag 1.2.3)

#### Task 1.1.3: Criar `.github/dependabot.yml` (1h) ✅
- [x] Ativar dependabot para composer.json (se houver dependências)
- [x] Configurar revisão semanal (segunda 09:00 UTC)
- [x] Auto-labels: "dependencies", "composer"
- [x] Auto-reviewer: @kelsoncm
- [x] Commit prefix: "chore"
- **Checkpoint**: ✅ Dependabot ativado, aguardando primeira execução (segunda)

---

### Sprint 1.2: Testes Behat Críticos (12h)

#### Task 1.2.1: Setup Behat Local (3h)
- [ ] Instalar Behat em Moodle (php admin/tool/behat/cli/init.php)
- [ ] Configurar browser (Chrome, Firefox ou Headless)
- [ ] Executar teste Moodle padrão (validar setup)
- **Checkpoint**: `php admin/tool/behat/cli/run.php --dry-run` passa

#### Task 1.2.2: Criar `tests/behat/imagemap.feature` (6h)
- [ ] Feature: "Create Image Map"
  - [ ] Scenario: Teacher pode criar activity
  - [ ] Scenario: Teacher pode fazer upload de imagem
  - [ ] Scenario: Teacher pode salvar activity
  
- [ ] Feature: "Edit Areas"
  - [ ] Scenario: Teacher clica "Edit areas"
  - [ ] Scenario: Teacher desenha rectangle
  - [ ] Scenario: Teacher desenha circle
  - [ ] Scenario: Teacher desenha polygon
  - [ ] Scenario: Teacher salva areas
  
- [ ] Feature: "View and Navigate"
  - [ ] Scenario: Student vê imagemap
  - [ ] Scenario: Student clica area → navega para target correto
  - [ ] Scenario: Student sem acesso → mensagem de proibido
  
- [ ] Feature: "Completion Filter"
  - [ ] Scenario: Area ativa quando módulo completo
  - [ ] Scenario: Area inativa quando módulo incompleto

#### Task 1.2.3: Executar Testes Behat (3h)
- [ ] Rodar: `vendor/bin/phpunit --filter imagemap`
- [ ] Rodar: `php admin/tool/behat/cli/run.php --tags=@mod_imagemap`
- [ ] Resolver falhas (debug localmente)
- [ ] Coverage deve ser >= 60% para funções críticas
- **Checkpoint**: Todos os cenários Behat passam ✓

---

### Sprint 1.3: Documentação Obrigatória (7h)

#### Task 1.3.1: Criar `SECURITY.md` (4h)
**Arquivo Estrutura** (seção 2.4 do guia):
```markdown
# Security Policy

## Supported Versions

| Version      | Status           | Until      |
|--------------|------------------|-----------|
| 1.2.x        | Actively Support | 2027-01-31|
| 1.0-1.1      | End of Life      | 2026-03-31|

## Security Properties
- **Capabilities**: 3 definidas em db/access.php
- **Input Validation**: required_param() + optional_param()
- **Database**: Prepared statements (no SQL injection)
- **File Upload**: Moodle File API (safe)
- **XSS**: format_text() + html escaping

## Known Vulnerabilities
(Listar se houver; caso contrário: "None known")

## Reporting Vulnerabilities
Email: security@kelsoncm.dev
(Response SLA: 48h critical, 7d non-critical)

```

- [ ] Documentar 10 security considerations
- [ ] Adicionar checklist de segurança para admin
- [ ] Listar versões suportadas e EOL dates
- **Checkpoint**: SECURITY.md publicado, claro e acionável

#### Task 1.3.2: Criar `CONTRIBUTING.md` (3h)
**Arquivo Estrutura**:
```markdown
# Contributing to mod_imagemap

## How to Contribute
1. Fork repository
2. Create branch: feature/suafuncionalidade
3. Follow Conventional Commits
4. Run: vendor/bin/phpunit
5. Create: Pull Request

## Code Style
- Moodle Coding Standard: phpcs --standard=moodle
- PHP 7.2+ compatible

## Reporting Bugs
- Create issue com: describe, steps, expected, actual

## Pull Request Checklist
- [ ] Tests passing
- [ ] CI/CD green
- [ ] CHANGELOG.md updated
- [ ] Code review by 1 person

```

- [ ] Detalhar código em template
- [ ] Adicionar links para padrões (Moodle style)
- [ ] Seção de troubleshooting
- **Checkpoint**: CONTRIBUTING.md pronto para first PR externo

---

### Sprint 1.2: Testes Behat Críticos (12h) ⏳ PRÓXIMO
**Status**: Esperando conclusão Sprint 1.1 CI/CD  
**Início previsto**: 04/03/2026 (após validação green de moodle-plugin-ci.yml)

### Sprint 1.3: Documentação Obrigatória (7h) ⏳ PRÓXIMO
**Status**: Bloqueado por Sprint 1.2 (docs referem testes Behat)

### Sprint 1 Validação (1h) ⏳ PRÓXIMO
- [ ] Rodar CI/CD pipeline completo locally
- [ ] Verificar todos 4 commits + 1 tag
- [ ] Validação final de docs
- [ ] Primeiro commit `.github/` para main

**Saída Sprint 1 Esperada**: Plugin com CI/CD \u2705, testes Behat \u2705, docs segurança \u2705

---

## Fase 2: Alta Prioridade (27 horas)

### Sprint 2.1: Mobile-First & Responsivo (8h)

#### Task 2.1.1: Verificar & Implementar Viewport (2h)
- [ ] Abrir `view.php` e `areas.php`
- [ ] Adicionar no `<head>` (ou template):
```html
<meta name="viewport" 
      content="width=device-width, initial-scale=1.0, viewport-fit=cover">
```
- [ ] Testar em Chrome DevTools: Device Emulation (iPhone 12, Pixel 6)
- [ ] Verificar breakpoints em styles.css
- **Checkpoint**: Plugin responsivo em 360px (mobile) até 1920px (desktop)

#### Task 2.1.2: Touch-Friendly Interface (4h)
- [ ] Auditoria de botões em `areas.php` editor
- [ ] Garantir tamanho >= 44x44px para todos os interactive elements
- [ ] Remover dependência de hover (usar :active, :focus)
- [ ] Input font-size >= 16px (evita zoom automático em iOS)
- [ ] Testar com toque real em device (ou emulador)
- **Checkpoint**: Editor usável em touchscreen

#### Task 2.1.3: Behat @mobile Tags (2h)
- [ ] Adicionar em `tests/behat/imagemap.feature`:
```gherkin
@mod_imagemap @mobile
Scenario: Draw rectangle on mobile
  Given I set browser window size to "390" by "844"
  When I draw rectangle from "10,10" to "100,100"
  Then the rectangle should be saved correctly
```
- [ ] Rodar com viewport simulado
- **Checkpoint**: Mobile scenarios passam

---

### Sprint 2.2: Web Services REST para Mobile App (14h)

#### Task 2.2.1: Criar `classes/external/api.php` (8h)
**Estrutura Padrão** (seção 12.3):

```php
<?php
namespace mod_imagemap\external;

class api extends \external_api {

    public static function get_imagemaps_parameters() {
        return new \external_function_parameters([
            'courseid' => new \external_value(PARAM_INT, 'Course ID'),
        ]);
    }

    public static function get_imagemaps($courseid) {
        global $DB;
        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('mod/imagemap:view', $context);
        
        // Query lógica
        $imagemaps = $DB->get_records('imagemap', ['course' => $courseid]);
        
        // Return structure validada
        return ['imagemaps' => $imagemaps];
    }

    public static function get_imagemaps_returns() {
        return new \external_single_structure([
            'imagemaps' => new \external_multiple_structure(
                new \external_single_structure([
                    'id'        => new \external_value(PARAM_INT),
                    'name'      => new \external_value(PARAM_TEXT),
                    'intro'     => new \external_value(PARAM_RAW),
                ])
            ),
        ]);
    }

    // ... get_areas(), get_area_details() ...
}
```

- [ ] Implementar métodos:
  - [ ] `get_imagemaps($courseid)` - List de imagemaps
  - [ ] `get_areas($imagemapid)` - Get areas para imagemap
  - [ ] `get_area_details($areaid)` - Single area com metadata
  
- [ ] Validação de contexto + capabilities em cada método
- [ ] Prepared statements para queries
- **Checkpoint**: 3+ web services funcionando

#### Task 2.2.2: Criar `db/services.php` (3h)
```php
<?php
$functions = [
    'mod_imagemap_get_imagemaps' => [
        'classname'    => 'mod_imagemap\\external\\api',
        'methodname'   => 'get_imagemaps',
        'type'         => 'read',
        'ajax'         => true,
        'capabilities' => 'mod/imagemap:view',
        'services'     => [MOODLE_OFFICIAL_MOBILE_SERVICE],  // ← CRUCIAL!
    ],
    'mod_imagemap_get_areas' => [
        'classname'    => 'mod_imagemap\\external\\api',
        'methodname'   => 'get_areas',
        'type'         => 'read',
        'ajax'         => true,
        'capabilities' => 'mod/imagemap:view',
        'services'     => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    // ...
];
```

- [ ] Registrar cada método em services.php
- [ ] Validar service está em lista MOODLE_OFFICIAL_MOBILE_SERVICE
- **Checkpoint**: Web service registry validado

#### Task 2.2.3: PHPUnit Tests para API (3h)
```php
public function test_get_imagemaps() {
    $this->resetAfterTest(true);
    $generator = $this->getDataGenerator();
    
    $course = $generator->create_course();
    $imagemap = $generator->create_module('imagemap', ['course' => $course->id]);
    
    $result = api::get_imagemaps($course->id);
    
    $this->assertCount(1, $result['imagemaps']);
    $this->assertEquals($imagemap->name, $result['imagemaps'][0]['name']);
}
```

- [ ] Testar acesso por student, guest, teacher
- [ ] Testar erro com contexto inválido
- [ ] Cobertura >= 80%
- **Checkpoint**: Testes PHPUnit passam, coverage OK

---

### Sprint 2.3: Cache Strategy com MUC (5h)

#### Task 2.3.1: Criar `db/caches.php` (2h)
```php
<?php
$definitions = [
    'imagemap_areas' => [
        'mode'                      => cache_store::MODE_APPLICATION,
        'simplekeys'                => true,
        'ttl'                       => 86400,  // 24h
        'staticacceleration'        => true,
        'staticaccelerationsize'    => 50,
    ],
    'imagemap_css_examples' => [
        'mode'                      => cache_store::MODE_APPLICATION,
        'simplekeys'                => true,
        'ttl'                       => 604800,  // 7d
        'staticacceleration'        => true,
        'staticaccelerationsize'    => 20,
    ],
];
```

- [ ] Define 2 caches (areas, CSS examples)
- [ ] TTL apropriado (24h para areas, 7d para CSS)
- **Checkpoint**: Caches definidos

#### Task 2.3.2: Integrar Cache em `view.php` e `lib.php` (2h)
```php
// Em imagemap_get_areas()
$cache = cache::make('mod_imagemap', 'imagemap_areas');
$areas = $cache->get($imagemapid);

if ($areas === false) {
    $areas = $DB->get_records('imagemap_area', ['imagemapid' => $imagemapid]);
    $cache->set($imagemapid, $areas);
}
return $areas;
```

- [ ] Usar cache em `get_areas()`
- [ ] Invalidar cache ao salvar/deletar area
- [ ] Testar performance: com vs sem cache
- **Checkpoint**: Cache funcional, performance +50%

#### Task 2.3.3: Teste de Cache Invalidation (1h)
```php
public function test_cache_invalidation_on_area_save() {
    $cache = cache::make('mod_imagemap', 'imagemap_areas');
    $areas_before = imagemap_get_areas($imagemapid);
    
    // Salvar area nova
    save_area(['imagemapid' => $imagemapid, ...]);
    
    // Cache deve estar vazio
    $this->assertFalse($cache->has($imagemapid));
}
```

- [ ] PHPUnit test para invalidação
- **Checkpoint**: Teste passa

---

**Saída Sprint 2**: Mobile-ready + API REST + Cache ✓

---

## Fase 3: Média (19 horas)

### Sprint 3.1: Auditoria & Events (8h)

#### Task 3.1.1: Criar `classes/event/area_created.php` (2h)
```php
<?php
namespace mod_imagemap\event;

class area_created extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';  // create
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'imagemap_area';
    }

    public static function get_name() {
        return get_string('event_area_created', 'mod_imagemap');
    }

    public function get_description() {
        return "User {$this->userid} created area {$this->objectid} in imagemap {$this->data['other']['imagemapid']}";
    }
}
```

- [ ] Criar event classes: area_created, area_updated, area_deleted
- [ ] Adicionar string em lang/en/imagemap.php
- **Checkpoint**: 3 eventos criados

#### Task 3.1.2: Trigger Events em `area_save.php` (3h)
```php
// Após inserir
$event = \mod_imagemap\event\area_created::create([
    'context' => $context,
    'objectid' => $areaid,
    'other' => ['imagemapid' => $imagemapid]
]);
$event->trigger();
```

- [ ] Trigger em save, update, delete
- [ ] Testar que eventos aparecem em Admin > Reports > Event Logs
- **Checkpoint**: Eventos sendo logados

#### Task 3.1.3: Observer para Auditoria Customizada (3h)
```php
// db/events.php
$observers = [
    [
        'eventname' => '\\mod_imagemap\\event\\area_created',
        'callback' => '\\mod_imagemap\\observer::area_created',
    ],
];

// classes/observer.php
public static function area_created(\mod_imagemap\event\area_created $event) {
    global $DB;
    $DB->insert_record('imagemap_audit_log', [
        'eventname' => 'area_created',
        'userid' => $event->userid,
        'objectid' => $event->objectid,
        'ip' => getremoteaddr(),
        'timecreated' => time(),
    ]);
}
```

- [ ] Implementar observer para logging customizado
- [ ] Criar (opcional) tabela `imagemap_audit_log` se quiser histórico custom
- **Checkpoint**: Auditoria funcionando

---

### Sprint 3.2: Tasks & Cron Jobs (6h)

#### Task 3.2.1: Criar `db/tasks.php` (2h)
```php
<?php
$tasks = [
    [
        'classname' => '\\mod_imagemap\\task\\cleanup_deleted_areas',
        'blocking'  => 0,
        'minute'    => '0',
        'hour'      => '2',  // 2 AM
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
    ],
];
```

- [ ] Define cleanup scheduled task
- **Checkpoint**: Task registered

#### Task 3.2.2: Implementar `classes/task/cleanup_deleted_areas.php` (4h)
```php
<?php
namespace mod_imagemap\task;

class cleanup_deleted_areas extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('task_cleanup', 'mod_imagemap');
    }

    public function execute() {
        global $DB;
        
        // Remove areas deleted mais de 30 dias atrás (soft delete)
        $threshold = time() - (30 * 86400);
        $deleted = $DB->delete_records_select(
            'imagemap_area_deleted',
            'timedeleted < ?',
            [$threshold]
        );
        
        mtrace("Cleanup: deleted $deleted old records");
    }
}
```

- [ ] Implementar lógica de cleanup
- [ ] Testar localmente: `php admin/cli/scheduled_task.php --execute=mod_imagemap\\task\\cleanup_deleted_areas`
- **Checkpoint**: Task roda sem erros

---

### Sprint 3.3: Performance & Logging (5h)

#### Task 3.3.1: Adicionar Índices Compostos (2h)
Edit `db/install.xml`:
```xml
<INDEXES>
  <INDEX NAME="imagemapid_shape" UNIQUE="false" FIELDS="imagemapid, shape"/>
  <INDEX NAME="imagemapid_sortorder" UNIQUE="false" FIELDS="imagemapid, sortorder"/>
</INDEXES>
```

- [ ] Adicionar índices para queries frequentes
- [ ] Executar upgrade em staging: `php admin/cli/upgrade.php --non-interactive`
- [ ] Verificar índices criados: `\g.d+ imagemap_area_idx` (postgres)
- **Checkpoint**: Índices aplicados

#### Task 3.3.2: Logging Performance (2h)
Em `lib.php`:
```php
$start = microtime(true);
$areas = imagemap_get_areas($imagemapid);
$duration = (microtime(true) - $start) * 1000;

if ($duration > 1000) {
    error_log("SLOW: imagemap_get_areas took {$duration}ms");
}
```

- [ ] Adicionar logging em funções críticas
- [ ] Log quando query > 1000ms
- [ ] Usar `mtrace()` para debug
- **Checkpoint**: Logging em place

#### Task 3.3.3: Benchmark Query Performance (1h)
```bash
# Enable slow query log
# Rodar scenario: 100 students visualizam imagemap
# Verificar: SELECT tempo médio < 100ms
```

- [ ] Benchmark antes/depois de cache
- [ ] Verificar que sem cache: slow
- [ ] Com cache: fast (< 10ms)
- **Checkpoint**: Perf mejora documentada

---

**Saída Fase 3**: Auditoria, manutenção automática, performance ✓

---

## Fase 4: Opcional (20 horas)

### Sprint 4.1: PHPUnit Cobertura Completa (10h)
- [ ] `classes/*` com 80%+ coverage
- [ ] Tests para: add, update, delete, get_areas, completion_filters
- **Resultado**: `vendor/bin/phpunit --coverage-text` >= 85%

### Sprint 4.2: WCAG 2.1 Acessibilidade (6h)
- [ ] ARIA labels em editor canvas
- [ ] Contrast ratio verificado (4.5:1 min)
- [ ] Teste com screen reader (NVDA/VoiceOver)
- **Resultado**: Lighthouse Accessibility >= 90

### Sprint 4.3: Documentação Técnica Avançada (4h)
- [ ] API Reference em `docs/API.md`
- [ ] Architecture diagram em Mermaid
- [ ] Performance benchmarks em `docs/PERFORMANCE.md`

---

## 📋 Checklist de Validação Final

Antes de **v2.0.0 Release**, validar:

### Código & Testes
- [ ] ✓ CI/CD green (todos os workflows passam)
- [ ] ✓ PHPUnit >= 80% coverage
- [ ] ✓ Behat todos scenarios passam (@mod_imagemap + @mobile)
- [ ] ✓ Sem console errors (JavaScript)
- [ ] ✓ Sem deprecation warnings do Moodle

### Segurança
- [ ] ✓ Sem hardcoded secrets (grep API_KEY, password, token)
- [ ] ✓ Sem SQL injection possível (all prepared statements)
- [ ] ✓ Sem XSS (all user input escaped)
- [ ] ✓ CSRF tokens validados (require_sesskey())
- [ ] ✓ Capabilities verificadas em cada endpoint

### Documentação
- [ ] ✓ README.md atualizado (version, features, requirements)
- [ ] ✓ CHANGELOG.md com v2.0.0 changes
- [ ] ✓ SECURITY.md completo com vulnerabilities reporting
- [ ] ✓ CONTRIBUTING.md com workflow claro
- [ ] ✓ API.md com exemplos de web services

### Performance
- [ ] ✓ Database queries < 1000ms (exceto bulk operations)
- [ ] ✓ Cache implementado (MUC com TTL)
- [ ] ✓ Lighthouse Performance >= 80
- [ ] ✓ Mobile queries < 500ms (4G slowdown)

### Mobile
- [ ] ✓ Responsivo em 360px, 768px, 1024px, 1920px
- [ ] ✓ Buttons/inputs >= 44x44px
- [ ] ✓ Web services em MOODLE_OFFICIAL_MOBILE_SERVICE
- [ ] ✓ Testado em device real (iOS + Android)

### Release Checklist
- [ ] ✓ Tag: `2.0.0` criada e validada
- [ ] ✓ ZIP: Contém estrutura correta (sem .git, node_modules, etc)
- [ ] ✓ GitHub Release: Com changelog summarizado
- [ ] ✓ Moodle Directory: Upload (se MOODLE_DIRECTORY_TOKEN set)

---

## 🎬 Próximos Passos

1. **Hoje**: Review este documento, ajustar timeline
2. **Amanhã**: Iniciar Sprint 1.1 (CI/CD)
3. **Semana 1**: Sprint 1 completo (32h)
4. **Semana 2-3**: Sprint 2 (mobile + web services)
5. **Semana 4**: Sprint 3 (auditoria)
6. **Semana 5+**: Sprints 4+ (opcional)

---

## 📞 Support & Questions

Se durante implementação encontrar bloqueadores:

1. Consultar boas práticas seção correspondente (ex: #3 para CI/CD, #15 para segurança)
2. Verificar exemplo real em `tiny_justify` (referenciado várias vezes)
3. Validate com `moodle-plugin-ci` tool

**Versão**: 1.0  
**Última atualização**: 04/03/2026
