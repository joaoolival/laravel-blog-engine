# Contributing

Thank you for considering contributing to Laravel Blog Engine! We welcome contributions from the community.

## Bug Reports

If you discover a bug, please [open an issue](../../issues) with:

-   A clear title and description
-   Steps to reproduce the issue
-   Expected vs actual behavior
-   Your environment (PHP version, Laravel version, etc.)

## Security Vulnerabilities

If you discover a security vulnerability, please email joaoljolival@gmail.com instead of using the issue tracker.

## Pull Requests

1. **Fork & Clone** - Fork the repository and clone it locally
2. **Branch** - Create a feature branch from `main`:
    ```bash
    git checkout -b feature/my-new-feature
    ```
3. **Install Dependencies** - Run `composer install`
4. **Code** - Make your changes following the coding standards below
5. **Test** - Run the test suite:
    ```bash
    composer test
    ```
6. **Lint** - Ensure code style compliance:
    ```bash
    composer format
    ```
7. **Commit** - Write clear, descriptive commit messages
8. **Push & PR** - Push your branch and open a pull request

## Coding Standards

This project follows [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards. We use [Laravel Pint](https://laravel.com/docs/pint) for automatic formatting:

```bash
composer format
```

### Guidelines

-   **Type hints** - Use PHP 8.4 type declarations for parameters and return types
-   **PHPDoc** - Add docblocks for complex methods and class properties
-   **Tests** - Include tests for new features and bug fixes
-   **Naming** - Use descriptive names for variables, methods, and classes

## Static Analysis

We use PHPStan for static analysis:

```bash
composer analyse
```

Please ensure your code passes at the configured level before submitting.

## Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Format code: `composer format`
5. Run analysis: `composer analyse`

## Questions?

Feel free to [open a discussion](../../discussions) if you have questions about contributing.

Thank you for helping make Laravel Blog Engine better! 🎉
