#!/usr/bin/make -f
#
#

# Colors
NO_COLOR		= \033[0m
TARGET_COLOR	= \033[32;01m
OK_COLOR		= \033[32;01m
ERROR_COLOR		= \033[31;01m
WARN_COLOR		= \033[33;01m
ACTION			= $(TARGET_COLOR)--> 

# Add local bin path for test tools
BIN 		= bin
VENDORBIN 	= vendor/bin
NPMBIN		= node_modules/.bin


# target: help          - Displays help.
.PHONY:  help
help:
	@echo "$(ACTION)Displaying help for this Makefile.$(NO_COLOR)"
	@echo "Usage:"
	@echo " make [target] ..."
	@echo "target:"
	@egrep "^# target:" Makefile | sed 's/# target: / /g'



# target: clean         - Remove all generated files.
.PHONY:  clean
clean:
	@echo "$(ACTION)Remove all generated files$(NO_COLOR)"
	rm -rf build
	rm -f npm-debug.log



# target: clean-all     - Remove all installed files.
.PHONY:  clean-all
clean-all: clean
	@echo "$(ACTION)Remove all installed files$(NO_COLOR)"
	rm -rf bin
	rm -rf node_modules
	rm -rf vendor



# target: build-prepare - Prepare the build directory.
.PHONY: build-prepare
build-prepare:
	@echo "$(ACTION)Prepare the build directory$(NO_COLOR)"
	install -d build
	#install -d bin/pip
	rm -rf build/*



# target: test          - Various test to pass build.
.PHONY: test
test: build-prepare phpunit behat phpcs



# target: phpcs         - Run phpcs for PHP code style.
.PHONY: phpcs
phpcs: build-prepare
	@echo "$(ACTION)phpcs$(NO_COLOR)"
	$(VENDORBIN)/phpcs --standard=.phpcs.xml | tee build/phpcs



# target: phpcbf        - Run phpcbf to fix PHP code style.
.PHONY: phpcbf
phpcbf:
	@echo "$(ACTION)phpcbf$(NO_COLOR)"
	$(VENDORBIN)/phpcbf --standard=.phpcs.xml



# target: phpunit       - Run phpunit for unit testing PHP.
.PHONY: phpunit
phpunit: build-prepare
	@echo "$(ACTION)phpunit$(NO_COLOR)"
	$(VENDORBIN)/phpunit --configuration .phpunit.xml



# target: behat        - Run behat for feature tests.
.PHONY: behat
behat:
	@echo "$(ACTION)behat$(NO_COLOR)"
	$(VENDORBIN)/behat



# target: phpdoc        - Run phpdoc to create API documentation.
.PHONY: phpdoc
phpdoc:
	@echo "$(ACTION)phpdoc$(NO_COLOR)"
	$(VENDORBIN)/phpdoc --config=.phpdoc.xml


# target: tag-prepare   - Prepare to tag new version.
.PHONY: tag-prepare
tag-prepare:
	@echo "$(ACTION)Prepare to tag new version, perform selfcheck$(NO_COLOR)"



# ------------------------- OBSOLETE TO BE REMOVED?

NPM_PACKAGES = 							\
	htmlhint							\
	csslint								\
	less								\

APM_PACKAGES = 							\
	linter 								\
	linter-htmlhint 					\
	linter-csslint 						\
	linter-less 						\
	linter-jscs 						\
	linter-jshint 						\
	linter-pep8 						\
	linter-pylint 						\
	linter-php 							\
	linter-phpcs 						\
	linter-phpmd 						\
	linter-shellcheck 					\
	linter-xmllint						\
	block-travel 						\



#
# less
#
.PHONY: less

less:
	lessc --clean-css app/css/style.less htdocs/css/style.css




#
# All developer tools
#
.PHONY: tools-config tools-install tools-update

tools-config: npm-config
	
tools-install: composer-require npm-install apm-install

tools-update: composer-update npm-update apm-update



#
# npm
#
.PHONY: npm-config npm-installl npm-update

npm-config: 
	npm config set prefix '~/.npm-packages'
	
npm-install: 
	npm -g install $(NPM_PACKAGES)

npm-update: 
	npm -g update



#
# apm
#
.PHONY: apm-installl apm-update

apm-install: 
	apm install $(APM_PACKAGES)

apm-update:
	apm update --confirm=false
