#!/usr/bin/env bash

clear
cd ../../
version=$1

shopt -s dotglob
find * -prune -type d | while read d; do
    if [ -f ".dirty" ]; then
        cd "$d"
        tag=$(grep -Po '(?<="version": ")[^"]*' composer.json)
        echo "\"phpzm/$d\": \"$tag\""
        git tag "${tag}"
        git push origin "tags/${tag}" --quiet
        rm ".dirty"
        cd ../
    fi
done
