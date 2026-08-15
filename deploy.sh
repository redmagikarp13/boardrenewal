#!/bin/sh
# Compila o Sass e sincroniza o plugin com o Kanboard do NAS
set -e
cd "$(dirname "$0")"
npm run build
rsync -az --delete --exclude 'src/' plugins/BoardRenewal/ root@nasleo.local:/opt/kanboard/plugins/BoardRenewal/
echo "BoardRenewal deploy OK — http://nasleo.local:8080"
