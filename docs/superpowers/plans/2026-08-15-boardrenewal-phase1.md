# BoardRenewal — Fase 1 (Fundação) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Entregar a fundação visual do tema BoardRenewal no Kanboard 1.2.53 do NAS: app shell (sidebar + topbar), board/cards modernos, tokens de cor, modos claro/escuro/auto/alto-contraste e responsividade.

**Architecture:** Plugin Kanboard (`plugins/BoardRenewal`) que sobrescreve o template `layout` via `setTemplateOverride`, injeta nosso próprio CSS (Sass compilado) e JS vanilla, e seta `data-theme`/`data-contrast` no `<html>`. O CSS do core (`light/dark/auto.min.css`) deixa de ser carregado; `vendor.min.css`, `colorCss()` e `print.min.css` são mantidos.

**Tech Stack:** PHP (templates Kanboard), Sass (compilado via npm `sass`), JS vanilla, deploy por rsync para `root@nasleo.local:/opt/kanboard/plugins/`.

**Spec:** `docs/superpowers/specs/2026-08-15-boardrenewal-theme-design.md`

---

## Contexto essencial para quem executa

- Instância: Kanboard 1.2.53 em Docker no NAS. Web: `http://nasleo.local:8080`. SSH: `root@nasleo.local`.
- Volumes do host: `/opt/kanboard/data` e `/opt/kanboard/plugins` — copiar a pasta do plugin já ativa (não precisa restart do container).
- Plugins carregam via classe `Kanboard\Plugin\{NomePasta}\Plugin` (arquivo `Plugin.php`), extendendo `Kanboard\Core\Plugin\Base`.
- Template override: `$this->template->setTemplateOverride('layout', 'boardRenewal:layout')` → resolve para `plugins/BoardRenewal/Template/layout.php`.
- Helpers disponíveis em templates: `$this->app`, `$this->asset`, `$this->url`, `$this->user`, `$this->text`, `$this->hook`, `$this->model`, `$this->layout`, etc.
- `UserSession::getTheme()` retorna `light|dark|auto` (preferência nativa do usuário).
- O layout do core está em `app/Template/layout.php` (já copiado abaixo como base do nosso override).
- Commit com identidade já configurada no repo (magikarp13). Commits frequentes, um por tarefa.
- Build Sass: `npm run build` na raiz do repo gera `plugins/BoardRenewal/Assets/css/boardrenewal.css` (commitado).

**Deploy (usado em todas as tarefas de verificação):**

```bash
./deploy.sh
```

(o script é criado na Task 1)

---

### Task 1: Esqueleto do plugin + build/deploy

**Files:**
- Create: `plugins/BoardRenewal/Plugin.php`
- Create: `package.json`
- Create: `deploy.sh`
- Create: `plugins/BoardRenewal/Assets/src/sass/main.scss` (placeholder mínimo)
- Create: `plugins/BoardRenewal/Assets/css/boardrenewal.css` (artefato)

- [ ] **Step 1: Criar `plugins/BoardRenewal/Plugin.php`**

```php
<?php

namespace Kanboard\Plugin\BoardRenewal;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->setTemplateOverride('layout', 'boardRenewal:layout');
    }

    public function getPluginName()
    {
        return 'BoardRenewal';
    }

    public function getPluginDescription()
    {
        return 'Modern responsive theme with dark mode, high contrast and per-project customization';
    }

    public function getPluginAuthor()
    {
        return 'magikarp13';
    }

    public function getPluginVersion()
    {
        return '0.1.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/magikarp13/boardrenewal';
    }
}
```

- [ ] **Step 2: Criar `package.json`**

```json
{
  "name": "boardrenewal",
  "private": true,
  "scripts": {
    "build": "sass --no-source-map plugins/BoardRenewal/Assets/src/sass/main.scss plugins/BoardRenewal/Assets/css/boardrenewal.css",
    "watch": "sass --watch --no-source-map plugins/BoardRenewal/Assets/src/sass/main.scss plugins/BoardRenewal/Assets/css/boardrenewal.css"
  },
  "devDependencies": {
    "sass": "^1.77.0"
  }
}
```

- [ ] **Step 3: Criar `deploy.sh`** (na raiz do repo, `chmod +x`)

```bash
#!/bin/sh
# Compila o Sass e sincroniza o plugin com o Kanboard do NAS
set -e
cd "$(dirname "$0")"
npm run build
rsync -az --delete --exclude 'src/' plugins/BoardRenewal/ root@nasleo.local:/opt/kanboard/plugins/BoardRenewal/
echo "BoardRenewal deploy OK — http://nasleo.local:8080"
```

- [ ] **Step 5: Sass inicial `plugins/BoardRenewal/Assets/src/sass/main.scss`**

```scss
// BoardRenewal — entrypoint (tokens e componentes entram nas próximas tasks)
body {
  outline: 3px solid magenta; // marcador temporário para confirmar que o CSS está carregando
}
```

- [ ] **Step 6: Instalar dependências e buildar**

```bash
npm install && npm run build
```

