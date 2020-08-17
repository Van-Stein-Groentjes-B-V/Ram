# RAM - Project Management Framework

Project management made easy, for small and medium enterprices.

[![Github Issues](https://img.shields.io/github/issues/Van-Stein-Groentjes-B-V/Ram)](https://github.com/Van-Stein-Groentjes-B-V/Ram/issues) [![License](https://img.shields.io/github/license/Van-Stein-Groentjes-B-V/Ram)](https://github.com/Van-Stein-Groentjes-B-V/Ram/)

![logo](https://s-g.nu/ram/img/ramlogo_small.png)

## How to setup

- Install composer
- run `composer install` in the root directory
- Transfer all files to the location you want to use the system. (For example: https://mysite.com/ram/)
- Open a browser and go to https://mysite.com/ram/
- Follow the installation steps (fill in the database details and your admin account)
- We advice to use a secure connection and force HTTPS.
- Login with your just created account and you are done.

## Setting up a local environment (for developers)

- Install composer
- run `composer install` in the root directory
- Make sure you have Docker-Desktop installed
- run `docker-compose up` to start a localhost session.
- Follow the installation steps on localhost with datbase name "local", host: "mariadb", user: "root" and password "rootpwd". (without ")
- Done

## Coding styles

Make sure you write your code using the SG coding styles, this is the PSR-12 standard with braces `{}` on the same line in functions and classes.  
Please also make sure your lines are not too long (200 chars).  
You can check this with `composer phpcsfull` and you can autofix simple stuff with `composer phpcbf`.  
Make sure there are no errors before creating a pull-request.

## Auto documentation.

Make sure that you write PHPDocBlocks in front of every function and class.
Auto generate the documentation with

    php phpdoc7.2.phar

This will also tell you any missing documentation strings and bugs.


## License

This project is available under the GNU Public License V3.  


# Contributing to RAM

:+1::tada: First off, thanks for taking the time to contribute! :tada::+1:

The following is a set of guidelines for contributing to RAM and or its modules, which are hosted on GitHub. These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

### How to contribute?

You can contribute by either creating issues or bug reports, or by forking the repository and submitting a pull request.  
Always explain well what you are contributing.

#### Submitting a bug report

Bugs are tracked as GitHub issues. Create an issue on the repository and provide the following information by filling in the template.

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many details as possible. When listing steps, **don't just say what you did, but explain how you did it**. 
* **Provide specific examples to demonstrate the steps**. Include links to files or GitHub pages, or copy/paste snippets, which you use in those examples. If you're providing snippets in the issue, use [Markdown code blocks](https://help.github.com/articles/markdown-basics/#multiple-lines).
* **Describe the behavior you observed after following the steps** and point out what exactly is the problem with that behavior.
* **Explain which behavior you expected to see instead and why.**
* **Include screenshots and animated GIFs** which show you following the described steps and clearly demonstrate the problem. 