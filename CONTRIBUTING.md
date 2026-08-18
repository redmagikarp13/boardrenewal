# Contributing to BoardRenewal

Thank you for your interest in contributing to BoardRenewal! This document provides guidelines and instructions for contributing.

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on what is best for the community

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the behavior
- **Expected behavior** vs actual behavior
- **Screenshots** if applicable
- **Environment details** (Kanboard version, browser, OS)

### Suggesting Features

Feature suggestions are welcome! Please provide:

- **Use case**: Why is this feature needed?
- **Proposed solution**: How should it work?
- **Alternatives considered**: Other approaches you've thought about

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Test thoroughly
5. Commit with clear messages
6. Push to your fork
7. Open a Pull Request

## Development Setup

### Prerequisites

- Node.js 16+
- npm 8+
- Kanboard 1.2.53+ (for testing)

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/boardrenewal.git
cd boardrenewal

# Install dependencies
npm install

# Build CSS
npm run build
```

### Development Workflow

```bash
# Watch for SCSS changes
npm run watch

# Copy to your Kanboard installation
cp -r plugins/BoardRenewal /path/to/kanboard/plugins/
```

## Coding Standards

### SCSS/CSS

- Use CSS custom properties (tokens) for colors, spacing, shadows
- Follow BEM-like naming conventions
- Keep files modular and focused
- Comment complex selectors

### JavaScript

- Use vanilla JavaScript (no frameworks)
- Keep it minimal and focused
- Comment non-obvious logic

### PHP

- Follow PSR-12 coding standards
- Use proper namespacing
- Document public methods

## Testing

- Test in both light and dark modes
- Test in high contrast mode
- Test on mobile devices
- Test with different Kanboard pages (board, task, settings, etc.)

## Documentation

- Update README.md if you change functionality
- Update CHANGELOG.md following Keep a Changelog format
- Add comments for complex code

## Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `style:` - Formatting, no code change
- `refactor:` - Code restructuring
- `test:` - Adding tests
- `chore:` - Maintenance tasks

Examples:
```
feat: add per-project color customization
fix: resolve icon alignment in dropdowns
docs: update installation instructions
```

## Questions?

Feel free to open an issue for any questions about contributing.

Thank you for contributing to BoardRenewal! 🎉
