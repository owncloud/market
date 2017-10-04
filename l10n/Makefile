NODE_BINDIR = ../node_modules/.bin
export PATH := $(NODE_BINDIR):$(PATH)

# Available locales for the app.
LOCALES = af_ZA  bg_BG  ca  cs_CZ  da  de  de_DE  el  en_GB  es  et_EE  fi_FI  fr  he  hu_HU  is  it  ja  ko  lt_LT  nb_NO  nl  oc  pl  pt_BR  pt_PT  ru  sl  sq  sv  th_TH  tr  uk  zh_CN


# Name of the generated .po files for each available locale.
LOCALE_FILES ?= $(patsubst %,locale/%/market.po,$(LOCALES))

GETTEXT_JS_SOURCES = $(shell find ../src -name '*.vue' -o -name '*.js')
GETTEXT_PHP_SOURCES = $(shell find .. -name '*.php')

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
		--output=template.pot $(GETTEXT_JS_SOURCES)
	xgettext --language=PHP --keyword=t --keyword=n:1,2 \
		--from-code=utf-8 --join-existing --no-wrap \
		--package-name=Market \
		--package-version=0.0.1 \
		--output=template.pot $(GETTEXT_PHP_SOURCES)

translations:
	gettext-compile --output translations.json $(LOCALE_FILES)

push:
	tx -d push -s
pull:
	tx -d pull -a --minimum-perc=15

transifex-sync: clean makemessages push pull translations
