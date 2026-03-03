# Manual Testing Guide - Backup and Restore

**Versão**: 1.2.1  
**Data**: Março 3, 2026  
**Última Atualização**: Implementação completa de backup/restore

---

## Pré-requisitos

- Moodle 4.1+ instalado
- mod_imagemap instalado e funcional
- Acesso de administrador ou capabilities adequadas
- Permissão para fazer backup e restore

---

## Teste 1: Backup Simples

### Objetivo
Validar que uma atividade imagemap pode ser feita backup sem erros.

### Passos

1. **Criar Curso de Teste**
   - Navegue para Site Admin → Cursos → Gerenciar Cursos e Categorias
   - Clique em "Criar novo curso"
   - Preencha:
     - Nome: "Teste Backup - [Data]"
     - Nome abreviado: "test-backup"
   - Salve

2. **Criar Atividade Imagemap**
   - Entre no curso criado
   - Clique em "Adicionar uma atividade ou recurso"
   - Selecione "Image Map"
   - Preencha:
     - Nome: "Mapa Teste 1"
     - Descrição: "Esta é uma atividade de teste para backup"
   - Carregue uma imagem (qualquer imagem PNG/JPG)
   - Salve

3. **Realizar Backup**
   - Vá para Site Admin → Cursos → Backup
   - Selecione o curso "Teste Backup - [Data]"
   - Clique "Próximo"
   - Selecione "Backup de conteúdo e configurações" (padrão)
   - Clique "Próximo"
   - Clique "Realizar backup"
   - Aguarde conclusão

### Validação

✅ **Esperado:**
- Processo completa sem erros PHP
- Mensagem: "Backup completado com êxito"
- Arquivo .mbz gerado em Backup de cursos

❌ **Se falhar:**
- Verifique logs: Site Admin → Logs
- Procure por "Class not found: backup_imagemap"
- Procure por exceções durante backup

---

## Teste 2: Backup com Áreas

### Objetivo
Validar que áreas desenhadas são incluídas no backup.

### Passos

1. **Usar Imagemap do Teste 1**
   - Entre na atividade "Mapa Teste 1"
   - Observe o formulário de edição

2. **Desenhar Áreas** (se o editor permitir)
   - Clique em "Editar" > "Áreas"
   - Desenhe 2 áreas (círculo e retângulo)
   - Defina títulos: "Área 1" e "Área 2"
   - Configure links para URLs externas
   - Salve as áreas

3. **Realizar Backup**
   - Site Admin → Cursos → Backup
   - Selecione o curso
   - Completar dados de backup

### Validação

✅ **Esperado:**
- Backup completado
- Ao descompactar .mbz, existe arquivo `activities/imagemap_1/imagemap.xml`
- XML contém:
  ```xml
  <areas>
    <area>
      <title>Área 1</title>
      <shape>circle</shape>
      ...
    </area>
    <area>
      <title>Área 2</title>
      <shape>rect</shape>
      ...
    </area>
  </areas>
  ```

---

## Teste 3: Restore Simples

### Objetivo
Validar que uma atividade imagemap restaurada funciona corretamente.

### Passos

1. **Preparar Backup**
   - Use o backup do Teste 1
   - Ou crie novo conforme Teste 1

2. **Criar Curso Destino**
   - Novo curso: "Teste Restore - [Data]"

3. **Realizar Restore**
   - Site Admin → Cursos → Restore
   - Selecione arquivo .mbz do backup
   - Clique "Restaurar"
   - Selecione "Novo curso"
   - Clique "Próximo"
   - Selecione "Restaurar conteúdo e atividades"
   - Clique "Próximo"
   - Selecione o curso destino
   - Clique "Realizar restore"

### Validação

✅ **Esperado:**
- Processo concluído sem erros
- Mensagem: "Restore completado com êxito"
- Atividade "Mapa Teste 1" aparece no novo curso
- Imagem carregada e visível
- Acesso direto ao View funciona

❌ **Se falhar:**
- Procure em Site Admin → Logs por erros durante restore
- Verifique permissões de arquivo em `/moodledata/`
- Procure por "Class not found: restore_imagemap"

---

## Teste 4: Validação de Dados Restaurados

