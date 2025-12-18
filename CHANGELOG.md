# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-12-16

## Added
- Composer project setup with PHP 8.2 requirements
- PHPUnit tests with mock setup
- Dockerfile with full PHP stack (PDO, GD, mbstring, RabbitMQ, intl)
- GitHub Actions workflow for test automation
- README.md with project description, license (MIT) and API docs link
- RoadRunner integration for local test execution and production
- Supervisor configuration for app and scheduler containers
- Modular architecture: DDD, CQRS, Onion Architecture
- High test coverage and separation of concerns

## Changed
- Initial project versioning and PSR-4 autoloading

## Fixed
- N/A (first release)

---

## [1.1.0] - 2025-12-18

### Added
- **Modular CI/CD Infrastructure**: Transitioned to a reusable workflow architecture using `.github/workflows/_docker.yml` and `.github/workflows/_test.yml`.
- **GHCR Integration**: Automated image storage and retrieval using GitHub Container Registry.
- **Enhanced Caching**: Implemented Docker Buildx with `type=gha` cache to accelerate build times.
- **Dynamic Tagging**: Added `docker/metadata-action` for automated image versioning based on branch names and commit SHAs.

### Changed
- **Dockerfile Architecture**: Refined multi-stage build process to optimize layer reuse and decrease final image size.
- **Security & Permissions**: Moved `chown` operations after the `COPY` stage to ensure the `app` user has full access to `var/cache` and `var/log`.
- **Test Orchestration**: Migrated unit tests to run within a `docker-compose` environment for better service parity.

### Fixed
- **Entrypoint Pathing**: Resolved the `stat /entrypoint.sh: no such file or directory` error by using relative paths in `ENTRYPOINT`.
- **Symfony Cache Write Access**: Fixed a critical `RuntimeException` caused by incorrect permissions in the `test` environment.
- **CI Output Mapping**: Resolved an issue where the image tag was not correctly passed between modular jobs, causing "invalid reference format" errors.

---