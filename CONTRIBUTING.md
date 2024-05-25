# Contributing to WP OOP Plugin Lib

Thank you for your interest in contributing to this library! At this point, it is still in an early development stage, but especially because of that feedback is much appreciated!

Just two general guidelines:
* All contributors are expected to follow the [WordPress Code of Conduct](https://make.wordpress.org/handbook/community-code-of-conduct/).
* All contributors who submit a pull request are agreeing to release their contribution under the [GPLv2+ license](https://github.com/WordPress/performance/blob/trunk/LICENSE).

## Providing feedback

If you're already using the library in a WordPress plugin, or you've started experimenting with it, you may run into limitations, or you simply may have questions on how a certain part of the infrastructure is supposed to work. You may run into a bug, or you may think about another WordPress API that you would like to see covered by this library. In any case, please let me know by [opening an issue](https://github.com/felixarntz/wp-oop-plugin-lib/issues/new/choose)!

## Sharing your use-cases

I'd love to learn how this library is being used by the WordPress ecosystem! Not only out of pure curiosity, but also because it allows me to improve it to potentially cater for those use-cases better. If you're already using the library in an open-source project, please share a link to the repository on [this issue](https://github.com/felixarntz/wp-oop-plugin-lib/issues/1)!

## Contributing code

Pull requests are welcome! For little fixes, feel free to go right ahead and open one. For new features or larger enhancements, I'd encourage you to open an issue first where we can scope and discuss the change. Though of course in any case feel free to jump right into writing code! You can do so by [forking this repository](https://github.com/felixarntz/wp-oop-plugin-lib/fork) and later opening a pull request with your changes.

### Guidelines for contributing code

If you're interested in contributing code, please consider the following guidelines and best practices:

* All code must follow the [WordPress Coding Standards and best practices](https://developer.wordpress.org/coding-standards/), including documentation. They are enforced via the project's PHP_CodeSniffer configuration.
* All code must be backward-compatible with WordPress 6.0 and PHP 7.2.
* All code must pass the automated PHP code quality requirements via the project's PHPMD and PHPStan configuration.
* All functional code changes should be accompanied by PHPUnit tests.

### Getting started with writing code

For the linting tools to work, you'll need to have [Composer](https://getcomposer.org/) installed on your machine. In order to make use of the built-in development environment including the ability to run the PHPUnit tests, you'll also need [Docker](https://www.docker.com/) and [Node.js](https://nodejs.org/).

The following linting commands are available:

* `composer lint`: Checks the code with [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/).
* `composer format`: Automatically fixes code problems detected by PHPCodeSniffer, where possible.
* `composer phpmd`: Checks the code with [PHPMD](https://github.com/phpmd/phpmd).
* `composer phpstan`: Checks the code with [PHPStan](https://github.com/phpstan/phpstan).

The following commands allow running PHPUnit tests using the built-in environment:

* `npm run test-php`: Runs the PHPUnit tests for a regular (single) WordPress site.
* `npm run test-php-multisite`: Runs the PHPUnit tests for a WordPress multisite.

The project comes with a demo WordPress plugin file `wp-oop-plugin-lib.php`, which does nothing but autoload the library's classes. If you need to quickly test some WordPress or library code, feel free to temporarily modify the file in your development environment. You can access the built-in environment with that demo plugin active using [wp-env](https://www.npmjs.com/package/@wordpress/env):

* `npm run wp-env start`: Starts the environment (typically available at `http://localhost:8888/`).
* `npm run wp-env stop`: Stops the environment.
