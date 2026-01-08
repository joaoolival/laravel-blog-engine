# Laravel Blog Engine — Maintenance Guide

This guide explains everything you need to know to maintain this package professionally.

---

## Table of Contents

1. [Your GitHub Workflows (What They Do)](#your-github-workflows)
2. [Contributing: The Complete Flow](#contributing-the-complete-flow)
3. [Commit Message Conventions](#commit-message-conventions)
4. [Branch Naming Conventions](#branch-naming-conventions)
5. [Pull Request Guidelines](#pull-request-guidelines)
6. [Release Process](#release-process)
7. [Quick Reference Card](#quick-reference-card)

---

## Your GitHub Workflows

All configuration files are correctly set up. Here is what each one does:

| File                            | Purpose                                                      | When it runs                 |
| ------------------------------- | ------------------------------------------------------------ | ---------------------------- |
| `run-tests.yml`                 | Runs your Pest test suite on PHP 8.3 & 8.4, Ubuntu & Windows | When any `.php` file changes |
| `phpstan.yml`                   | Runs static analysis (PHPStan) to catch type errors          | When any `.php` file changes |
| `fix-php-code-style-issues.yml` | Auto-formats your code with Laravel Pint                     | When any `.php` file changes |
| `dependabot-auto-merge.yml`     | Auto-merges safe Dependabot PRs (patch/minor updates)        | When Dependabot opens a PR   |
| `dependabot.yml`                | Configures Dependabot to check for updates weekly            | Weekly (automatic)           |
| `FUNDING.yml`                   | Enables the "Sponsor" button on GitHub                       | Always visible               |
| `ISSUE_TEMPLATE/`               | Provides structured forms for bug reports                    | When someone opens an issue  |

---

## Contributing: The Complete Flow

Every time you want to make a change, follow this flow:

### Step 1: Start from a clean `main`

```bash
git checkout main
git pull origin main
```

### Step 2: Create a branch

```bash
git checkout -b feat/add-search-endpoint
```

### Step 3: Make your changes and commit

```bash
git add .
git commit -m "feat: add search endpoint for posts"
```

### Step 4: Push and open a PR

```bash
git push origin feat/add-search-endpoint
# -> Go to GitHub and open the Pull Request
```

### Step 5: Merge the PR (after CI passes)

Once the green checks appear on GitHub, click **Merge**.

---

## Commit Message Conventions

Use **Conventional Commits**: `<type>: <description>`

| Type       | Use Case           |
| ---------- | ------------------ |
| `feat`     | New feature        |
| `fix`      | Bug fix            |
| `docs`     | Documentation      |
| `style`    | Formatting         |
| `refactor` | Code restructuring |
| `test`     | Adding tests       |

---

## Release Process

### Step 1: Tag the new version

```bash
git checkout main
git pull origin main
git tag v1.0.2
git push origin v1.0.2
```

### Step 2: Create the GitHub Release

1. Go to **Releases** -> **Draft a new release**.
2. Select your tag (`v1.0.2`).
3. Click **"Generate release notes"**.
4. Click **Publish release**.

---

## Quick Reference Commands

```bash
# Run tests locally
composer test

# Run static analysis
composer analyse

# Format code
composer format
```