### Objetivo
Verificar que todos os dados incluindo áreas foram restaurados.

### Passos

1. **Após Restore bem-sucedido**
   - Entre no curso destino
   - Abra atividade imagemap restaurada

2. **Verificar Dados**
   - Imagem visível? ✅
   - Áreas ainda presentes? ✅
   - Títulos das áreas preservados? ✅
   - Links funcionam? ✅
   - Filtros CSS aplicados? ✅

3. **Verificar via Banco de Dados** (opcional, para admin)
   ```sql
   -- Verificar atividade restaurada
   SELECT * FROM mdl_imagemap 
   WHERE name = 'Mapa Teste 1' AND course = [ID do novo curso];
   
   -- Verificar áreas restauradas
   SELECT * FROM mdl_imagemap_area 
   WHERE imagemapid = [ID da atividade restaurada];
   ```

### Validação

✅ **Esperado:**
- Todos os dados visualmente presentes
- Database query retorna registros
- IDs remapeados (não iguais aos originais)

---

## Teste 5: Restore com Mapeamento de Módulos

### Objetivo
Validar que links para outros módulos são remapeados corretamente.

### Passos

1. **Criar Curso Fonte com Múltiplos Módulos**
   - Novo curso: "Teste Mapping - [Data]"
   - Crie 3 atividades diferentes:
     - Forum (ID ~5)
     - Quiz (ID ~6)
     - Page (ID ~7)

2. **Criar Imagemap com Links para Módulos**
   - Adicione atividade "Image Map"
   - Desenhe 3 áreas
   - Configure cada área com link para um módulo:
     - Área 1 → Forum
     - Área 2 → Quiz
     - Área 3 → Page

3. **Fazer Backup**
   - Backup do curso inteiro

4. **Restaurar em Novo Curso**
   - Novo curso destino
   - Restore completo (com atividades)

### Validação

✅ **Esperado:**
- Atividades restauradas com novos IDs
- Links em área ainda apontam para módulos corretos
- Clicar em área abre módulo correto
- Database: targetid foi remapeado

```sql
-- Verificar remapeamento
SELECT im.id, im.name, ia.title, ia.targetid
FROM mdl_imagemap im
JOIN mdl_imagemap_area ia ON ia.imagemapid = im.id
WHERE ia.targettype = 'module';
```

❌ **Se links apontarem para módulos errados:**
- Issue no mapeamento de course_module
- Verifique `restore_imagemap_stepslib.php`

---

## Teste 6: Restore de Linhas Conectoras

### Objetivo
Validar que linhas entre áreas são preservadas.

### Passos

1. **Criar Imagemap com Linhas**
   - Novo curso com imagemap
   - Desenhe 3 áreas
   - Conecte com linhas:
     - Área 1 ↔ Área 2
     - Área 2 ↔ Área 3
   - Salve

2. **Backup e Restore**
   - Faça backup
   - Restaure em novo curso

### Validação

✅ **Esperado:**
- Linhas visualmente presentes (se editor suportar)
- Database: registros em `mdl_imagemap_line` existem
- from_areaid e to_areaid remapeadas corretamente

```sql
-- Contar linhas restauradas
SELECT COUNT(*) FROM mdl_imagemap_line 
WHERE imagemapid = [ID da atividade restaurada];
```

---

## Teste 7: CSS Examples

### Objetivo
Validar que exemplos de CSS são preservados.

### Passos

1. **Verificar CSS Examples Existentes**
   - Site Admin → Plugins → Módulos de atividade → Image Map → CSS Examples
   - Anote quantos exemplos existem (ex: 5)

2. **Fazer Backup e Restore**
   - Crie novo curso com imagemap usando CSS examples
   - Faça backup
   - Restaure

3. **Verificar CSS Examples após Restore**
   - Retorne à página de CSS Examples
   - Verifique que não há duplicação
   - Mesmo número de exemplos

### Validação

✅ **Esperado:**
- Nenhum exemplo duplicado
- Todos os exemplos ainda disponíveis
- Nenhum único novo exemplo

```sql
-- Contar CSS examples
SELECT COUNT(*) FROM mdl_imagemap_css_examples;
-- Deve retornar o mesmo número antes e depois
```

