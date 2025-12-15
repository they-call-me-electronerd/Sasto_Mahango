# Contributing to MulyaSuchi

First off, thank you for considering contributing to MulyaSuchi! It's people like you that make MulyaSuchi such a great tool for Nepal's market intelligence.

## Table of Contents
1. [Code of Conduct](#code-of-conduct)
2. [How Can I Contribute?](#how-can-i-contribute)
3. [Development Setup](#development-setup)
4. [Coding Standards](#coding-standards)
5. [Commit Guidelines](#commit-guidelines)
6. [Pull Request Process](#pull-request-process)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

### Our Standards

- Using welcoming and inclusive language
- Being respectful of differing viewpoints and experiences
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** to demonstrate the steps
- **Describe the behavior you observed** and what you expected
- **Include screenshots** if possible
- **Note your environment**: OS, PHP version, MySQL version, browser

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Provide specific examples** to demonstrate the steps
- **Describe the current behavior** and the expected behavior
- **Explain why this enhancement would be useful**

### Your First Code Contribution

Unsure where to begin? You can start by looking through these issues:

- **Beginner issues** - issues that should only require a few lines of code
- **Help wanted issues** - issues that may require more involved changes

### Pull Requests

- Fill in the required template
- Follow the coding standards
- Include appropriate test cases
- Update documentation as needed
- End all files with a newline

## Development Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- Git

### Setup Steps

1. **Fork the repository**
   ```bash
   git clone https://github.com/yourusername/MulyaSuchi.git
   cd MulyaSuchi
   ```

2. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-bug-fix
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your local database credentials
   ```

4. **Import database**
   ```bash
   mysql -u root -p < sql/schema.sql
   mysql -u root -p < sql/seed_data.sql
   ```

5. **Set permissions**
   ```bash
   chmod 775 assets/uploads/
   chmod 775 logs/
   ```

6. **Test your setup**
   - Navigate to `http://localhost/MulyaSuchi/public/`
   - Verify the homepage loads correctly

## Coding Standards

### PHP Standards

- Follow PSR-12 coding style
- Use meaningful variable and function names
- Add PHPDoc comments for all classes and methods
- Use type hints where possible
- Keep functions small and focused

Example:
```php
<?php
/**
 * Get item by ID
 * 
 * @param int $id Item ID
 * @return array|null Item data or null if not found
 */
public function getItemById(int $id): ?array {
    // Implementation
}
```

### JavaScript Standards

- Use ES6+ features
- Use `const` and `let`, avoid `var`
- Use meaningful variable names
- Add JSDoc comments for functions
- Keep functions pure when possible

Example:
```javascript
/**
 * Format price for display
 * @param {number} price - Price in paisa
 * @returns {string} Formatted price string
 */
function formatPrice(price) {
    return `रू ${(price / 100).toFixed(2)}`;
}
```

### CSS Standards

- Use BEM naming convention
- Keep specificity low
- Use CSS variables for colors and spacing
- Mobile-first responsive design
- Comment complex selectors

Example:
```css
/* Primary button component */
.btn--primary {
    background: var(--color-primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
}
```

### Database Standards

- Use prepared statements (PDO)
- Always use transactions for multi-step operations
- Add indexes for frequently queried columns
- Use meaningful table and column names
- Document complex queries

## Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Type
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation only changes
- **style**: Code style changes (formatting, missing semi-colons, etc)
- **refactor**: Code refactoring
- **perf**: Performance improvements
- **test**: Adding or updating tests
- **chore**: Changes to build process or auxiliary tools

### Examples

```
feat(products): add price history chart

Implement Chart.js integration to display price trends
over the last 30 days on product detail pages.

Closes #123
```

```
fix(auth): resolve session timeout issue

Users were being logged out prematurely due to incorrect
session lifetime configuration.

Fixes #456
```

## Pull Request Process

1. **Update your branch**
   ```bash
   git checkout main
   git pull origin main
   git checkout your-branch
   git rebase main
   ```

2. **Test thoroughly**
   - Test all affected features
   - Check browser compatibility
   - Verify mobile responsiveness
   - Run any available tests

3. **Update documentation**
   - Update README.md if needed
   - Add/update inline code comments
   - Update API documentation if applicable

4. **Submit pull request**
   - Use a clear and descriptive title
   - Reference related issues
   - Describe your changes in detail
   - Include screenshots for UI changes
   - List any breaking changes

5. **Code review**
   - Address reviewer feedback
   - Update your branch as needed
   - Be open to suggestions

6. **After merge**
   - Delete your branch
   - Update your local repository

## Testing

### Manual Testing Checklist

- [ ] Feature works as intended
- [ ] No console errors
- [ ] Mobile responsive
- [ ] Works in Chrome, Firefox, Safari
- [ ] Forms validate correctly
- [ ] Database queries are optimized
- [ ] No security vulnerabilities introduced

### Security Checklist

- [ ] Input is validated and sanitized
- [ ] SQL injection prevention (using PDO)
- [ ] XSS prevention
- [ ] CSRF tokens used for forms
- [ ] Proper authentication checks
- [ ] Proper authorization checks
- [ ] Sensitive data is not exposed

## Questions?

Feel free to open an issue with the label "question" if you need clarification on anything.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to MulyaSuchi! 🙏
