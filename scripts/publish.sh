#!/usr/bin/env bash

clear
cd ../../
version=$1

shopt -s dotglob
find * -prune -type d | while read d; do
    if [[ ${d} != "core" ]]; then
        cd "$d"
        tag=$(grep -Po '(?<="version": ")[^"]*' composer.json)
        echo "\"phpzm/$d\": \"$tag\""
        git tag "${tag}"
        git push origin "tags/${tag}" --quiet
        cd ../
    fi
done
