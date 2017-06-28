NODE_BINDIR = ../node_modules/.bin
export PATH := $(NODE_BINDIR):$(PATH)

# Available locales for the app.
LOCALES = el eo fi_FI ko pt_PT et_EE eu th_TH gl id es ru nl tr hu_HU ja lt_LT it ca en_GB bg_BG fr pt_BR de da fa sk_SK de_DE es_MX cs_CZ is he zh_CN sq oc sv pl uk sl nb_NO


# Name of the generated .po files for each available locale.
LOCALE_FILES ?= $(patsubst %,locale/%/market.po,$(LOCALES))

# Makefile Targets
.PHONY: clean makemessages translations push pull

clean:
	rm -rf template.pot translations.json locale

makemessages:
	touch template.pot
	xgettext --language=JavaScript --keyword=t \
		--from-code=utf-8 --join-existing --no-wrap \
		--package-name=Market \
		--package-version=0.0.1 \
		--output=template.pot ../js/market.bundle.js
	xgettext --language=PHP --keyword=t --keyword=n:1,2 \
		--from-code=utf-8 --join-existing --no-wrap \
		--package-name=Market \
		--package-version=0.0.1 \
		--output=template.pot ../lib/Controller/MarketController.php
	xgettext --language=PHP --keyword=t --keyword=n:1,2 \
		--from-code=utf-8 --join-existing --no-wrap \
		--package-name=Market \
		--package-version=0.0.1 \
		--output=template.pot ../lib/MarketService.php

translations:
	gettext-compile --output translations.json $(LOCALE_FILES)

push:
	tx -d push -s
pull:
	tx -d pull -a --minimum-perc=15

transifex-sync: clean makemessages push pull translations
