#!/usr/bin/env bash

clear
cd ../../
version=$1

php core/scripts/updater.version.php ${version}

php core/scripts/updater.dependencies.php

shopt -s dotglob
find * -prune -type d | while read d; do
    cd "$d"
    if [[ -f ".dirty" ]]; then
        output=$(git status)
        if [[ ${output} == *"Changes not staged for commit"* ]]; then
            version=$(grep -Po '(?<="version": ")[^"]*' composer.json)
            echo "phpzm/$d ~> $version"
            git add --all
            git commit -m "Update version [$(date)]" --quiet
            git push origin master --quiet
        fi
    fi
    cd ../
done
