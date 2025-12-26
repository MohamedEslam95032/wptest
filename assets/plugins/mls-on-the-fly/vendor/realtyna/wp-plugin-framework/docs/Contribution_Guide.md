# Contribution Guide

We welcome contributions to the Realtyna Base Plugin framework! Whether you're fixing bugs, adding new features, or improving documentation, your help is greatly appreciated. This guide outlines how you can contribute to the project.

## Table of Contents
1. [Reporting Issues](#reporting-issues)
2. [Submitting Pull Requests](#submitting-pull-requests)
3. [Coding Standards](#coding-standards)
4. [Development Workflow](#development-workflow)
5. [Running Tests](#running-tests)
6. [License](#license)

## 1. Reporting Issues

If you encounter a bug, have a feature request, or notice something that needs improvement, please open an issue on our GitHub repository. When reporting an issue, please include:

- A clear and descriptive title.
- A detailed description of the problem or suggestion.
- Steps to reproduce the issue, if applicable.
- Relevant code snippets or error messages.
- Your environment details (WordPress version, PHP version, etc.).

[Open an Issue on GitHub](https://github.com/realtyna/wp-plugin-framework/issues)

## 2. Submitting Pull Requests

We encourage you to submit pull requests (PRs) to fix bugs or implement new features. Before submitting a PR, please make sure to:

- Fork the repository and create your branch from `main`.
- Ensure your code follows the project's coding standards.
- Include tests for your changes, if applicable.
- Update the documentation, if needed.
- Ensure that your code passes all tests and does not introduce new issues.

### Steps to Submit a Pull Request:

1. **Fork the Repository**: Click the "Fork" button on the repository's GitHub page.

2. **Clone Your Fork**: Clone your forked repository to your local machine.

   ```bash
   git clone https://github.com/your-username/Realtyna-Base-Plugin.git
   ```

3. **Create a Branch**: Create a new branch for your feature or bugfix.

   ```bash
   git checkout -b feature-or-bugfix-name
   ```

4. **Make Your Changes**: Implement your changes in the new branch.

5. **Run Tests**: Run the test suite to ensure your changes don't break anything.

   ```bash
   composer test
   ```

6. **Commit and Push**: Commit your changes and push the branch to your fork.

   ```bash
   git add .
   git commit -m "Description of the changes"
   git push origin feature-or-bugfix-name
   ```

7. **Open a Pull Request**: Go to the original repository and open a pull request from your branch.

## 3. Coding Standards

Please adhere to the following coding standards when contributing to the project:

- **PHP**: Follow the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.
- **Naming Conventions**: Use camelCase for variable and function names, PascalCase for class names, and UPPERCASE for constants.
- **Comments**: Use PHPDoc comments for classes, methods, and functions. Inline comments should be concise and clear.

### Example:

```php
/**
 * This class handles user authentication.
 */
class UserAuthenticator
{
    /**
     * Authenticate a user by username and password.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function authenticate(string $username, string $password): bool
    {
        // Authentication logic here
    }
}
```

## 4. Development Workflow

We follow the Gitflow workflow for managing our development process:

- **`main` branch**: Contains the stable code that is ready for release.
- **`develop` branch**: Contains the latest code with new features and bug fixes that are still under testing.
- **Feature branches**: Used for developing new features or fixing bugs.
- **Hotfix branches**: Used for critical fixes that need to be applied to the `main` branch immediately.

### Branch Naming Conventions

- **Feature branches**: `feature/feature-name`
- **Bugfix branches**: `bugfix/bug-name`
- **Hotfix branches**: `hotfix/issue-name`

## 5. Running Tests

Testing is crucial to ensure the stability and reliability of the framework. Before submitting a pull request, please run the test suite to verify that all tests pass.

### Running Tests Locally

1. **Install Dependencies**: Make sure you have all the dependencies installed.

   ```bash
   composer install
   ```

2. **Run Tests**: Use the following command to run the tests.

   ```bash
   composer test
   ```

If you add new functionality, please include corresponding tests to ensure that your changes work as expected.

## 6. License

By contributing to the Realtyna Base Plugin framework, you agree that your contributions will be licensed under the MIT License.

Thank you for your contributions! Your help makes this framework better for everyone.
