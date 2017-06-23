NODE_BINDIR = ../node_modules/.bin
export PATH := $(NODE_BINDIR):$(PATH)

# Available locales for the app.
LOCALES = bg_BG ca cs_CZ da de de_DE el en_GB eo es es_MX et_EE eu fa fi_FI fr gl he hu_HU id is it ja ko lt_LT nb_NO nl oc pl pt_BR pt_PT ru sk_SK sl sq sv th_TH tr uk zh_CN

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

translations:
	gettext-compile --output translations.json $(LOCALE_FILES)

push:
	tx -d push -s
pull:
	tx -d pull -a --minimum-perc=20

transifex-sync: clean makemessages push pull translations
