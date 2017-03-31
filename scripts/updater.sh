#!/usr/bin/env bash

clear
version=$1
cd ../../

shopt -s dotglob
find * -prune -type d | while read d; do
    cd "$d"
    output=$(git status)
    if [[ ${output} == *"Changes not staged for commit"* ]]; then
      update=1
      echo "$d"
    fi
    cd ../
done

echo ${update}
if [[ ${update} -ne 1 ]]; then
    php core/scripts/updater.php ${version}
fi

shopt -s dotglob
find * -prune -type d | while read d; do
    cd "$d"
    tag=$(grep -Po '(?<="version": ")[^"]*' composer.json)
    git add --all
    git commit -m "Update version [$(date)]"
    git tag "${tag}"
    git push origin --porcelain --progress "tags/${tag}"
    cd ../
done
