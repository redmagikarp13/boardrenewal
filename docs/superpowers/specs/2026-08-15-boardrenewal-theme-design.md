# BoardRenewal — Tema moderno para Kanboard

**Data:** 2026-08-15
**Status:** Aprovado em brainstorming
**Alvo:** Kanboard 1.2.53 (Docker em `nasleo.local:8080`, volumes em `/opt/kanboard/`)

## 1. Visão geral

O BoardRenewal é um tema/plugin para Kanboard que substitui completamente a interface
padrão por uma experiência moderna inspirada em Vikunja, Notion, Linear e Trello.
Entrega UI responsiva (mobile e desktop), modos claro/escuro/alto contraste, e
personalização visual por projeto (capa, fundo do board, emoji, cores).

O Kanboard está em **modo manutenção** (DOM estável, sem mudanças estruturais previstas),
o que valida a **abordagem B: CSS reescrito do zero** — nenhum stylesheet do core
(`light/dark/auto.min.css`) é carregado. O tema segue convenções upstream
(CSS custom properties, estrutura de plugin/tema) para facilitar possível
incorporação oficial futura.

## 2. Contexto técnico (verificado na instância real)

- Kanboard 1.2.53 roda em container Docker (`kanboard/kanboard:latest`), porta 8080.
- Volumes do host: `/opt/kanboard/data` e `/opt/kanboard/plugins` → deploy de plugin
  é apenas copiar a pasta, sem rebuild do container.
- Plugin `TableTask` já instalado (deve continuar funcionando com o novo CSS).
- Sistema nativo de temas: `assets/css/{tema}.min.css` com `light`/`dark`/`auto`,
  todos baseados em CSS custom properties no `:root`; `auto` usa
  `@media (prefers-color-scheme)`. Seleção via `UserSession::getTheme()`.
- `Template/layout.php` carrega: `colorCss()` (cores de tarefa), `vendor.min.css`,
  tema do usuário, `print.min.css`, `customCss()` (stylesheet global das config),
  depois hooks `template:layout:css` e `template:layout:js`.
- Temas suportam override de templates via pasta `Template/` do plugin.
- Board usa DOM de tabela (`app/Template/board/table_*.php`) — mantido por causa do
  drag-and-drop nativo; apenas reestilizado.
- Hooks úteis: `template:project:dropdown`, `template:layout:css/js`, hooks de
  settings de projeto/usuário.

## 3. Estrutura do plugin

```
plugins/BoardRenewal/
├── Theme.php                  # registro: hooks, rotas, helpers
├── Controller/
│   ├── AppearanceController.php   # settings de aparência do projeto (form + upload)
│   ├── ThemeAssetController.php   # serve imagens de capa/fundo com check de permissão
│   └── CommandPaletteController.php # JSON: projetos, busca de tarefas, ações
├── Model/
│   └── BoardRenewalModel.php      # get/set de metadados de projeto e usuário
├── Template/                  # overrides do core
│   ├── layout.php             # app shell: sidebar + topbar + data-theme no <html>
│   ├── project_header/views.php   # abas de visão
│   ├── project_overview/      # capa + emoji
│   └── appearance/            # telas de settings próprias do tema
├── Locale/pt_BR/translations.php, Locale/fr_FR/... (mínimo: pt_BR + en)
└── Assets/
    ├── css/boardrenewal.css   # artefato compilado (commitado)
    ├── js/                    # sidebar drawer, command palette, theme toggle
    └── src/sass/              # fonte Sass por componente (build local)
```

**Decisões de arquitetura:**

- Sem migration: configuração vai em **metadados nativos**
  (`project_has_metadata`, `user_has_metadata`).
- Uploads salvos em `data/boardrenewal/{project_id}/` (volume persistente).
- `vendor.min.css` continua carregado (widgets de terceiros); `colorCss()` mantido
  para as cores nativas de tarefa.
- JS vanilla, sem dependências novas.

## 4. Sistema visual

**Tokens `--br-*`** (BoardRenewal) em CSS custom properties: superfícies
(bg, surface, elevated), texto (primary/secondary/muted), bordas, sombras,
raios (6–12px), espaçamentos, tipografia (system font stack), cor de destaque.

**Paleta base: Índigo Linear** (destaque `#6366f1`, superfícies quase-brancas,
escuro `#191a23`).

**Modos** via atributos no `<html>`, setados pelo `layout.php`:

- `data-theme="light" | "dark" | "auto"` — segue a preferência nativa do usuário;
  no `auto`, `@media (prefers-color-scheme: dark)` alterna em tempo real com o dispositivo.
- `data-contrast="default" | "high"` — toggle de alto contraste do usuário (metadado),
  ortogonal ao tema: as 4 combinações resultam em claro, escuro, claro-HC, escuro-HC
  (auto-HC acompanha o dispositivo dentro do conjunto HC).

