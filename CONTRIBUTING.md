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
vendor/bin/phpcbf --standard=PSR2 --ignore=vendor --ignore=coverage ./
```

or if you don't have php installed:

```shell
docker run -it --user=root --volume ./:/home/app/docroot pelso/php-dev-xdebug:7.2 vendor/bin/phpcbf --standard=PSR2 --ignore=vendor --ignore=coverage ./
```

(or with another container having a composer)

### Tests

Run the unit tests:

```shell
./vendor/bin/phpunit --testdox
```

or if you don't have php installed:

```shell
docker run -it --user=root --volume ./:/home/app/docroot pelso/php-dev-xdebug:7.2 ./vendor/bin/phpunit --testdox
```

(or with another container having a composer)

### Code coverage

Generate coverage:

```shell
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage
```

or if you don't have php installed:


```shell
docker run -it --user=root --volume ./:/home/app/docroot -e XDEBUG_MODE=coverage pelso/php-dev-xdebug:7.2 ./vendor/bin/phpunit --coverage-html coverage
```