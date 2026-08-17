# Changelog

All notable changes to BoardRenewal will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-08-17

### Added
- Modern responsive theme for Kanboard 1.2.53+
- Dark mode support with automatic system preference detection
- High contrast mode for accessibility
- Glassmorphism effects on sidebar, topbar, and modals
- Custom SVG icons (Moderno Rounded style)
- Color-coded task cards with left border instead of full background
- Responsive sidebar with project initials when collapsed
- Horizontal scrolling board that extends under sidebar
- Custom styled checkboxes
- Improved form styling with consistent spacing
- Styled tables with hover effects
- Custom dropdown menus with proper alignment
- Styled comment section with markdown editor
- Subtask table styling with inline editing support
- Theme tokens system (CSS custom properties)
- 18 category colors mapped for task cards

### Changed
- Complete CSS rewrite from scratch (Approach B)
- Removed dependency on Kanboard's light/dark/auto.min.css
- Improved contrast ratios across all theme modes
- Unified spacing system throughout the interface
- Better visual hierarchy in all pages

### Fixed
- Dashboard layout alignment issues
- Board horizontal scrolling under sidebar
- Icon alignment in dropdown menus
- Text color contrast on colored task cards
- Modal overlay and positioning
- Form field styling consistency

### Technical
- SCSS architecture with modular files
- CSS custom properties for theming
- Sass compilation with dart-sass
- Deploy script for NAS Docker environment
- Git worktree support for isolated development

## [Unreleased]

### Planned
- Per-project theme customization
- Project-specific color schemes
- Custom logo upload per project
- Command palette (Ctrl+K)
- User preferences panel
- Additional icon sets
- Animation preferences