Expected: `plugins/BoardRenewal/Assets/css/boardrenewal.css` gerado.

> **Atenção:** ainda NÃO sobrescrevemos o `layout` nessa task para validar o plugin isolado. O `setTemplateOverride` para `layout` só será efetivo quando criarmos `Template/layout.php` (Task 2). Enquanto isso, o Kanboard tenta resolver `boardrenewal:layout`, falha silenciosamente? Não — falharia. Por isso o override do layout é adicionado **na Task 2**; nesta task, comente a linha `setTemplateOverride` temporariamente se preferir validar por partes. Recomendação: deixe a linha, mas só faça deploy na Task 2.

- [ ] **Step 7: Commit**

```bash
git add -A && git commit -m "feat: esqueleto do plugin BoardRenewal com build Sass e deploy"
```

---

### Task 2: Helper do tema + override do layout (app shell base + data-theme)

**Files:**
- Create: `plugins/BoardRenewal/Helper/BoardRenewalHelper.php`
- Modify: `plugins/BoardRenewal/Plugin.php` (registrar helper)
- Create: `plugins/BoardRenewal/Template/layout.php`
- Create: `plugins/BoardRenewal/Template/sidebar.php` (versão mínima)
- Create: `plugins/BoardRenewal/Assets/js/boardrenewal.js` (mínimo)
- Modify: `plugins/BoardRenewal/Assets/src/sass/main.scss` (remover marcador magenta)

> **Importante:** templates do Kanboard só acessam helpers (`$this->algo`), nunca serviços
> do container como `userSession`. Por isso todo estado de usuário vem do helper do tema,
> criado ANTES do layout ser ativado.

- [ ] **Step 1: Criar `plugins/BoardRenewal/Helper/BoardRenewalHelper.php`**

```php
<?php

namespace Kanboard\Plugin\BoardRenewal\Helper;

use Kanboard\Core\Base;

class BoardRenewalHelper extends Base
{
    public function isLogged()
    {
        return $this->userSession->isLogged();
    }

    /**
     * Tema do usuário atual para o atributo data-theme ('light' para visitantes)
     */
    public function getTheme()
    {
        return $this->userSession->isLogged() ? $this->userSession->getTheme() : 'light';
    }

    /**
     * Projetos ativos do usuário atual (para a sidebar)
     */
    public function getUserProjects()
    {
        if (! $this->userSession->isLogged()) {
            return array();
        }

        $ids = $this->projectPermission->getActiveProjectIds($this->userSession->getId());

        return $this->projectModel->getAllByIds($ids);
    }

    /**
     * Nome exibível do usuário atual (UserSession não expõe getFullname)
     */
    public function getCurrentUserFullname()
    {
        $user = $this->userSession->getAll();

        return ! empty($user['name']) ? $user['name'] : $user['username'];
    }
}
```

- [ ] **Step 2: Registrar o helper em `Plugin.php`** (obrigatório antes do override de layout)

```php
    public function getHelpers()
    {
        return array(
            'Plugin\BoardRenewal\Helper' => array('BoardRenewalHelper'),
        );
    }
```

- [ ] **Step 3: Criar `plugins/BoardRenewal/Template/layout.php`**

Baseado no `app/Template/layout.php` do core 1.2.53, com estas mudanças:
(a) `data-theme` e `data-contrast` no `<html>`;
(b) NÃO carrega `assets/css/{tema}.min.css` do core — carrega `boardrenewal.css` no lugar;
(c) renderiza a sidebar (`boardrenewal:sidebar`) antes do `<section class="page">`;
(d) injeta overrides inline de paleta (placeholder vazio por ora, preenchido na Fase 2).

