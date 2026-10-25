# AGENTS.md

## Project

SensorLogger is a Nextcloud app for collecting, storing, and presenting sensor data.

The existing repository is the source of truth for the app's functionality, architecture,
supported Nextcloud versions, and development workflow.

## General instructions

* Inspect the existing implementation before making changes.
* Follow the architecture, naming conventions, and coding style already used in the project.
* Prefer small, focused changes over unnecessary refactoring.
* Do not introduce new dependencies without a clear need.
* Do not invent features, configuration options, or requirements.
* If requirements or existing behavior are unclear, investigate first and document any assumptions.

## Nextcloud compatibility

* Follow the Nextcloud public application APIs (OCP) wherever possible.
* Avoid relying on internal Nextcloud APIs (OC) unless the existing implementation requires them
  and no suitable public alternative exists.
* Preserve compatibility with the Nextcloud and PHP versions declared by the project.
* Do not assume that APIs available in the latest Nextcloud release are available in older supported
  releases.
* Follow Nextcloud conventions for app bootstrapping, routing, controllers, dependency injection,
  database access, and migrations.

## PHP and code quality

* Follow the existing PHP version constraints and coding standards.
* Use strict typing and type declarations where consistent with the project.
* Prefer dependency injection over directly instantiating services.
* Keep controllers thin and separate application logic into appropriate services.
* Handle errors explicitly and use Nextcloud's logging facilities where appropriate.
* Avoid suppressing errors or exceptions merely to make tests pass.

## Sensor data and persistence

* Preserve the integrity and consistency of recorded sensor data.
* Validate incoming data before storing or processing it.
* Handle timestamps, units, missing values, and invalid measurements explicitly.
* Avoid silently changing existing data formats or measurement semantics.
* Use Nextcloud's supported database abstraction and migration mechanisms.
* Make schema changes compatible with existing installations and upgrades.

## Security and privacy

* Treat sensor data as potentially sensitive.
* Enforce authentication, authorization, and ownership checks where applicable.
* Validate and constrain user-supplied input.
* Use parameterized database queries and safe output handling.
* Never expose credentials, tokens, private configuration, or sensitive sensor data in logs.
* Do not introduce public endpoints or weaken CSRF protection without an explicit requirement.
* Never commit secrets or production data.

## Frontend

* Follow the frontend framework and build tooling already present in the repository.
* Reuse existing Nextcloud UI components and conventions where practical.
* Do not introduce a new frontend framework or build system without justification.
* Keep frontend and backend behavior consistent, including validation and error handling.

## Testing and validation

Before changing code:

1. Inspect the relevant implementation and existing tests.
2. Identify the appropriate test or validation commands.

After changing code:

1. Run the relevant tests and available static analysis.
2. Validate frontend changes using the existing build and lint commands, where applicable.
3. Check database migrations and upgrade behavior when persistence changes.
4. Report the commands executed and their results.
5. Explicitly identify checks that could not be performed.

Do not claim that tests pass unless they were actually executed successfully.

## Git and change management

* Do not discard unrelated working-tree changes.
* Do not rewrite Git history, force-push, or delete branches without explicit authorization.
* Do not commit changes unless requested.
* Keep changes focused and explain significant design decisions.
* Update relevant documentation when behavior, configuration, or development procedures change.

## Working approach

* Explore before editing.
* Prefer evidence from the repository over assumptions.
* Ask for clarification when a decision could affect compatibility, stored data, security, or existing user workflows.
* At completion, summarize the changes, validation performed, and any remaining concerns.
