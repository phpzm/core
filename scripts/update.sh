#!/usr/bin/env bash

clear
cd ../../
options="major,minor,patch"
version=$1

if [[ ",$list," = *",$version,"* ]]; then
   echo "You need to enter one of these options: major, minor, patch"
   exit
fi

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
