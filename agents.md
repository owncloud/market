# agents.md -- Market

## Repository Overview

ownCloud Server 10 Marketplace integration app. Provides an in-app storefront for managing ownCloud apps. Licensed under AGPL-3.0. Built with PHP backend and webpack-based JavaScript frontend.

## Architecture & Key Paths

- `lib/` -- PHP application logic
- `src/` -- Frontend JavaScript source (webpack)
- `templates/` -- Server-side templates
- `img/` -- Images and icons
- `l10n/` -- Translation files
- `tests/` -- Unit tests
- `Makefile` -- Build and test automation
- `composer.json` -- PHP dependencies
- `package.json` -- Node.js dependencies
- `webpack.config.js` -- Webpack configuration

## Development Conventions

- PHP backend follows ownCloud coding standards
- JavaScript frontend built with webpack
- Static analysis with PHPStan

## Build & Test Commands

```bash
make install-js-deps    # Install Node.js dependencies
make build-dev          # Build frontend (development)
make dist               # Create distribution package
make test-php-unit      # Run PHP unit tests
make test-php-style     # Check PHP code style
make test-js            # Run JavaScript tests
```

## Important Constraints

- Licensed under AGPL-3.0 (copyleft). Apache 2.0 migration planned.
- All contributions require a DCO sign-off.


## OSPO Policy Constraints

### GitHub Actions
- **Only** use actions owned by `owncloud`, created by GitHub (`actions/*`), verified on the GitHub Marketplace, or verified by the ownCloud Maintainers.
- Pin all actions to their full commit SHA (not tags): `uses: actions/checkout@<SHA> # vX.Y.Z`
- Never introduce actions from unverified third parties.

### Dependency Management
- Dependabot is configured for automated dependency updates.
- Review and merge Dependabot PRs as part of regular maintenance.
- Do not introduce new dependencies without discussion in an issue first.

### Git Workflow
- **Rebase policy**: Always rebase; never create merge commits. Use `git pull --rebase` and `git rebase` before pushing.
- **Signed commits**: All commits **must** be PGP/GPG signed (`git commit -S -s`).
- **DCO sign-off**: Every commit needs a `Signed-off-by` line (`git commit -s`).
- **Conventional Commits & Squash Merge**: Use the [Conventional Commits](https://www.conventionalcommits.org/) format where the repository enforces it. Many repos use squash merge, where the PR title becomes the commit message on the default branch — apply Conventional Commits format to PR titles as well. A reusable GitHub Actions workflow enforces this.

## Context for AI Agents

Standard ownCloud OC10 app with PHP + webpack frontend. The `src/` directory contains the frontend code built via webpack, while `lib/` contains the PHP backend. The app connects to the ownCloud Marketplace API.
