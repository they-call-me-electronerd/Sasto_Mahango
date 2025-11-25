# Tests Directory

This directory is reserved for automated tests.

## Structure

- `unit/` - Unit tests for individual classes and functions
- `integration/` - Integration tests for database and API interactions
- `functional/` - Functional tests for user workflows

## Running Tests

Tests should be implemented using PHPUnit or similar testing frameworks.

```bash
# Install PHPUnit (if not already installed)
composer require --dev phpunit/phpunit

# Run tests
./vendor/bin/phpunit tests/
```

## TODO

- Implement unit tests for core classes (Database, Auth, Item, Category, User)
- Add integration tests for API endpoints
- Create functional tests for critical user workflows
- Set up continuous integration pipeline
