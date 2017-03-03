#
#
#

# Detect OS
OS = $(shell uname -s)

# Defaults
ECHO = echo

# Make adjustments based on OS
# http://stackoverflow.com/questions/3466166/how-to-check-if-running-in-cygwin-mac-or-linux/27776822#27776822
ifneq (, $(findstring CYGWIN, $(OS)))
	ECHO = /bin/echo -e
endif

# Colors and helptext
NO_COLOR	= \033[0m
ACTION		= \033[32;01m
OK_COLOR	= \033[32;01m
ERROR_COLOR	= \033[31;01m
WARN_COLOR	= \033[33;01m

# Which makefile am I in?
WHERE-AM-I = $(CURDIR)/$(word $(words $(MAKEFILE_LIST)),$(MAKEFILE_LIST))
THIS_MAKEFILE := $(call WHERE-AM-I)

# Echo some nice helptext based on the target comment
HELPTEXT = $(ECHO) "$(ACTION)--->" `egrep "^\# target: $(1) " $(THIS_MAKEFILE) | sed "s/\# target: $(1)[ ]*-[ ]* / /g"` "$(NO_COLOR)"

# Add local bin path for test tools
#PATH_ORIG = $(PATH)
PATH := "./.bin:./vendor/bin:./node_modules/.bin:$(PATH)"
SHELL := env PATH=$(PATH) $(SHELL)



# target: help               - Displays help.
.PHONY:  help
help:
	echo $(SHELL)
	@$(call HELPTEXT,$@)
	@$(ECHO) "Usage:"
	@$(ECHO) " make [target] ..."
	@$(ECHO) "target:"
	@egrep "^# target:" $(THIS_MAKEFILE) | sed 's/# target: / /g'



# target: prepare            - Prepare for tests and build
.PHONY:  prepare
prepare:
	@$(call HELPTEXT,$@)
	[ -d .bin ] || mkdir .bin
	[ -d build ] || mkdir build
	rm -rf build/*



# target: clean              - Removes generated files and directories.
.PHONY:  clean
clean:
	@$(call HELPTEXT,$@)
	rm -rf build



# target: clean-all          - Removes generated files and directories.
.PHONY:  clean-all
clean-all:
	@$(call HELPTEXT,$@)
	rm -rf .bin build vendor composer.lock



# target: check              - Check version of installed tools.
.PHONY:  check
check: check-tools-php
	@$(call HELPTEXT,$@)



# target: test               - Run all tests.
.PHONY:  test
test: phpunit phpcs phpmd phploc behat
	@$(call HELPTEXT,$@)



# target: doc                - Generate documentation.
.PHONY:  doc
doc: phpdoc
	@$(call HELPTEXT,$@)



# target: build              - Do all build
.PHONY:  build
build: test doc #less-compile less-minify js-minify
	@$(call HELPTEXT,$@)



# target: install            - Install all tools
.PHONY:  install
install: prepare install-tools-php
	@$(call HELPTEXT,$@)



# target: update             - Update the codebase and tools.
.PHONY:  update
update:
	@$(call HELPTEXT,$@)
	git pull
	composer update



# target: tag-prepare        - Prepare to tag new version.
.PHONY: tag-prepare
tag-prepare:
	@$(call HELPTEXT,$@)



# ------------------------------------------------------------------------
#
# PHP
#

# target: install-tools-php  - Install PHP development tools.
.PHONY: install-tools-php
install-tools-php:
	@$(call HELPTEXT,$@)
	curl -Lso bin/phpdoc https://www.phpdoc.org/phpDocumentor.phar && chmod 755 bin/phpdoc

	curl -Lso bin/phpcs https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar && chmod 755 bin/phpcs

	curl -Lso bin/phpcbf https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar && chmod 755 bin/phpcbf

	curl -Lso bin/phpmd http://static.phpmd.org/php/latest/phpmd.phar && chmod 755 bin/phpmd

	curl -Lso bin/phpunit https://phar.phpunit.de/phpunit-5.7.9.phar && chmod 755 bin/phpunit

	curl -Lso bin/phploc https://phar.phpunit.de/phploc.phar && chmod 755 bin/phploc

	curl -Lso bin/behat https://github.com/Behat/Behat/releases/download/v3.3.0/behat.phar && chmod 755 bin/behat

	composer install




# target: check-tools-php    - Check versions of PHP tools.
.PHONY: check-tools-php
check-tools-php:
	@$(call HELPTEXT,$@)
	which phpunit && phpunit --version
	which phploc && phploc --version
	which phpcs && phpcs --version && echo
	which phpmd && phpmd --version && echo
	which phpcbf && phpcbf --version && echo
	which phpdoc && phpdoc --version && echo
	which behat && behat --version && echo



# target: phpunit            - Run unit tests for PHP.
.PHONY: phpunit
phpunit: prepare
	@$(call HELPTEXT,$@)
	phpunit --configuration .phpunit.xml



# target: phpcs              - Codestyle for PHP.
.PHONY: phpcs
phpcs: prepare
	@$(call HELPTEXT,$@)
	phpcs --standard=.phpcs.xml | tee build/phpcs



# target: phpcbf             - Fix codestyle for PHP.
.PHONY: phpcbf
phpcbf:
	@$(call HELPTEXT,$@)
	phpcbf --standard=.phpcs.xml



# target: phpmd              - Mess detector for PHP.
.PHONY: phpmd
phpmd: prepare
	@$(call HELPTEXT,$@)
	- phpmd . text .phpmd.xml | tee build/phpmd



# target: phploc             - Code statistics for PHP.
.PHONY: phploc
phploc: prepare
	@$(call HELPTEXT,$@)
	phploc src > build/phploc



# target: phpdoc             - Create documentation for PHP.
.PHONY: phpdoc
phpdoc:
	@$(call HELPTEXT,$@)
	phpdoc --config=.phpdoc.xml



# target: behat              - Run behat for feature tests.
.PHONY: behat
behat:
	@$(call HELPTEXT,$@)
	behat