Alto contraste: bordas fortes, sem sombras difusas, contraste mínimo WCAG AAA (7:1)
para texto, estados de foco bem visíveis.

**Paleta customizável:** cor de destaque escolhida pelo usuário (preferências) ou
por projeto (aba Aparência) é injetada como override inline de `--br-accent*`
no layout. O mesmo mecanismo serve para uma paleta totalmente própria no futuro.

## 5. App shell, board e responsividade

**Desktop (≥1024px):**

- Sidebar fixa à esquerda (escura): logo da instância, atalho Ctrl+K, Dashboard,
  lista de projetos com emoji, rodapé com usuário + seletor de modo.
- Topbar fina: breadcrumb do contexto + ações principais (substitui o menu
  horizontal antigo).
- Em projeto: capa (imagem + emoji sobreposto), abas de visão
  (Board / Lista / Calendário / Resumo).
- Board: colunas como painéis arredondados translúcidos (quando há fundo de
  imagem), cards brancos com sombra leve, etiqueta em pill, faixa lateral na cor
  da tarefa, ícones discretos (comentário/anexo/subtarefa).

**Tablet (768–1023px):** sidebar colapsa para barra de ícones.

**Mobile (<768px):** sidebar vira drawer (hambúrguer na topbar), board com scroll
horizontal + scroll-snap por coluna (~85% da largura), tabelas viram cards
empilhados, formulários em coluna única, touch targets ≥44px.

Ferramentas reestilizadas com os mesmos tokens: modais, tooltips, dropdowns,
formulários, tabelas, alertas, badges, avatares.

**Telas de autenticação e públicas:** login, "esqueci a senha" e reset de senha
passam pelo mesmo `layout.php` e recebem o tema — o formulário de login vira um
card centralizado moderno com logo da instância, funcionando nos 4 modos
(claro/escuro/auto/HC). Páginas públicas somente leitura (board público, via flag
`not_editable`) também são cobertas pelo override de layout.

## 6. Personalização por projeto

Aba **"Aparência"** nas settings do projeto (gerentes de projeto + admins):

- Emoji/ícone do projeto (texto curto) — sidebar, breadcrumb, capa
- Imagem de capa (upload; jpg/png/webp; ≤2MB; recomendada ~1600×300)
- Fundo do board: cor sólida, gradiente pré-definido ou imagem (mesmos limites)
- Cor de destaque do projeto (override do índigo dentro do projeto)
- Cor padrão dos cards novos do projeto (integrada às cores nativas de tarefa)

Chaves de metadado (prefixo `boardrenewal.`): `emoji`, `cover_image`,
`board_background_type`, `board_background_value`, `accent_color`,
`default_task_color`. Rota `ThemeAssetController` serve as imagens validando
acesso do usuário ao projeto.

## 7. Paleta de comandos (Ctrl+K)

- Atalho `Ctrl+K`/`Cmd+K` + botão na sidebar; modal central com busca instantânea
- Seções: projetos, tarefas (título/número, via `TaskFinderModel` exposto por
  `CommandPaletteController`), ações rápidas (nova tarefa, alternar modo)
- Navegação por teclado (↑ ↓ Enter Esc); JS vanilla

## 8. Preferências do usuário

Na tela de preferências do usuário, o tema adiciona:

- Toggle **alto contraste**
- **Cor de destaque pessoal** (override global)

O seletor claro/escuro/auto continua sendo o nativo do Kanboard (já compatível).

## 9. Build, deploy e testes

- **Fonte:** Sass organizado por componente em `Assets/src/sass/`, compilado via
  npm script (sass CLI) para `Assets/css/boardrenewal.css` (commitado).
- **Deploy:** `rsync` da pasta → `root@nasleo.local:/opt/kanboard/plugins/BoardRenewal/`,
  depois ativar em Configurações → Plugins.
- **Testes:** checklist manual de telas (board, dashboard, lista, calendário, Gantt,
  visão de tarefa, settings de projeto/usuário/admin, login, tooltips/modais) ×
  modos (claro, escuro, auto, HC) × viewports (desktop 1440, tablet 800, mobile 375).
  Validação de contraste com ferramenta automatizada (axe) nas telas principais.
- **Compatibilidade:** verificar visualmente o plugin TableTask existente.

## 10. Fases de entrega

1. **Fundação:** estrutura do plugin, tokens, 4 modos, app shell (topbar + sidebar),
   board/cards, responsividade, overrides de layout.
2. **Personalização:** aba Aparência, uploads, metadados, capa/emoji/fundo/cores.
3. **Extras:** abas de visão, paleta Ctrl+K, preferências de usuário do tema,
   polimento geral.

## Fora de escopo (YAGNI)

- Fork/modificação do core do Kanboard
- Reescrita de funcionalidades (drag-and-drop, busca avançada, notificações)
- PWA/offline, i18n além de pt_BR/en
- Temas comunitários/marketplace de paletas
