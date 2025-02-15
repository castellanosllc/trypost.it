

## Migration with base seed
```sh
php artisan migrate:fresh --seed
```

# Start reverb server locally
```sh
php artisan reverb:start --host="0.0.0.0" --port=8080 --hostname="socket.kivo.test"
```

# Start queue worker
```sh
php artisan horizon:watch
```

## Commit Message Convention

To keep our commit history clean and consistent, please follow these guidelines when writing commit messages:

| **Prefix**   | **Meaning**                                                                                             |
|--------------|---------------------------------------------------------------------------------------------------------|
| **build**    | Changes related to build tools or external dependencies (e.g., npm scripts, Webpack, Gradle).          |
| **chore**    | Routine tasks or maintenance that do not affect application behavior (e.g., updating `.gitignore`).    |
| **ci**       | Updates to CI configuration files or scripts (e.g., GitHub Actions, Travis, CircleCI).                 |
| **docs**     | Documentation-only changes (e.g., updating `README.md`, JSDoc comments).                                |
| **feat**     | Introducing a new feature or enhancement.                                                              |
| **fix**      | A bug fix or patch.                                                                                    |
| **perf**     | Performance improvements or optimizations.                                                             |
| **refactor** | Non-functional code refactoring (no new features, no bug fixes).                                       |
| **revert**   | Reverting a previous commit.                                                                           |
| **style**    | Changes that do not affect code logic, such as formatting or styling updates.                          |
| **test**     | Adding or updating tests (no production code changes).                                                |

By using these prefixes, you make your contributions easier to understand and maintain, and you help automate versioning and release notes where needed.
