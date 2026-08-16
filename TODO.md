# BoardRenewal — Pendências de Implementação

> Gerado em 2026-08-16 após auditoria visual das principais telas.
> Prioridade: Alta → Média → Baixa.

---

## 🔴 Alta Prioridade (impacto visual imediato)

### 1. Listas com bullets pretos (todas as telas)
- **Telas afetadas:** Dashboard, Configurações, Plugins, Usuários, Perfil, Tarefa, Projeto
- **Problema:** Todas as listas de navegação e ações estão com bullets pretos do browser default
- **Solução:** Remover `list-style` e transformar em menus/grid estilizados

### 2. Layout não usa largura total
- **Problema:** Todas as telas (exceto board) estão com conteúdo estreito, muito espaço em branco à direita
- **Solução:** Ajustar `max-width` ou remover limitação de largura

### 3. Dashboard/Visão Global
- **Problema:** Projetos aparecem como texto corrido, muito denso
- **Problema:** Tarefas sem estrutura visual (cards ou lista estilizada)
- **Problema:** Campo de busca solto, sem integração visual
- **Solução:** Cards de projeto com estatísticas, tarefas em lista estilizada

---

## 🟡 Média Prioridade

### 4. Visão de Lista de Tarefas
- **Problema:** Tarefas como texto simples, sem cards
- **Problema:** Barra de cor da tarefa muito grossa e sem estilo
- **Problema:** Checkboxes nativos
- **Solução:** Cards de tarefa com hover, checkboxes customizados

### 5. Visão de Tarefa Individual
- **Problema:** Card de tarefa com fundo amarelo muito forte (cor nativa)
- **Problema:** Listas de navegação e ações com bullets
- **Solução:** Layout em grid, remover bullets, ajustar cores

### 6. Configurações (Projeto e Sistema)
- **Problema:** Menus laterais como listas com bullets
- **Problema:** Deveriam ser sidebars fixas ou abas
- **Solução:** Menu lateral estilizado ou abas horizontais

### 7. Formulários
- **Problema:** Fieldsets com bordas brancas muito fortes
- **Problema:** Inputs sem estilo consistente
- **Solução:** Estilizar fieldsets, inputs com bordas suaves

---

## 🟢 Baixa Prioridade

### 8. Tabelas
- **Problema:** Funcionais mas poderiam ter mais estilo
- **Solução:** Hover, zebra striping

### 9. Checkboxes customizados
- **Problema:** Checkboxes nativos do browser
- **Solução:** Customizar com CSS ou biblioteca

---

##  Efeitos Visuais (Fase 2)

### 10. Transparência e Sombras
- **Pendente:** Efeitos de transparência (glassmorphism) na sidebar, topbar e modais
- **Pendente:** Sombras mais elaboradas nos cards (talvez com glow sutil nos cards ativos)
- **Pendente:** Possivelmente backdrop-filter blur nos overlays

---

##  Status

- [ ] 1. Remover bullets de todas as listas
- [ ] 2. Ajustar largura do conteúdo para usar espaço disponível
- [ ] 3. Estilizar menus de navegação (sidebar ou abas)
- [ ] 4. Cards de projeto no Dashboard
- [ ] 5. Lista de tarefas estilizada
- [ ] 6. Formulários com fieldsets suaves
- [ ] 7. Tabelas com mais estilo
- [ ] 8. Checkboxes customizados
- [ ] 9. Efeitos de transparência (glassmorphism)
- [ ] 10. Sombras elaboradas nos cards
