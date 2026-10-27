#!/bin/sh
# Rebuild, then zip the WordPress theme, the static preview and the full project into dist/.
set -e
cd "$(dirname "$0")"
python3 build.py
rm -rf dist && mkdir -p dist
(cd wordpress && zip -qr ../dist/one-christian-church-wordpress-theme.zip one-christian-church -x '*.DS_Store')
cp -r docs dist/one-christian-church-preview
cp PREVIEW-README.txt dist/one-christian-church-preview/README.txt
(cd dist && zip -qr one-christian-church-static-preview.zip one-christian-church-preview && rm -rf one-christian-church-preview)
# Project folder for a new git repository: sources, build script and the built docs/ (no git history).
git ls-files -z --cached --others --exclude-standard | xargs -0 zip -q dist/one-christian-church-repo.zip
ls -la dist
