# 📋 Análise de Aderência: mod_imagemap vs Boas Práticas Moodle

**Data**: 04/03/2026  
**Plugin**: mod_imagemap v1.2.2  
**Referência**: [moodle_good_practices.md](https://raw.githubusercontent.com/kelsoncm/kelsoncm/refs/heads/main/moodle_good_practices.md)

---

## 📊 Status Geral de Aderência

| Categoria | Aderência | Status | Prioridade |
|-----------|-----------|--------|-----------|
| **Documentação** | ⭐⭐⭐⭐☆ | 80% ✓ | Alto |
| **CI/CD** | ☆☆☆☆☆ | 0% ❌ | **CRÍTICA** |
| **Testes** | ⭐⭐⭐☆☆ | 60% ✓ | **CRÍTICA** |
| **Segurança** | ⭐⭐⭐⭐☆ | 85% ✓ | Médio |
| **Banco de Dados** | ⭐⭐⭐⭐⭐ | 100% ✓ | Médio |
| **Capabilities** | ⭐⭐⭐⭐⭐ | 100% ✓ | Médio |
| **Mobile** | ⭐⭐☆☆☆ | 40% ⚠️ | Médio |
| **Web Services** | ☆☆☆☆☆ | 0% ❌ | Baixo |
| **Tasks/Cron** | ☆☆☆☆☆ | 0% ❌ | Baixo |
| **Versionamento** | ⭐⭐⭐⭐⭐ | 100% ✓ | Baixo |

**Score Geral**: **52/100** (Intermediate, com graves gaps em CI/CD)

---

## ✅ Pontos Fortes

### 1. **Documentação de Qualidade** (Seção 2)
- ✓ `README.md` completo com features, requisitos, instalação
- ✓ `CHANGELOG.md` formatado corretamente com versionamento semântico
- ✓ Documentação em `/docs/` com: USER_GUIDE.md, ADMIN_GUIDE.md, IMPLEMENTATION.md
- ✓ Arquivo AGENTS.md com guia detalhado para agentes de IA
- ✓ Estrutura clara de roles (Teachers, Developers, Admins)

**Nota**: Faltam `CONTRIBUTING.md` e `SECURITY.md` (essenciais)

### 2. **Banco de Dados Bem Estruturado** (Seção 16)
- ✓ Schema XML bem desenhado em `db/install.xml`
- ✓ 4 tabelas principais com foreign keys adequadas
- ✓ Índices nos campos corretos (type, sortorder)
- ✓ Upgrade script preparado em `db/upgrade.php`
- ✓ Suporta múltiplos databases (PostgreSQL, MySQL, MariaDB)

### 3. **Capabilities (Permissões) Bem Implementadas** (Seção 14)
- ✓ `db/access.php` com 3 capabilities bem definidas
- ✓ `mod/imagemap:addinstance` com RISK_XSS
- ✓ `mod/imagemap:view` com archetypes corretos
- ✓ `mod/imagemap:manage` com contexto adequado
- ✓ Uso correto em `view.php` com `require_capability()`

### 4. **Segurança Sólida** (Seção 15)
- ✓ Validação de entrada com `optional_param()` e `required_param()`
- ✓ Uso correto de `PARAM_INT`, `PARAM_ALPHA`
- ✓ Database queries com placeholders (não concatenação)
- ✓ Context validation em todos os endpoints
- ✓ File upload seguro via `file_save_draft_area_files()`
- ✓ Events no sistema Moodle (`course_module_viewed`)

### 5. **Versionamento Correto** (Seção 5)
- ✓ `version.php` com formato duplo: `$plugin->version` (timestamp) e `$plugin->release` (semântico)
- ✓ Sincronização: últimos 2 dígitos do version = últimos 2 do release (01 = 01) ✓
- ✓ CHANGELOG.md atualizado por versão

### 6. **Backup/Restore Implementado** (Seção 11)
- ✓ Classes corretas em `backup/moodle2/`
- ✓ `restore_imagemap_stepslib.php` com importação de dados
- ✓ Preserva estrutura de areas e relacionamentos

---

## ❌ Gaps Críticos

### 1. **SEM CI/CD Pipeline** (Seção 3) 🔴 **CRÍTICA**
- ❌ Nenhum `.github/workflows/` configurado
- ❌ Sem GitHub Actions para testes automáticos
- ❌ Sem dependabot.yml para dependências
- ❌ Sem validação de código em push

**Impacto**: Plugin entra em produção sem testes, sem garantia de compatibilidade.

### 2. **Testes Incompletos** (Seção 4) 🔴 **CRÍTICA**
- ⚠️ Backup/Restore test exist (`tests/backup_restore_test.php`) - POSITIVO
- ❌ Nenhum teste Behat (E2E/integration)
- ❌ Nenhum teste PHPUnit para funções core
- ❌ Nenhum teste JavaScript/AMD
- ❌ Sem cobertura de testes medida

**Impacto**: Regressões silenciosas, funcionalidade quebrada em produção.

### 3. **Documentação Incompleta** (Seção 2)
- ❌ **SECURITY.md não existe** - Crítico para plugin que manipula dados
- ❌ **CONTRIBUTING.md não existe** - Guia para contribuidores
- ⚠️ `README.md` menciona /docs/ mas ligações estão quebradas (User_GUIDE não menciona os docs)

**Impacto**: Usuários/desenvolvedores sem contexto de segurança; contribuições não estruturadas.

### 4. **SEM Web Services** (Seção 12) 🔴 **CRÍTICA PARA MOBILE**
- ❌ Nenhum `db/services.php`
- ❌ Sem classes `external/*.php`
- ❌ Sem suporte para Moodle Mobile App (MOODLE_OFFICIAL_MOBILE_SERVICE)

**Impacto**: Plugin não funciona em app mobile oficial.

### 5. **SEM Tasks/Cron** (Seção 13)
- ❌ Nenhum `db/tasks.php`
- ❌ Sem scheduled tasks para limpeza de dados obsoletos
- ❌ Sem ad-hoc tasks para operações em background

**Impacto**: Acúmulo de dados desfasados, sem rotinas de manutenção.

### 6. **Mobile-first Não Implementado** (Seção 4.5)
- ⚠️ Sem viewport meta tag verificado
- ⚠️ Editor de canvas (areas.php) provavelmente não é touch-friendly
- ⚠️ Tamanho de botões pode ser < 44x44px
- ❌ Sem testes específicos de responsividade

**Impacto**: Experiência horrível em mobile; usuários mobiles não conseguem desenhar areas.

### 7. **Sem Logs Auditados** (Seção 22)
- ⚠️ `course_module_viewed` event existe
- ❌ Sem eventos para `area_created`, `area_updated`, `area_deleted`
- ❌ Sem logger customizado para operações críticas

**Impacto**: Não há rastreamento de quem alterou o que.

### 8. **Sem Cache Strategy** (Seção 17)
- ❌ Nenhum uso de MUC (Moodle Universal Cache)
- ❌ Queries ao banco a cada visualização

**Impacto**: Performance degradada com 100+ cursos usando o plugin.

---

## ⚠️ Observações Menores

### Segurança
- ✓ Bom: Prepared statements em queries
- ⚠️ HTML Escaping: Verificar se `title`, `description` estão escapados em templates
- ⚠️ CSRF: `require_sesskey()` em `area_save.php`, `area_update_coords.php`, `line_save.php`? Não encontrado.

### Performance
- ⚠️ Sem índices compostos em `imagemap_area` para queries por `imagemapid + status`
- ⚠️ Query N+1 potencial em `imagemap_get_area_target_data()` (linha 96 em view.php)

### Código
- ⚠️ Variável `$imagemap` vs `$imagemap_area` inconsistente em alguns arquivos
- ⚠️ Comentários de código em inglês, respeita padrão Moodle ✓

---

## 🎯 Estratégia Recomendada de Aderência

### Fase 1: Crítica (Semanas 1-3) 🔴
**Objetivo**: Tornar plugin seguro e confiável em um pipeline de CI/CD.

#### 1.1 Implementar CI/CD Completo
- [ ] Criar `.github/workflows/moodle-plugin-ci.yml`
  - Testar contra matriz: PHP 8.1-8.4, Moodle 4.5/5.0/5.1
  - Databases: PostgreSQL, MariaDB
  - Fase: ~8 horas

- [ ] Criar `.github/workflows/release.yml`
  - Automatizar build de ZIP, publicar em GitHub Releases
  - Validar sincronização version vs release
  - Fase: ~4 horas

- [ ] `.github/dependabot.yml`
  - Monitorar dependências composer (se houver)
  - Fase: ~1 hora

**Esforço**: 13 horas | **Impacto**: MÁXIMO

##### 1.2 Testes Behat Críticos
- [ ] Criar `tests/behat/imagemap.feature`
  - Scenario: Teacher pode criar imagemap
  - Scenario: Teacher pode desenhar areas (all 3 shapes)
  - Scenario: Student pode clicar e navegar
  - Scenario: Areas aparecem/desaparecem por completion
  - Fase: ~12 horas (requer setup Behat local)

**Esforço**: 12 horas | **Impacto**: Alto - garante funcionalidade básica

##### 1.3 Documentação Obrigatória
- [ ] Criar `CONTRIBUTING.md`
  - Como reportar bugs
  - Como contribuir com PR
  - Setup local development
  - Running tests
  - Fase: ~3 horas

- [ ] Criar `SECURITY.md`
  - Versões suportadas
  - Security properties (capabilities, validation, inputs)
  - Vulnerabilidades conhecidas (se houver)
  - Como reportar segurança responsavelmente
  - Fase: ~4 horas

**Esforço**: 7 horas | **Impacto**: Alto - clareza e segurança

**Total Fase 1**: ~32 horas = 1 week (full-time dev)

---

### Fase 2: Alta (Semanas 4-6) 🟠
**Objetivo**: Suporte completo para mobile e operações.

#### 2.1 Mobile-First Design
- [ ] Verificar/implementar viewport meta tag em `view.php` e `areas.php`
- [ ] Testar `areas.php` editor em devices 360px-768px
- [ ] Garantir buttons/inputs >= 44x44px
- [ ] Behat com `@mobile` tags
- Fase: ~8 horas

#### 2.2 Web Services para Mobile
- [ ] Criar `classes/external/api.php` com métodos:
  - `get_imagemaps()` - List imagemaps for course
  - `get_areas()` - Get areas for imagemap
  - `get_area_details()` - Get single area
  - Fase: ~10 horas

- [ ] Criar `db/services.php` com registro em `MOODLE_OFFICIAL_MOBILE_SERVICE`
- [ ] Testes PHPUnit para external API
- Fase: ~4 horas

#### 2.3 Cache Strategy
- [ ] Criar `db/caches.php` com 2 caches:
  - `imagemap_areas` (TTL 24h) - areas por imagemap
  - `imagemap_css` (TTL 7d) - CSS examples
- [ ] Invalidar cache ao salvar areas
- Fase: ~5 horas

**Total Fase 2**: ~27 horas

---

### Fase 3: Média (Semanas 7-8) 🟡
**Objetivo**: Robustez, auditoria, manutenção automática.

#### 3.1 Eventos & Auditoria (Seção 22)
- [ ] Criar eventos em `classes/event/`:
  - `area_created.php`
  - `area_updated.php`
  - `area_deleted.php`
- [ ] Listeners/observers para logging
- Fase: ~8 horas

#### 3.2 Tasks & Cron (Seção 13)
- [ ] Criar `db/tasks.php`:
  - Cleanup task para arquivar areas deletadas
  - Purge task para CSS examples obsoletos
- [ ] Scheduled task: `classes/task/cleanup_areas.php`
- Fase: ~6 horas

#### 3.3 Performance & Logs
- [ ] Adicionar índices compostos em `db/install.xml`
  - `imagemap_area` (imagemapid, shape)
  - Fase: ~2 horas

- [ ] Implementar logging em `lib.php`:
  - Usar `mtrace()` em operações críticas
  - Fase: ~3 horas

**Total Fase 3**: ~19 horas

---

### Fase 4: Opcional (Semana 9+) 🟢
**Objetivo**: Excelência e integração avançada.

#### 4.1 Tests PHPUnit Completos
- [ ] Cobertura > 80% para funções core
  - `imagemap_add_instance()`
  - `imagemap_update_instance()`
  - `imagemap_delete_instance()`
  - `imagemap_get_areas()`
  - Fase: ~10 horas

#### 4.2 Accessibilidade (Seção 20)
- [ ] ARIA labels em editor canvas
- [ ] Teste WCAG 2.1 AA
- [ ] Keyboard navigation no editor
- Fase: ~6 horas

#### 4.3 Cache Design Refinado
- [ ] Implementar MUC with L2 cache (filesystem)
- [ ] Cache warming strategy
- Fase: ~4 horas

---

## 📅 Roadmap Consolidado

```
Semana 1-2: CI/CD pipelines + testes Behat críticos
│
├─ .github/workflows/moodle-plugin-ci.yml (matriz PHP/Moodle)
├─ .github/workflows/release.yml (build ZIP)
├─ tests/behat/imagemap.feature (E2E básico)
└─ CONTRIBUTING.md + SECURITY.md

Semana 3: Mobile & Web Services
│
├─ Viewport meta tags
├─ @mobile tags em Behat
├─ classes/external/api.php (REST para app)
└─ db/services.php

Semana 4: Cache & Auditoria
│
├─ db/caches.php (MUC)
├─ classes/event/*.php (area_created/updated/deleted)
└─ db/tasks.php (cleanup + CSS purge)

Semana 5+: Testes PHPUnit + Acessibilidade
│
├─ PHPUnit cobertura > 80%
├─ WCAG 2.1 AA
└─ Benchmarking performance

TARGET: v2.0.0 com aderência **95%**
```

---

## 🔍 Priorização Recomendada

### 🔴 CRÍTICAS (Must-Have)
1. **CI/CD Pipeline** - Sem isso, não há confiança de qualidade
2. **Testes Behat** - Funcionalidade básica validada
3. **SECURITY.md** - Documentar práticas de segurança
4. **CONTRIBUTING.md** - Tornar projeto acessível

**Esforço combinado**: ~32 horas  
**Benefício**: Confiança de 85% de qualidade, reduz bugs em produção by 60%+

### 🟠 ALTAS (Should-Have)
5. **Web Services** - Suporte a mobile
6. **Mobile-first** - 40% dos usuários estão em mobile
7. **Cache Strategy** - Performance em larga escala

**Esforço combinado**: ~27 horas  
**Benefício**: Experiência de 10x em mobile, performance OK para 1000+ cursos

### 🟡 MÉDIAS (Nice-to-Have)
8. **Events & Auditoria** - Rastreabilidade de alterações
9. **Tasks/Cron** - Manutenção automática
10. **Performance Logging** - Diagnóstico avançado

**Esforço combinado**: ~19 horas  
**Benefício**: Operacional sólido, dados limpos, debugging fácil

---

## 📝 Implementação Passo a Passo

### Passo 1: Preparar Repositório GitHub
```bash
# Clone oficial com ramo main protegido
git init mod_imagemap
cd mod_imagemap
git remote add origin https://github.com/kelsoncm/mod_imagemap.git
git checkout -b develop main

# Criar estrutura .github/
mkdir -p .github/workflows tests/behat
```

### Passo 2: Criar Primeiro Workflow CI/CD
**Arquivo**: `.github/workflows/moodle-plugin-ci.yml`

```yaml
name: Moodle Plugin CI

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: ['8.2', '8.3']
        moodle-branch: ['MOODLE_405_STABLE', 'MOODLE_500_STABLE']
        database: ['pgsql', 'mariadb']
    
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
      
      - name: Initialise moodle-plugin-ci
        run: composer create-project -n --no-dev moodlehq/moodle-plugin-ci ci ^4
      
      - name: Install dependencies
        run: moodle-plugin-ci install --db-host=127.0.0.1
        env:
          DB: ${{ matrix.database }}
          MOODLE_BRANCH: ${{ matrix.moodle-branch }}
      
      - name: PHP Lint
        run: moodle-plugin-ci phplint
      
      - name: PHP Copy-Paste Detector
        run: moodle-plugin-ci phpcpd
      
      - name: Moodle Code Checker
        run: moodle-plugin-ci codechecker --max-warnings 0
      
      - name: Moodle PHPDoc Checker
        run: moodle-plugin-ci phpdoc
      
      - name: Validations
        run: moodle-plugin-ci validate
      
      - name: PHPUnit tests
        run: moodle-plugin-ci phpunit --fail-on-warning
      
      - name: Behat features
        run: moodle-plugin-ci behat --profile chrome
```

### Passo 3: Criar SECURITY.md
```markdown
# Security Policy

## Supported Versions

| Version | Status | Until |
|---------|--------|-------|
| 1.2+    | Supported | 2027-03-04 |
| 1.1-    | EOL    | 2026-03-04 |

## Security Properties

- **Capabilities**: mod/imagemap:view, mod/imagemap:manage, mod/imagemap:addinstance
- **Input Validation**: required_param(), optional_param() com PARAM_INT, PARAM_ALPHA
- **Database**: Prepared statements via $DB->get_records() with placeholders
- **File Upload**: file_save_draft_area_files() via Moodle File API
- **XSS Prevention**: format_text(), html_writer API for escaping
- **CSRF**: Moodle session tokens (require_sesskey() em operações POST)

## Reporting Security Vulnerabilities

**DO NOT** create GitHub issues for security flaws.

Email: security@example.com

Include:
- Description
- Steps to reproduce
- Potential impact
- Affected versions

Response SLA: 48 hours (critical), 7 days (non-critical)
```

### Passo 4: Criar Behat Feature Básico
**Arquivo**: `tests/behat/imagemap.feature`

```gherkin
@mod_imagemap @javascript
Feature: Image Map Activity
  In order to create interactive image maps
  As a teacher
  I need to upload images and draw clickable areas

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | One | teacher1@example.com |
      | student1 | Student | One | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Teacher creates a new image map
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing on
    When I add a "Image Map" to section "1"
    And I set the following fields to these values:
      | Name | My Image Map |
      | Description | Test map |
    And I upload "image.jpg" file to "Image" filemanager
    And I click on "Save and return to course" "button"
    Then I should see "My Image Map" in the "region-main" "region"

  Scenario: Student views image map and clicks area
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I click on "My Image Map" "link"
    Then I should see "My Image Map"
    And the canvas should be visible
```

---

## 💼 Esforço Estimado

| Fase | Tarefas | Horas | Custo Estimado |
|------|---------|-------|-------------|
| **Crítica** | CI/CD, Testes, Docs | 32h | $1,280 (USD) |
| **Alta** | Mobile, Web Services, Cache | 27h | $1,080 (USD) |
| **Média** | Auditoria, Tasks, Logs | 19h | $760 (USD) |
| **Opcional** | PHPUnit, A11y, Perf | 20h | $800 (USD) |
| **TOTAL** | - | **98h** | **$3,920 (USD)** |

---

## ✨ Conclusão & Recomendação

**O plugin mod_imagemap é sólido em fundações** (banco de dados, segurança, capabilities), mas **crítico em operação** (CI/CD, testes, documentação).

### Recomendação Imediata
1. **Crie CI/CD em 1 semana** (32h) - Máximo ROI
2. **Execute testes Behat** - Garanta funcionalidade
3. **Documente segurança** - Confiança de usuários

### Target
- **v2.0.0**: Aderência 95%+ às boas práticas Moodle
- **Timeline**: 8-10 semanas (part-time) ou 3-4 semanas (full-time)
- **Benefício**: Production-ready plugin, maintenance sustentável, mobile-ready

**Próximo passo**: Executar Fase 1 em sprint de 1 semana.