---

## Teste 8: Caso de Erro - Links Órfãos

### Objetivo
Validar tratamento gracioso de módulos que não foram restaurados.

### Passos

1. **Criar Imagemap com Link Externo**
   - Novo curso com imagemap
   - Área com link para módulo que NOT será restaurado
   - Backup (apenas atividade imagemap, sem dependências)

2. **Restaurar Seletivamente**
   - Restore
   - Selecione APENAS imagemap (desselecione outros módulos)

### Validação

✅ **Esperado:**
- Restore completa sem erro
- Área presente
- targetid nulo ou inválido (gracefully handled)

❌ **Se houver erro:**
- Deve haver log explicativo
- Não deve interromper todo restore

---

## Teste 9: Backup do Curso Inteiro

### Objetivo
Validar que imagemap não interfere com backup de outros conteúdos.

### Passos

1. **Criar Curso com Múltiplos Conteúdos**
   - Quiz
   - Forum
   - Page
   - **Image Map** (nosso módulo)
   - Assignment

2. **Fazer Backup Completo**
   - Site Admin → Cursos → Backup
   - Selecione o curso
   - Backup com ALL conteúdos

3. **Verificar Arquivo .mbz**
   - Descompacte (em local seguro)
   - Procure por `activities/imagemap_*/imagemap.xml`
   - Procure por `activities/forum_*/forum.xml`
   - Procure por `activities/quiz_*/quiz.xml`

### Validação

✅ **Esperado:**
- Arquivo muito maior (múltiplas atividades)
- XML bem-formado para todo conteúdo
- Nenhum conflito entre atividades

---

## Checklist de Produção

Antes de considerar backup/restore como pronto para produção:

- [ ] Teste 1 (Backup simples): PASSOU
- [ ] Teste 2 (Backup com áreas): PASSOU
- [ ] Teste 3 (Restore simples): PASSOU
- [ ] Teste 4 (Validação dados): PASSOU
- [ ] Teste 5 (Mapeamento módulos): PASSOU
- [ ] Teste 6 (Linhas conectoras): PASSOU
- [ ] Teste 7 (CSS Examples): PASSOU
- [ ] Teste 8 (Erro handling): PASSOU
- [ ] Teste 9 (Curso completo): PASSOU

---

## Troubleshooting

### "Class not found: backup_imagemap_activity_task"

**Causa**: Arquivos de backup não encontrados

**Solução**:
1. Verificar: `/backup/moodle2/backup_imagemap_activity_task.class.php` existe
2. Verificar: `/backup/moodle2/backup_imagemap_stepslib.php` existe
3. Limpar cache: Site Admin → Development → Purge all caches
4. Retry backup

### "XML parsing error during restore"

**Causa**: Arquivo corrompido ou estrutura inválida

**Solução**:
1. Usar novo backup
2. Verificar integridade do arquivo .mbz
3. Checar logs detalhados em Site Admin → Logs

### "Database error during restore"

**Causa**: Conflito de dados ou constraints

**Solução**:
1. Verifique que não há imagemap com mesmo nome no curso destino
2. Verifique permissões de database
3. Restaurar em "Novo curso" ao invés de "Curso existente"

### "Imagens não restauram"

**Causa**: Permissões de arquivo ou espaço em disco

**Solução**:
1. Verificar `/moodledata/` tem permissão 755+
2. Verificar espaço em disco disponível
3. Verificar logs: Site Admin → Logs

---

## Relatório de Teste

Ao reportar resultado de testes, inclua:

```
Teste de Backup/Restore - mod_imagemap v1.2.1
Data: [DATA]
Moodle: 4.1 / 4.2 / 4.3 / etc
PHP: 7.x / 8.x / etc

Teste 1 (Backup simples): [PASSOU/FALHOU]
Teste 2 (Com áreas): [PASSOU/FALHOU]
Teste 3 (Restore): [PASSOU/FALHOU]
...

Erros encontrados:
- [Listar qualquer erro]

Sugestões:
- [Listar melhorias sugeridas]
```

---

## Próximos Passos

Após validação completa:
1. Documentar resultados
2. Atualizar CHANGELOG
3. Tagear versão 1.2.1
4. Notificar usuários
