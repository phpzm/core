#!/usr/bin/env bash

clear
version=$2
cd ../../

shopt -s dotglob
find * -prune -type d | while read d; do
    cd "$d"
    output=$(git status)
    if [[ ${output} == *"Changes not staged for commit"* ]]; then
      update=0
      echo "$d"
    fi
    cd ../
done

if [[ ${update} -ne 0 ]]; then
    php core/scripts/updater.php ${version}
fi