```php
<?php $brTheme = $this->BoardRenewalHelper->getTheme(); ?>
<!DOCTYPE html>
<html lang="<?= $this->app->jsLang() ?>"
      data-theme="<?= $this->text->e($brTheme) ?>"
      data-contrast="default"
      <?php if ($this->app->isRtlLanguage()): ?> dir="rtl"<?php endif; ?>>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="robots" content="noindex,nofollow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="referrer" content="no-referrer">

        <?php if (isset($board_public_refresh_interval)): ?>
            <meta http-equiv="refresh" content="<?= $board_public_refresh_interval ?>">
        <?php endif ?>

        <?= $this->asset->colorCss() ?>
        <?= $this->asset->css('assets/css/vendor.min.css') ?>
        <?= $this->asset->css('plugins/BoardRenewal/Assets/css/boardrenewal.css') ?>
        <?= $this->asset->css('assets/css/print.min.css', true, 'print') ?>
        <?= $this->asset->customCss() ?>

        <?php if (! isset($not_editable)): ?>
            <?= $this->asset->js('assets/js/vendor.min.js') ?>
            <?= $this->asset->js('assets/js/app.min.js') ?>
            <?= $this->asset->js('plugins/BoardRenewal/Assets/js/boardrenewal.js') ?>
        <?php endif ?>

        <?= $this->hook->asset('css', 'template:layout:css') ?>
        <?= $this->hook->asset('js', 'template:layout:js') ?>

        <link rel="icon" href="<?= $this->url->dir() ?>assets/img/adaptive-favicon.svg" type="image/svg+xml">
        <link rel="icon" type="image/png" href="<?= $this->url->dir() ?>assets/img/favicon.png">
        <link rel="apple-touch-icon" href="<?= $this->url->dir() ?>assets/img/touch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= $this->url->dir() ?>assets/img/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= $this->url->dir() ?>assets/img/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= $this->url->dir() ?>assets/img/touch-icon-ipad-retina.png">

        <title>
            <?php if (isset($page_title)): ?>
                <?= $this->text->e($page_title) ?>
            <?php elseif (isset($title)): ?>
                <?= $this->text->e($title) ?>
            <?php else: ?>
                Kanboard
            <?php endif ?>
        </title>

        <?= $this->hook->render('template:layout:head') ?>
    </head>
    <body data-status-url="<?= $this->url->href('UserAjaxController', 'status') ?>"
          data-login-url="<?= $this->url->href('AuthController', 'login') ?>"
          data-keyboard-shortcut-url="<?= $this->url->href('DocumentationController', 'shortcuts') ?>"
          data-timezone="<?= $this->app->getTimezone() ?>"
          data-js-date-format="<?= $this->app->getJsDateFormat() ?>"
          data-js-time-format="<?= $this->app->getJsTimeFormat() ?>"
    >

    <?php if (isset($no_layout) && $no_layout): ?>
        <?= $this->app->flashMessage() ?>
        <?= $content_for_layout ?>
    <?php else: ?>
        <?= $this->hook->render('template:layout:top') ?>
        <div class="br-app">
            <?php if ($this->BoardRenewalHelper->isLogged()): ?>
                <?= $this->render('boardRenewal:sidebar', array(
                    'project' => isset($project) ? $project : array(),
                )) ?>
            <?php endif ?>
            <div class="br-main">
                <?= $this->render('header', array(
                    'title' => $title,
                    'description' => isset($description) ? $description : '',
                    'board_selector' => isset($board_selector) ? $board_selector : array(),
                    'project' => isset($project) ? $project : array(),
                )) ?>
                <section class="page">
                    <?= $this->app->flashMessage() ?>
                    <?= $content_for_layout ?>
                </section>
            </div>
        </div>
        <?= $this->hook->render('template:layout:bottom') ?>
    <?php endif ?>
    </body>
</html>
```

- [ ] **Step 4: Criar `plugins/BoardRenewal/Template/sidebar.php`** (versão mínima; conteúdo completo na Task 4)

```php
<aside class="br-sidebar" id="br-sidebar">
    <div class="br-sidebar__brand">
        <a href="<?= $this->url->dir() ?>">Kanboard</a>
    </div>
    <nav class="br-sidebar__nav">
        <?= $this->hook->render('template:dashboard:sidebar') ?>
    </nav>
</aside>
```

- [ ] **Step 5: Criar `plugins/BoardRenewal/Assets/js/boardrenewal.js`** (mínimo por ora)

```js
// BoardRenewal — JS do tema (drawer e modos entram nas próximas tasks)
(function () {
    'use strict';
})();
```

- [ ] **Step 6: Remover marcador magenta do `main.scss`** e deixar:

```scss
// BoardRenewal — entrypoint
@use 'tokens';
```

E criar `plugins/BoardRenewal/Assets/src/sass/_tokens.scss` com o conteúdo da Task 3 Step 1 (para não quebrar o build, crie o arquivo agora vazio com `// tokens` e preencha na Task 3).

- [ ] **Step 7: Deploy e verificação**

```bash
./deploy.sh
```

Abra `http://nasleo.local:8080` (via browser MCP ou navegador): a página de login e o dashboard devem carregar SEM o CSS do core (visual "quebrado"/sem estilo do Kanboard é esperado aqui — confirma que nosso layout está ativo e que `boardrenewal.css` carregou sem erro no console). Verifique no DevTools: `<html data-theme="light">` presente e nenhum erro de rede 404 nos assets do plugin.

- [ ] **Step 8: Commit**

```bash
git add -A && git commit -m "feat: override de layout com data-theme e shell inicial"
```

---

### Task 3: Design tokens + base/reset

**Files:**
- Modify: `plugins/BoardRenewal/Assets/src/sass/_tokens.scss`
- Create: `plugins/BoardRenewal/Assets/src/sass/_base.scss`
- Modify: `plugins/BoardRenewal/Assets/src/sass/main.scss`

- [ ] **Step 1: `_tokens.scss`** — paleta Índigo Linear + tokens semânticos + variantes dark/HC

