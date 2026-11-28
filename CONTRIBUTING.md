Contributing
============

Policies to follow
------------------

### Commit messages

Follow the specifications of [conventionalcommits.org](https://www.conventionalcommits.org/en/v1.0.0/#specification),
the only change: if there's any issue related to a commit, it should be mentioned on the beginning of the commit message:

`[issue_id] <type>[optional scope]: <description>`

Where the `issue_id` matches with the following regular: `\#(?<id>\d+)`

Example for the first issue: `1# feat: initiate project`

### Coding standards

To check or fix coding standard issues, run phpcs or phpcbf with PSR2 standard.

```shell
vendor/bin/phpcbf --standard=PSR2 src Tests
```

or if you don't have php installed:

```shell
docker run -it --user=root --volume ./:/home/app/docroot pelso/php-dev-xdebug:7.2 vendor/bin/phpcbf --standard=PSR2 src Tests
```

(or with another container having a composer)

### Tests

Run the unit suite in `Tests/Unit`:

```shell
./vendor/bin/phpunit --testsuite unit --testdox
```

or if you don't have php installed:

```shell
docker run -it --user=root --volume ./:/home/app/docroot pelso/php-dev-xdebug:7.2 ./vendor/bin/phpunit --testsuite unit --testdox
```

(or with another container having a composer)

Framework integration tests go in `Tests/Integration` and use `--testsuite integration`.

### Code coverage

Generate a separate HTML report for each suite.

```shell
XDEBUG_MODE=coverage ./vendor/bin/phpunit --testsuite unit --coverage-html test_artifacts/Unit/coverage
XDEBUG_MODE=coverage ./vendor/bin/phpunit --testsuite integration --coverage-html test_artifacts/Integration/coverage
```

To run either command in Docker:

```shell
docker run -it --user=root --volume ./:/home/app/docroot -e XDEBUG_MODE=coverage pelso/php-dev-xdebug:7.2 ./vendor/bin/phpunit --testsuite unit --coverage-html test_artifacts/Unit/coverage
docker run -it --user=root --volume ./:/home/app/docroot -e XDEBUG_MODE=coverage pelso/php-dev-xdebug:7.2 ./vendor/bin/phpunit --testsuite integration --coverage-html test_artifacts/Integration/coverage
```
