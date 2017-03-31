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
    echo "\"phpzm/$d\": \"$tag\","
    git add --all --quiet
    git commit -m "Update version [$(date)]" --quiet
    #git tag "${tag}" --quiet
    #git push origin "tags/${tag}" --quiet
    git push origin master --quiet
    cd ../
done