```scss
// Tokens BoardRenewal (--br-*)

:root,
[data-theme='light'] {
  --br-accent: #6366f1;
  --br-accent-hover: #4f46e5;
  --br-accent-soft: #eef2ff;
  --br-accent-contrast: #ffffff;

  --br-bg: #fafafa;
  --br-surface: #ffffff;
  --br-surface-2: #f1f2f8;
  --br-sidebar-bg: #191a23;
  --br-sidebar-fg: #c7cadf;
  --br-sidebar-fg-muted: #6b7194;
  --br-sidebar-active: #6366f1;

  --br-text: #0f172a;
  --br-text-secondary: #475569;
  --br-text-muted: #94a3b8;

  --br-border: #e5e7ef;
  --br-border-strong: #cbd5e1;

  --br-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
  --br-shadow-md: 0 4px 12px rgba(15, 23, 42, 0.10);

  --br-danger: #dc2626;
  --br-success: #16a34a;
  --br-warning: #d97706;

  --br-radius-sm: 6px;
  --br-radius: 10px;
  --br-radius-lg: 14px;

  --br-font: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;

  // Compat com componentes do core que ainda referenciam tokens antigos (vendor, colorCss)
  --color-primary: var(--br-text);
  --link-color-primary: var(--br-accent);
  --button-primary-background-color: var(--br-accent);
  --button-primary-border-color: var(--br-accent);
}

[data-theme='dark'] {
  --br-accent-soft: #2a2c3f;
  --br-bg: #14151d;
  --br-surface: #1e2030;
  --br-surface-2: #262839;
  --br-sidebar-bg: #101118;
  --br-text: #e6e8f2;
  --br-text-secondary: #a7adc6;
  --br-text-muted: #6b7194;
  --br-border: #32344a;
  --br-border-strong: #4a4d6a;
  --br-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.4);
  --br-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.5);
}

// Auto: acompanha o dispositivo quando o usuário escolheu "auto"
[data-theme='auto'] {
  @media (prefers-color-scheme: dark) {
    --br-accent-soft: #2a2c3f;
    --br-bg: #14151d;
    --br-surface: #1e2030;
    --br-surface-2: #262839;
    --br-sidebar-bg: #101118;
    --br-text: #e6e8f2;
    --br-text-secondary: #a7adc6;
    --br-text-muted: #6b7194;
    --br-border: #32344a;
    --br-border-strong: #4a4d6a;
    --br-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.4);
    --br-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.5);
  }
}

// Alto contraste (ortogonal ao tema)
[data-contrast='high'] {
  --br-shadow-sm: none;
  --br-shadow-md: none;
  --br-border: #64748b;
  --br-border-strong: #0f172a;
  --br-text-muted: var(--br-text-secondary);

  &[data-theme='dark'],
  &[data-theme='auto'] {
    --br-border: #94a3b8;
  }
}

[data-contrast='high'] * {
  text-shadow: none !important;
}

[data-contrast='high'] :focus-visible {
  outline: 3px solid var(--br-accent);
  outline-offset: 2px;
}
```

- [ ] **Step 2: `_base.scss`** — reset leve e tipografia

```scss
* { box-sizing: border-box; }

body {
  margin: 0;
  background: var(--br-bg);
  color: var(--br-text);
  font-family: var(--br-font);
  font-size: 14px;
  line-height: 1.5;
}

a {
  color: var(--br-accent);
  text-decoration: none;
  &:hover { color: var(--br-accent-hover); text-decoration: underline; }
}

h1, h2, h3 { color: var(--br-text); font-weight: 650; line-height: 1.25; }
h1 { font-size: 22px; }
h2 { font-size: 18px; }
h3 { font-size: 15px; }

hr { border: 0; border-top: 1px solid var(--br-border); }

code, pre {
  background: var(--br-surface-2);
  border-radius: var(--br-radius-sm);
  font-size: 13px;
}

.br-app {
  display: flex;
  min-height: 100vh;
}

.br-main {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

section.page {
  padding: 20px 28px;
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
}
```

- [ ] **Step 3: `main.scss`**

```scss
// BoardRenewal — entrypoint
@use 'tokens';
@use 'base';
```

- [ ] **Step 4: Build + deploy + verificação**

```bash
./deploy.sh
```

Verificar no navegador: fundo claro `#fafafa`, texto `#0f172a`, links índigo. Trocar a preferência de tema do usuário no Kanboard (Perfil → Editar → Tema = Dark) e recarregar: fundo deve ficar escuro (mecanismo `data-theme` funcionando). Com tema "auto", alternar o modo do SO/navegador deve trocar as cores.

- [ ] **Step 5: Commit**

```bash
git add -A && git commit -m "feat: design tokens e base com modos claro/escuro/auto/HC"
```

---

### Task 4: Sidebar completa + topbar

**Files:**
- Modify: `plugins/BoardRenewal/Template/sidebar.php`
- Create: `plugins/BoardRenewal/Assets/src/sass/_shell.scss`
- Modify: `plugins/BoardRenewal/Assets/src/sass/main.scss`

- [ ] **Step 1: `sidebar.php` completo**

O helper do tema já foi criado na Task 2 (`BoardRenewalHelper` com `getUserProjects()`
e `getCurrentUserFullname()` — APIs verificadas no core 1.2.53:
`ProjectPermissionModel::getActiveProjectIds($user_id)` e `ProjectModel::getAllByIds($ids)`).

