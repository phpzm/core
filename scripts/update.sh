#!/usr/bin/env bash

clear
cd ../../
version=$1

php core/scripts/updater.version.php ${version}

php core/scripts/updater.dependencies.php

shopt -s dotglob
find * -prune -type d | while read d; do
    if [[ ${d} != "core" ]]; then
        cd "$d"
        version=$(grep -Po '(?<="version": ")[^"]*' composer.json)
        echo "phpzm/$d ~> $version"
        git add --all
        git commit -m "Update version [$(date)]" --quiet
        git push origin master --quiet
        cd ../
    fi
done
