SHELL := /bin/bash

#
# Define NPM and check if it is available on the system.
#
NPM := $(shell command -v npm 2> /dev/null)
ifndef NPM
    $(error npm is not available on your system, please install npm)
endif

PHPUNIT="$(PWD)/lib/composer/phpunit/phpunit/phpunit"

market_doc_files=LICENSE README.md
market_src_dirs=appinfo css img js lib templates vendor
market_all_src=$(market_src_dirs) $(market_doc_files)
build_dir=build
dist_dir=$(build_dir)/dist
COMPOSER_BIN=$(build_dir)/composer.phar

# internal aliases
composer_deps=vendor/
composer_dev_deps=lib/composer/phpunit
js_deps=node_modules/

#
# Catch-all rules
#
.PHONY: all
all: $(composer_dev_deps) $(js_deps)

.PHONY: clean
clean: clean-composer-deps clean-js-deps clean-dist clean-build


#
# Basic required tools
#
$(COMPOSER_BIN):
	mkdir $(build_dir)
	cd $(build_dir) && curl -sS https://getcomposer.org/installer | php

#
# ownCloud market PHP dependencies
#
$(composer_deps): $(COMPOSER_BIN) composer.json composer.lock
	php $(COMPOSER_BIN) install --no-dev

$(composer_dev_deps): $(COMPOSER_BIN) composer.json composer.lock
	php $(COMPOSER_BIN) install --dev

.PHONY: clean-composer-deps
clean-composer-deps:
	rm -f $(COMPOSER_BIN)
	rm -Rf $(composer_deps)

.PHONY: update-composer
update-composer: $(COMPOSER_BIN)
	rm -f composer.lock
	php $(COMPOSER_BIN) install --prefer-dist

#
# ownCloud market JavaScript dependencies
#
$(js_deps): $(NPM) package.json
	$(NPM) install
	touch $(js_deps)

.PHONY: install-js-deps
install-js-deps: $(js_deps)

.PHONY: update-js-deps
update-js-deps: $(js_deps)


.PHONY: clean-js-deps
clean-js-deps:
	rm -Rf $(js_deps)

#
# build
#
.PHONY: js/market.bundle.js
js/market.bundle.js: $(js_deps)
	$(NPM) run build

#
# dist
#

$(dist_dir)/market: $(composer_deps)  $(js_deps)  js/market.bundle.js
	rm -Rf $@; mkdir -p $@
	cp -R $(market_all_src) $@
	find $@/vendor -type d -iname Test? -print | xargs rm -Rf
	find $@/vendor -name travis -print | xargs rm -Rf
	find $@/vendor -name doc -print | xargs rm -Rf
	find $@/vendor -iname \*.sh -delete
	find $@/vendor -iname \*.exe -delete

.PHONY: dist
dist: $(dist_dir)/market

.PHONY: clean-dist
clean-dist:
	rm -Rf $(dist_dir)

.PHONY: clean-build
clean-build:
	rm -Rf $(build_dir)