```php
<?php
$brProjects = $this->BoardRenewalHelper->getUserProjects();
$brCurrentProjectId = isset($project['id']) ? $project['id'] : 0;
?>
<aside class="br-sidebar" id="br-sidebar">
    <div class="br-sidebar__brand">
        <a href="<?= $this->url->dir() ?>" title="Dashboard">
            <span class="br-sidebar__logo">◆</span>
            <span class="br-sidebar__name">Kanboard</span>
        </a>
    </div>

    <button type="button" class="br-sidebar__search" id="br-search-button" title="Buscar (Ctrl+K)">
        🔍 <span><?= t('Search') ?></span> <kbd>Ctrl K</kbd>
    </button>

    <nav class="br-sidebar__nav">
        <a class="br-nav-item" href="<?= $this->url->href('DashboardController', 'show', array('user_id' => $this->user->getId())) ?>">
            <span class="br-nav-item__icon">⊞</span> <?= t('Dashboard') ?>
        </a>

        <div class="br-sidebar__section-title"><?= t('Projects') ?></div>
        <?php foreach ($brProjects as $brProject): ?>
            <a class="br-nav-item <?= $brProject['id'] == $brCurrentProjectId ? 'br-nav-item--active' : '' ?>"
               href="<?= $this->url->href('BoardViewController', 'show', array('project_id' => $brProject['id'])) ?>">
                <span class="br-nav-item__icon">▸</span> <?= $this->text->e($brProject['name']) ?>
            </a>
        <?php endforeach ?>
    </nav>

    <div class="br-sidebar__footer">
        <button type="button" class="br-sidebar__mode" id="br-contrast-toggle" title="<?= t('High contrast') ?>">◐</button>
        <a class="br-nav-item" href="<?= $this->url->href('UserViewController', 'show', array('user_id' => $this->user->getId())) ?>">
            <span class="br-nav-item__icon">👤</span> <?= $this->text->e($this->BoardRenewalHelper->getCurrentUserFullname()) ?>
        </a>
    </div>
</aside>
```

- [ ] **Step 2: `_shell.scss`** — sidebar, topbar e header do core reestilizado

```scss
.br-sidebar {
  width: 230px;
  flex-shrink: 0;
  background: var(--br-sidebar-bg);
  color: var(--br-sidebar-fg);
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 14px 10px;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;

  &__brand a {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    padding: 4px 8px 12px;
    &:hover { text-decoration: none; }
  }

  &__search {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.08);
    color: var(--br-sidebar-fg);
    border: 1px solid transparent;
    border-radius: var(--br-radius);
    padding: 8px 10px;
    font-size: 13px;
    cursor: pointer;
    margin-bottom: 10px;
    &:hover { background: rgba(255, 255, 255, 0.14); }
    kbd { margin-left: auto; opacity: 0.6; font-size: 11px; }
  }

  &__section-title {
    color: var(--br-sidebar-fg-muted);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 8px 4px;
  }

  &__footer {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
  }

  &__mode {
    background: none;
    border: 0;
    color: var(--br-sidebar-fg);
    font-size: 16px;
    cursor: pointer;
    padding: 6px 8px;
    border-radius: var(--br-radius-sm);
    &:hover { background: rgba(255, 255, 255, 0.1); }
  }
}

.br-nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--br-sidebar-fg);
  border-radius: var(--br-radius-sm);
  padding: 7px 8px;
  font-size: 13px;
  &:hover { background: rgba(255, 255, 255, 0.08); color: #fff; text-decoration: none; }
  &--active {
    background: var(--br-sidebar-active);
    color: #fff;
    &:hover { background: var(--br-sidebar-active); }
  }
  &__icon { width: 18px; text-align: center; opacity: 0.85; }
}

// Topbar: reaproveita o <header> do core, reestilizado
.br-main > header {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--br-surface);
  border-bottom: 1px solid var(--br-border);
  padding: 10px 20px;
  min-height: 52px;

  .title-container { flex: 1; min-width: 0; }
  h1 { font-size: 16px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .board-selector-container { max-width: 260px; }
  .menus-container { display: flex; align-items: center; gap: 6px; }
}

// Botão hambúrguer (mobile) — visível só <1024px
.br-menu-button {
  display: none;
  background: none;
  border: 0;
  font-size: 20px;
  color: var(--br-text);
  cursor: pointer;
  padding: 8px;
}
```

- [ ] **Step 3: Adicionar em `main.scss`**

```scss
@use 'shell';
```

- [ ] **Step 4: Deploy + verificação**

```bash
./deploy.sh
```

Verificar: sidebar escura fixa à esquerda com lista de projetos; item do projeto atual destacado; topbar clara com título da página; usuário no rodapé da sidebar. Testar navegação entre dashboard e 2+ projetos.

- [ ] **Step 5: Commit**

```bash
git add -A && git commit -m "feat: sidebar de projetos e topbar moderna"
```

---

### Task 5: Board e cards

**Files:**
- Create: `plugins/BoardRenewal/Assets/src/sass/_board.scss`
- Modify: `plugins/BoardRenewal/Assets/src/sass/main.scss`

- [ ] **Step 1: `_board.scss`**

