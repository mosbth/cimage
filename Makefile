#!/usr/bin/make -f
#
#


#
# Build
#
.PHONY: build

build:
	[ -d build ] || mkdir build
	rm -rf build/*



#
# Various test to pass build
#
.PHONY: test

test: build phpunit phpcs



#
# phpcs
#
.PHONY: phpcs

phpcs:
	phpcs --standard=.phpcs.xml | tee build/phpcs



#
# phpcbf
#
.PHONY: phpcbf

phpcbf:
	phpcbf --standard=.phpcs.xml


#
# phpunit
#
.PHONY: phpunit

phpunit:
	phpunit --configuration .phpunit.xml



#
# phpdoc
#
.PHONY: phpdoc

phpdoc:
	phpdoc --config=.phpdoc.xml


# ------------------------- OBSOLETE TO BE REMOVED?

#
# Build and development environment using make
#
COMPOSER_PACKAGES = 					\
	"phpunit/phpunit=4.*" 				\
	"sebastian/phpcpd=2.*"				\
	"phploc/phploc=2.*"					\
	"phpdocumentor/phpdocumentor=2.*"	\
	"squizlabs/php_codesniffer=2.*"		\
	"phpmd/phpmd=@stable"				\

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
# composer
#
.PHONY: composer-require composer-update

composer-require: 
	composer --sort-packages --update-no-dev global require $(COMPOSER_PACKAGES)

composer-update:
	composer --no-dev global update



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
