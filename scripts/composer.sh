#!/usr/bin/env bash

clear
cd ../../

shopt -s dotglob
find * -prune -type d | while read d; do
    if [[ ${d} != "core" ]]; then
        cd "$d"
        tag=$(grep -Po '(?<="version": ")[^"]*' composer.json)
        echo "\"phpzm/${d}\": \">=${tag}\","
        cd ../
    fi
done
