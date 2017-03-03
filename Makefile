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
#PATH := "./.bin:./vendor/bin:./node_modules/.bin:$(PATH)"
#SHELL := env PATH=$(PATH) $(SHELL)
PHPUNIT := .bin/phpunit
PHPLOC 	:= .bin/phploc
PHPCS   := .bin/phpcs
PHPCBF  := .bin/phpcbf
PHPMD   := .bin/phpmd
PHPDOC  := .bin/phpdoc
BEHAT   := .bin/behat



# target: help               - Displays help.
.PHONY:  help
help:
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
	curl -Lso $(PHPDOC) https://www.phpdoc.org/phpDocumentor.phar && chmod 755 $(PHPDOC)

	curl -Lso $(PHPCS) https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar && chmod 755 $(PHPCS)

	curl -Lso $(PHPCBF) https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar && chmod 755 $(PHPCBF)

	curl -Lso $(PHPMD) http://static.phpmd.org/php/latest/phpmd.phar && chmod 755 $(PHPMD)

	curl -Lso $(PHPUNIT) https://phar.phpunit.de/phpunit-5.7.9.phar && chmod 755 $(PHPUNIT)

	curl -Lso $(PHPLOC) https://phar.phpunit.de/phploc.phar && chmod 755 $(PHPLOC)

	curl -Lso $(BEHAT) https://github.com/Behat/Behat/releases/download/v3.3.0/behat.phar && chmod 755 $(BEHAT)

	composer install




# target: check-tools-php    - Check versions of PHP tools.
.PHONY: check-tools-php
check-tools-php:
	@$(call HELPTEXT,$@)
	which $(PHPUNIT) && $(PHPUNIT) --version
	which $(PHPLOC) && $(PHPLOC) --version
	which $(PHPCS) && $(PHPCS) --version && echo
	which $(PHPMD) && $(PHPMD) --version && echo
	which $(PHPCBF) && $(PHPCBF) --version && echo
	which $(PHPDOC) && $(PHPDOC) --version && echo
	which $(BEHAT) && $(BEHAT) --version && echo



# target: phpunit            - Run unit tests for PHP.
.PHONY: phpunit
phpunit: prepare
	@$(call HELPTEXT,$@)
	$(PHPUNIT) --configuration .phpunit.xml



# target: phpcs              - Codestyle for PHP.
.PHONY: phpcs
phpcs: prepare
	@$(call HELPTEXT,$@)
	$(PHPCS) --standard=.phpcs.xml | tee build/phpcs



# target: phpcbf             - Fix codestyle for PHP.
.PHONY: phpcbf
phpcbf:
	@$(call HELPTEXT,$@)
	$(PHPCBF) --standard=.phpcs.xml



# target: phpmd              - Mess detector for PHP.
.PHONY: phpmd
phpmd: prepare
	@$(call HELPTEXT,$@)
	- $(PHPMD) . text .phpmd.xml | tee build/phpmd



# target: phploc             - Code statistics for PHP.
.PHONY: phploc
phploc: prepare
	@$(call HELPTEXT,$@)
	$(PHPLOC) src > build/phploc



# target: phpdoc             - Create documentation for PHP.
.PHONY: phpdoc
phpdoc:
	@$(call HELPTEXT,$@)
	$(PHPDOC) --config=.phpdoc.xml



# target: behat              - Run behat for feature tests.
.PHONY: behat
behat:
	@$(call HELPTEXT,$@)
	$(BEHAT)