```scss
// Board do Kanboard usa tabelas (.board-container table) — mantemos o DOM
// por causa do drag-and-drop e reestilizamos.

.board-container {
  background: var(--br-bg);
  border-radius: var(--br-radius-lg);
  overflow-x: auto;

  table {
    border-collapse: separate;
    border-spacing: 10px 0;
    width: 100%;
  }
}

.board-column {
  background: var(--br-surface-2);
  border-radius: var(--br-radius);
  vertical-align: top;
  border: 1px solid var(--br-border) !important;
  min-width: 230px;
}

.board-column-header {
  padding: 8px;
  .board-column-title, strong, a { color: var(--br-text); font-size: 13px; }
}

.board-swimlane-header {
  color: var(--br-text-secondary);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 10px 4px 2px;
}

// Card de tarefa
.board-item, .task-board {
  background: var(--br-surface);
  border: 1px solid var(--br-border) !important;
  border-radius: var(--br-radius);
  box-shadow: var(--br-shadow-sm);
  padding: 10px;
  margin: 6px 0;
  font-size: 13px;
  color: var(--br-text);
  transition: box-shadow 0.12s ease, transform 0.12s ease;

  &:hover { box-shadow: var(--br-shadow-md); }

  a { color: var(--br-text); }

  // Cores nativas de tarefa do Kanboard aplicam background no card;
  // garante legibilidade mantendo a faixa lateral em vez do fundo chapado:
  &[style*="background"] {
    border-left-width: 3px !important;
  }
}

.task-board-title { font-weight: 600; line-height: 1.35; }

.task-board-icons, .task-footer {
  color: var(--br-text-muted);
  font-size: 11px;
  margin-top: 6px;
  a { color: var(--br-text-muted); }
}

// Categorias/etiquetas em pill
.task-board-category, .badge {
  display: inline-block;
  background: var(--br-accent-soft);
  color: var(--br-accent);
  border: 0 !important;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 500;
  padding: 1px 9px;
  margin: 0 4px 4px 0;
}

// Estado "arrastando"
.task-board-sortable, .draggable-item { cursor: grab; }
```

- [ ] **Step 2: `main.scss`** — adicionar `@use 'board';`

- [ ] **Step 3: Deploy + verificação**

```bash
./deploy.sh
```

Abrir o board de um projeto com tarefas em `http://nasleo.local:8080`: colunas arredondadas sobre fundo, cards brancos com sombra, etiquetas em pill. **Arrastar um card entre colunas** e confirmar que o drag-and-drop nativo continua funcionando. Verificar também com tarefas que têm cor (faixa lateral). Testar nos modos claro e escuro.

- [ ] **Step 4: Commit**

```bash
git add -A && git commit -m "feat: board e cards modernos preservando drag-and-drop"
```

---

### Task 6: Botões, forms, tabelas, modais, alertas

**Files:**
- Create: `plugins/BoardRenewal/Assets/src/sass/_components.scss`
- Modify: `plugins/BoardRenewal/Assets/src/sass/main.scss`

- [ ] **Step 1: `_components.scss`**

```scss
// Botões (o core usa .btn, .btn-blue, .btn-red, .btn-green)
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--br-surface);
  color: var(--br-text);
  border: 1px solid var(--br-border-strong);
  border-radius: var(--br-radius-sm);
  font-size: 13px;
  font-weight: 500;
  padding: 7px 14px;
  cursor: pointer;
  text-decoration: none !important;
  transition: background 0.1s ease, border-color 0.1s ease;

  &:hover { background: var(--br-surface-2); color: var(--br-text); }
}

.btn-blue, .btn-primary {
  background: var(--br-accent);
  border-color: var(--br-accent);
  color: var(--br-accent-contrast);
  &:hover { background: var(--br-accent-hover); color: var(--br-accent-contrast); }
}

.btn-red { background: var(--br-danger); border-color: var(--br-danger); color: #fff; }
.btn-green { background: var(--br-success); border-color: var(--br-success); color: #fff; }

// Formulários
input[type="text"], input[type="password"], input[type="email"],
input[type="number"], input[type="search"], select, textarea {
  background: var(--br-surface);
  color: var(--br-text);
  border: 1px solid var(--br-border-strong);
  border-radius: var(--br-radius-sm);
  padding: 8px 10px;
  font-size: 14px;
  font-family: var(--br-font);
  max-width: 100%;
  &:focus {
    outline: none;
    border-color: var(--br-accent);
    box-shadow: 0 0 0 3px var(--br-accent-soft);
  }
}

label { color: var(--br-text-secondary); font-weight: 500; font-size: 13px; }

.form-help { color: var(--br-text-muted); font-size: 12px; }
.form-required { color: var(--br-danger); }

// Tabelas
table.table-fixed, table {
  border-collapse: collapse;
  width: 100%;
  th, td {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid var(--br-border);
    color: var(--br-text);
  }
  th {
    background: var(--br-surface-2);
    color: var(--br-text-secondary);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  tr:hover td { background: var(--br-surface-2); }
}

// Alertas
.alert {
  border-radius: var(--br-radius);
  padding: 10px 14px;
  font-size: 13px;
  border: 1px solid var(--br-border);
  background: var(--br-surface-2);
  color: var(--br-text);
}
.alert-success { border-color: var(--br-success); background: color-mix(in srgb, var(--br-success) 10%, var(--br-surface)); }
.alert-error { border-color: var(--br-danger); background: color-mix(in srgb, var(--br-danger) 10%, var(--br-surface)); }

// Modais / popovers (vendor dropdowns + modais do app.min.js)
#modal-box, .ui-widget, .select2-dropdown, .tooltip-container .tooltip {
  background: var(--br-surface);
  color: var(--br-text);
  border: 1px solid var(--br-border);
  border-radius: var(--br-radius);
  box-shadow: var(--br-shadow-md);
}

#modal-box .modal-header {
  border-bottom: 1px solid var(--br-border);
  padding: 14px 18px;
  font-weight: 650;
}
#modal-box .modal-content { padding: 18px; }

// Dropdowns de menu do header
.js-dropdown-container {
  background: var(--br-surface);
  border: 1px solid var(--br-border);
  border-radius: var(--br-radius);
  box-shadow: var(--br-shadow-md);
  padding: 6px;
  a {
    display: block;
    padding: 7px 10px;
    border-radius: var(--br-radius-sm);
    color: var(--br-text);
    &:hover { background: var(--br-surface-2); text-decoration: none; }
  }
}

// Tela de login
.form-login {
  max-width: 380px;
  margin: 8vh auto;
  background: var(--br-surface);
  border: 1px solid var(--br-border);
  border-radius: var(--br-radius-lg);
  box-shadow: var(--br-shadow-md);
  padding: 32px;
}
```

