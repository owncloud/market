NODE_BINDIR = ../node_modules/.bin
export PATH := $(NODE_BINDIR):$(PATH)

# Available locales for the app.
LOCALES = de

# Name of the generated .po files for each available locale.
LOCALE_FILES ?= $(patsubst %,locale/%/market.po,$(LOCALES))

# Makefile Targets
.PHONY: clean makemessages translations push pull

clean:
	rm -f template.pot translations.json

makemessages:
	touch template.pot
	xgettext --language=JavaScript --keyword=npgettext:1c,2,3 \
		--from-code=utf-8 --join-existing --no-wrap \
		--package-name=Market \
		--package-version=0.0.1 \
		--output=template.pot ../js/market.bundle.js

translations:
	gettext-compile --output translations.json $(LOCALE_FILES)

push:
	tx -d push -s
pull:
	tx -d pull -a --minimum-perc=75

transifex-sync: clean makemessages push pull translations
