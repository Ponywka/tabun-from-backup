#!/bin/sh

cd /app/templates/skin/synio/locale/ru_RU/LC_MESSAGES
msgfmt messages.po
npx po2json -f jed1.x messages.po messages.json

# Set 666 permissions so that files can be edited by users on the host
chmod 666 messages.json messages.mo