- [ ] **Step 2: `main.scss`** — adicionar `@use 'components';`

- [ ] **Step 3: Deploy + verificação**

```bash
./deploy.sh
```

Verificar: tela de login como card centralizado; dropdowns do header abrem estilizados; criar/editar uma tarefa abre o modal com visual novo; tabelas de lista de tarefas legíveis; botões primários índigo. Nos 4 modos (trocar preferência do usuário + toggle de SO para o auto).

- [ ] **Step 4: Commit**

```bash
git add -A && git commit -m "feat: botões, forms, tabelas, modais e tela de login"
```

---

### Task 7: Toggle de alto contraste + JS do tema

**Files:**
- Modify: `plugins/BoardRenewal/Assets/js/boardrenewal.js`

- [ ] **Step 1: `boardrenewal.js` completo da Fase 1**

```js
(function () {
    'use strict';

    var root = document.documentElement;

    // Alto contraste: persistido em localStorage até a Fase 3 (metadado de usuário)
    var savedContrast = null;
    try { savedContrast = localStorage.getItem('boardrenewal.contrast'); } catch (e) {}
    if (savedContrast === 'high') {
        root.setAttribute('data-contrast', 'high');
    }

    function toggleContrast() {
        var current = root.getAttribute('data-contrast') === 'high' ? 'default' : 'high';
        root.setAttribute('data-contrast', current);
        try { localStorage.setItem('boardrenewal.contrast', current); } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        var contrastButton = document.getElementById('br-contrast-toggle');
        if (contrastButton) {
            contrastButton.addEventListener('click', toggleContrast);
            contrastButton.setAttribute('aria-pressed',
                root.getAttribute('data-contrast') === 'high' ? 'true' : 'false');
        }

        // Drawer mobile
        var menuButton = document.getElementById('br-menu-button');
        var sidebar = document.getElementById('br-sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', function () {
                sidebar.classList.toggle('br-sidebar--open');
                document.body.classList.toggle('br-drawer-open');
            });
            document.addEventListener('click', function (e) {
                if (document.body.classList.contains('br-drawer-open') &&
                    !sidebar.contains(e.target) && !menuButton.contains(e.target)) {
                    sidebar.classList.remove('br-sidebar--open');
                    document.body.classList.remove('br-drawer-open');
                }
            });
        }
    });
})();
```

- [ ] **Step 2: Injetar botão hambúrguer no header** — adicionar em `plugins/BoardRenewal/Template/layout.php`, logo após `<div class="br-main">` e antes do `render('header')`:

```php
<button type="button" class="br-menu-button" id="br-menu-button" aria-label="Menu">☰</button>
```

- [ ] **Step 3: Deploy + verificação**

```bash
./deploy.sh
```

Clicar no botão ◐ do rodapé da sidebar: alto contraste ativa (bordas fortes, sem sombras), persiste após recarregar. Clicar de novo: volta. Alternar tema do usuário entre claro/escuro com HC ativo: combinações corretas.

- [ ] **Step 4: Commit**

```bash
git add -A && git commit -m "feat: toggle de alto contraste com persistência local"
```

---

### Task 8: Responsividade

**Files:**
- Create: `plugins/BoardRenewal/Assets/src/sass/_responsive.scss`
- Modify: `plugins/BoardRenewal/Assets/src/sass/main.scss`

