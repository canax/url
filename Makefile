#!/usr/bin/env make
#
# An Anax module.
# See organisation on GitHub: https://github.com/canax

# ------------------------------------------------------------------------
#
# General stuff, reusable for all Makefiles.
#

# Detect OS
OS = $(shell uname -s)

# Defaults
ECHO = echo

# Make adjustments based on OS
ifneq (, $(findstring CYGWIN, $(OS)))
	OS_CYGWIN = "true"
	ECHO = /bin/echo -e
else ifneq (, $(findstring Linux, $(OS)))
	OS_LINUX = "true"
else ifneq (, $(findstring Darwin, $(OS)))
	OS_MAC = "true"
endif

# Colors and helptext
NO_COLOR	= \033[0m
ACTION		= \033[32;01m
OK_COLOR	= \033[32;01m
ERROR_COLOR	= \033[31;01m
WARN_COLOR	= \033[33;01m

# Print out colored action message
ACTION_MESSAGE = $(ECHO) "$(ACTION)---> $(1)$(NO_COLOR)"

# Which makefile am I in?
WHERE-AM-I = "$(CURDIR)/$(word $(words $(MAKEFILE_LIST)),$(MAKEFILE_LIST))"
THIS_MAKEFILE := $(call WHERE-AM-I)

# Echo some nice helptext based on the target comment
HELPTEXT = $(call ACTION_MESSAGE, $(shell egrep "^\# target: $(1) " $(THIS_MAKEFILE) | sed "s/\# target: $(1)[ ]*-[ ]* / /g"))

# Check version  and path to command and display on one line
CHECK_VERSION = printf "%-10s %-20s %s\n" "`basename $(1)`" "`which $(1)`" "`$(1) --version $(2)`"

# Get current working directory, it may not exist as environment variable.
PWD = $(shell pwd)

# target: help                    - Displays help.
.PHONY:  help
help:
	@$(call HELPTEXT,$@)
	@sed '/^$$/q' $(THIS_MAKEFILE) | tail +3 | sed 's/#\s*//g'
	@$(ECHO) "Usage:"
	@$(ECHO) " make [target] ..."
	@$(ECHO) "target:"
	@egrep "^# target:" $(THIS_MAKEFILE) | sed 's/# target: / /g'



# ------------------------------------------------------------------------
#
# Specifics for this project.
#
# Default values for arguments
container ?= latest

BIN        := .bin
VENDORBIN  := vendor/bin
PHPUNIT    := $(VENDORBIN)/phpunit
PHPLOC     := $(BIN)/phploc
PHPCS      := $(BIN)/phpcs
PHPCBF     := $(BIN)/phpcbf
PHPMD      := $(BIN)/phpmd
PHPSTAN    := $(VENDORBIN)/phpstan
PHPDOC     := $(BIN)/phpdoc
PHPDOX     := $(BIN)/phpdox
BEHAT      := $(BIN)/behat
SHELLCHECK := $(BIN)/shellcheck
BATS       := $(BIN)/bats



# target: prepare                 - Prepare for tests and build
.PHONY:  prepare
prepare:
	@$(call HELPTEXT,$@)
	[ -d .bin ] || mkdir .bin
	[ -d build ] || mkdir build
	rm -rf build/*



# target: clean                   - Removes generated files and directories.
.PHONY: clean
clean:
	@$(call HELPTEXT,$@)
	rm -rf build
	rm -f .phpunit.result.cache



# target: clean-cache             - Clean the cache.
.PHONY:  clean-cache
clean-cache:
	@$(call HELPTEXT,$@)
	rm -rf cache/*/*



# target: clean-all               - Removes generated files and directories.
.PHONY:  clean-all
clean-all: clean clean-cache
	@$(call HELPTEXT,$@)
	rm -rf .bin vendor composer.lock



# target: check                   - Check version of installed tools.
.PHONY:  check
check: check-tools-bash check-tools-php check-docker
	@$(call HELPTEXT,$@)



# target: test                    - Run all tests.
.PHONY:  test
test: phpunit phpcs phpmd phpstan phploc behat shellcheck bats
	@$(call HELPTEXT,$@)
	composer validate



# target: test-anax               - Run tests in vendor/anax/*.
.PHONY:  test-anax
test-anax:
	@$(call HELPTEXT,$@)
	for repo in `ls vendor/anax` ; do                        \
		$(call ACTION_MESSAGE,"Do \"make install test\" of anax/$$repo");                       \
		$(MAKE) --directory=vendor/anax/$$repo install test; \
	done



# target: doc                     - Generate documentation.
.PHONY:  doc
doc: phpdoc phpdox
	@$(call HELPTEXT,$@)



# target: build                   - Do all build
.PHONY:  build
build: test doc #theme less-compile less-minify js-minify
	@$(call HELPTEXT,$@)



# target: install                 - Install all tools
.PHONY:  install
install: prepare install-tools-php install-tools-bash
	@$(call HELPTEXT,$@)
	composer install



# target: install-lowest          - Install lowest version of deoendencies
.PHONY:  install-lowest
install-lowest:
	@$(call HELPTEXT,$@)
	composer update --no-dev --prefer-lowest



# target: update                  - Update the codebase and tools.
.PHONY:  update
update:
	@$(call HELPTEXT,$@)
	[ ! -d .git ] || git pull
	composer update



# target: tag-prepare             - Prepare to tag new version.
.PHONY: tag-prepare
tag-prepare:
	@$(call HELPTEXT,$@)



# ----------------------------------------------------------------------------
#
# docker
#
# target: docker-up               - Start all docker container="", or specific, default "latest".
.PHONY: docker-up
docker-up:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml up -d $(container)



# target: docker-stop             - Stop running docker containers.
.PHONY: docker-stop
docker-stop:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml stop



# target: docker-run              - Run container="" with what="" one off command.
.PHONY: docker-run
docker-run:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml run $(container) $(what)



# target: docker-bash             - Run container="" with what="bash" one off command.
.PHONY: docker-bash
docker-bash:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml run $(container) bash



# target: docker-exec             - Run container="" with what="" command in running container.
.PHONY: docker-exec
docker-exec:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml exec $(container) $(what)



# target: docker-install          - Run make install in container="".
.PHONY: docker-install
docker-install:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml run $(container) make install



# target: docker-test             - Run make test in container="".
.PHONY: docker-test
docker-test:
	@$(call HELPTEXT,$@)
	[ ! -f docker-compose.yaml ] || docker-compose -f docker-compose.yaml run $(container) make test



# target: check-docker            - Check versions of docker.
.PHONY: check-docker
check-docker:
	@$(call HELPTEXT,$@)
	@$(call CHECK_VERSION, docker, | cut -d" " -f3-)
	@$(call CHECK_VERSION, docker-compose, | cut -d" " -f3-)



# ------------------------------------------------------------------------
#
# PHP
#

# target: install-tools-php       - Install PHP development tools.
.PHONY: install-tools-php
install-tools-php:
	@$(call HELPTEXT,$@)
	# phpdoc
	curl -Lso $(PHPDOC) https://github.com/phpDocumentor/phpDocumentor2/releases/download/v3.0.0-rc/phpDocumentor.phar && chmod 755 $(PHPDOC)

	# phpdox
	curl -Lso $(PHPDOX) http://phpdox.de/releases/phpdox.phar && chmod 755 $(PHPDOX)

	# phpcs
	curl -Lso $(PHPCS) https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar && chmod 755 $(PHPCS)

	# phpcbf
	curl -Lso $(PHPCBF) https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar && chmod 755 $(PHPCBF)

	# phpmd
	curl -Lso $(PHPMD) https://github.com/phpmd/phpmd/releases/download/2.9.1/phpmd.phar && chmod 755 $(PHPMD)

	# phpstan
	curl -Lso $(PHPLOC) https://phar.phpunit.de/phploc.phar && chmod 755 $(PHPLOC)

	# Behat
	curl -Lso $(BEHAT) https://github.com/Behat/Behat/releases/download/v3.3.0/behat.phar && chmod 755 $(BEHAT)

	# Composer install
	[ ! -f composer.json ] || composer install



# target: check-tools-php         - Check versions of PHP tools.
.PHONY: check-tools-php
check-tools-php:
	@$(call HELPTEXT,$@)
	php --version && echo
	composer show && echo
	@$(call CHECK_VERSION, $(PHPUNIT))
	@$(call CHECK_VERSION, $(PHPLOC))
	@$(call CHECK_VERSION, $(PHPCS))
	@$(call CHECK_VERSION, $(PHPCBF))
	@$(call CHECK_VERSION, $(PHPMD))
	@$(call CHECK_VERSION, $(PHPSTAN))
	@$(call CHECK_VERSION, $(PHPDOC))
	@$(call CHECK_VERSION, $(PHPDOX))
	@$(call CHECK_VERSION, $(BEHAT))



# target: phpunit                 - Run unit tests for PHP.
.PHONY: phpunit
phpunit: prepare
	@$(call HELPTEXT,$@)
	[ ! -d "test" ] || $(PHPUNIT) --configuration .phpunit.xml $(options)



# target: phpcs                   - Codestyle for PHP.
.PHONY: phpcs
phpcs: prepare
	@$(call HELPTEXT,$@)
	[ ! -f .phpcs.xml ] || $(PHPCS) --standard=.phpcs.xml | tee build/phpcs



# target: phpcbf                  - Fix codestyle for PHP.
.PHONY: phpcbf
phpcbf:
	@$(call HELPTEXT,$@)
ifneq ($(wildcard test),)
	[ ! -f .phpcs.xml ] || $(PHPCBF) --standard=.phpcs.xml
else
	[ ! -f .phpcs.xml ] || $(PHPCBF) --standard=.phpcs.xml src
endif



# target: phpmd                   - Mess detector for PHP.
.PHONY: phpmd
phpmd: prepare
	@$(call HELPTEXT,$@)
	- [ ! -f .phpmd.xml ] || [ ! -d src ] || $(PHPMD) . text .phpmd.xml | tee build/phpmd



# target: phpstan                 - Static code analysis for PHP.
.PHONY: phpstan
phpstan: prepare
	@$(call HELPTEXT,$@)
	- [ ! -f .phpstan.neon ] || $(PHPSTAN) analyse -c .phpstan.neon | tee build/phpstan



# target: phploc                  - Code statistics for PHP.
.PHONY: phploc
phploc: prepare
	@$(call HELPTEXT,$@)
	[ ! -d src ] || $(PHPLOC) src > build/phploc



# target: phpdoc                  - Create documentation for PHP.
.PHONY: phpdoc
phpdoc:
	@$(call HELPTEXT,$@)
	[ ! -d doc ] || [ ! -f .phpdox.xml ] || $(PHPDOC) --config=.phpdoc.xml



# target: phpdox                  - Create documentation for PHP.
.PHONY: phpdox
phpdox:
	@$(call HELPTEXT,$@)
	[ ! -d doc ] || [ ! -f .phpdox.xml ] || $(PHPDOX) --file .phpdox.xml



# target: behat                   - Run behat for feature tests.
.PHONY: behat
behat:
	@$(call HELPTEXT,$@)
	[ ! -d features ] || $(BEHAT)



# ------------------------------------------------------------------------
#
# Bash
#

# target: install-tools-bash      - Install Bash development tools.
.PHONY: install-tools-bash
install-tools-bash:
	@$(call HELPTEXT,$@)
	# Shellcheck
ifdef OS_LINUX
	curl -Ls https://github.com/koalaman/shellcheck/releases/download/latest/shellcheck-latest.linux.x86_64.tar.xz | tar -xJ -C build/ && rm -f .bin/shellcheck && ln build/shellcheck-latest/shellcheck .bin/
else ifdef OS_MAC
	curl -Ls https://github.com/koalaman/shellcheck/releases/download/latest/shellcheck-latest.darwin.x86_64.tar.xz | tar -xJ -C build/ && rm -f .bin/shellcheck && ln build/shellcheck-latest/shellcheck .bin/
endif

	# Bats
	curl -Lso $(BIN)/bats-exec-suite https://raw.githubusercontent.com/sstephenson/bats/master/libexec/bats-exec-suite
	curl -Lso $(BIN)/bats-exec-test https://raw.githubusercontent.com/sstephenson/bats/master/libexec/bats-exec-test
	curl -Lso $(BIN)/bats-format-tap-stream https://raw.githubusercontent.com/sstephenson/bats/master/libexec/bats-format-tap-stream
	curl -Lso $(BIN)/bats-preprocess https://raw.githubusercontent.com/sstephenson/bats/master/libexec/bats-preprocess
	curl -Lso $(BATS) https://raw.githubusercontent.com/sstephenson/bats/master/libexec/bats
	chmod 755 $(BIN)/bats*



# target: check-tools-bash        - Check versions of Bash tools.
.PHONY: check-tools-bash
check-tools-bash:
	@$(call HELPTEXT,$@)
	@$(call CHECK_VERSION, $(SHELLCHECK))
	@$(call CHECK_VERSION, $(BATS))



# target: shellcheck              - Run shellcheck for bash files.
.PHONY: shellcheck
shellcheck:
	@$(call HELPTEXT,$@)
	[ ! -f src/*.bash ] || $(SHELLCHECK) --shell=bash src/*.bash



# target: bats                    - Run bats for unit testing bash files.
.PHONY: bats
bats:
	@$(call HELPTEXT,$@)
	[ ! -d bats ] || $(BATS) bats/



# ------------------------------------------------------------------------
#
# Developer
#
# target: scaff-reinstall         - Reinstall using scaffolding processing scripts.
.PHONY: scaff-reinstall
scaff-reinstall:
	@$(call HELPTEXT,$@)
	#rm -rf -v !(composer.*|vendor|.anax); .anax/scaffold/postprocess.bash



# ------------------------------------------------------------------------
#
# Theme
#
# target: theme                   - Do make build install in theme/ if available.
.PHONY: theme
theme:
	@$(call HELPTEXT,$@)
	[ ! -d theme ] || $(MAKE) --directory=theme build
	rsync -a theme/build/less/css htdocs/