- [ ] **Step 1: `_responsive.scss`**

```scss
// Tablet: sidebar vira barra de ícones
@media (max-width: 1023px) {
  .br-sidebar {
    width: 56px;
    padding: 10px 6px;
    .br-sidebar__name, .br-sidebar__section-title,
    .br-nav-item span:not(.br-nav-item__icon),
    .br-sidebar__search span, .br-sidebar__search kbd { display: none; }
    .br-sidebar__search { justify-content: center; }
    .br-nav-item { justify-content: center; padding: 9px 6px; }
  }
}

// Mobile: sidebar vira drawer, board rola com snap
@media (max-width: 767px) {
  .br-menu-button { display: block; }

  .br-app { display: block; }

  .br-sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: 270px;
    transform: translateX(-100%);
    transition: transform 0.2s ease;
    z-index: 1000;
    .br-sidebar__name, .br-sidebar__section-title,
    .br-nav-item span:not(.br-nav-item__icon),
    .br-sidebar__search span, .br-sidebar__search kbd { display: inline; }
    .br-nav-item { justify-content: flex-start; }

    &--open { transform: translateX(0); box-shadow: var(--br-shadow-md); }
  }

  body.br-drawer-open::after {
    content: '';
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 999;
  }

  .br-main > header {
    padding: 8px 12px;
    h1 { font-size: 15px; }
    .board-selector-container { display: none; }
  }

  section.page { padding: 12px; }

  // Board mobile: scroll horizontal com snap por coluna
  .board-container {
    table { border-spacing: 8px 0; }
    .board-column {
      min-width: 80vw;
      scroll-snap-align: start;
    }
  }

  // Tabelas de listagem viram cards
  table:not(.board-container table) {
    thead { display: none; }
    tr {
      display: block;
      background: var(--br-surface);
      border: 1px solid var(--br-border);
      border-radius: var(--br-radius);
      margin-bottom: 10px;
      padding: 6px 0;
    }
    td {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      border: 0;
      padding: 6px 12px;
      &::before {
        content: attr(data-label);
        color: var(--br-text-muted);
        font-size: 12px;
      }
    }
  }

  // Formulários em coluna única, targets grandes
  .form-actions, .form-help { width: 100%; }
  input[type="text"], input[type="password"], input[type="email"],
  input[type="number"], input[type="search"], select, textarea,
  .btn { min-height: 44px; }
}
```

> Nota: o pseudo-elemento `td::before` com `attr(data-label)` só funciona se as `<td>` tiverem `data-label`; Kanboard não gera isso nativamente. Se na verificação as células ficarem sem rótulo, remover o `&::before` e manter apenas o layout empilhado (decidir durante execução conforme o resultado visual).

- [ ] **Step 2: `main.scss`** — adicionar `@use 'responsive';` no final

- [ ] **Step 3: Deploy + verificação em 3 viewports (browser MCP)**

```bash
./deploy.sh
```

- **1440px**: layout desktop completo intacto.
- **800px**: sidebar vira barra de ícones de 56px.
- **375px**: hambúrguer abre o drawer; board rola na horizontal com snap por coluna; listas viram cards; botões/inputs com altura ≥44px.
- Repetir o teste do drawer nos modos claro e escuro.

- [ ] **Step 4: Commit**

```bash
git add -A && git commit -m "feat: responsividade com drawer mobile e board com scroll-snap"
```

---

### Task 9: QA final da Fase 1 + correções

**Files:**
- Modificar quaisquer arquivos das tasks anteriores conforme achados

- [ ] **Step 1: Rodar checklist completo (browser MCP)**

Checklist (registrar resultado de cada item):

Telas (desktop 1440 + mobile 375, modos claro/escuro/auto/HC):
1. Login / logout
2. Dashboard
3. Board com drag-and-drop
4. Lista de tarefas + filtros
5. Calendário
6. Visão de tarefa (abrir uma tarefa)
7. Criação de tarefa (modal)
8. Settings de projeto
9. Settings de usuário (troca de tema claro/escuro/auto refletindo)
10. Admin → Configurações + lista de plugins (BoardRenewal aparece sem erro)
11. Console do navegador sem erros de JS/rede do plugin
12. Plugin TableTask instalado continua funcional

- [ ] **Step 2: Corrigir achados** (uma iteração por categoria: layout, cores, interação)

```bash
./deploy.sh  # após cada correção
```

- [ ] **Step 3: Commit final da fase**

```bash
git add -A && git commit -m "feat: QA da fase 1 — BoardRenewal com app shell, board, modos e responsivo"
git tag v0.1.0
```

- [ ] **Step 4: Mostrar ao usuário** — screenshots do board desktop e mobile nos modos claro e escuro, e convidar para navegar em `http://nasleo.local:8080`.

---

## Fora desta fase (próximos planos)

- Fase 2: personalização por projeto (capa, emoji, fundo do board, cores) — requer controllers, uploads e metadados
- Fase 3: abas de visão, paleta Ctrl+K funcional, preferência de alto contraste em metadado de usuário, cor de destaque pessoal
