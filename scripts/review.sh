#!/usr/bin/env bash

clear
cd ../../

shopt -s dotglob
find * -prune -type d | while read d; do
    cd "$d"
    echo -n "$d"
    if [ -f ".dirty" ]; then
        rm ".dirty"
    fi
    output=$(git status)
    if [[ ${output} == *"Changes not staged for commit"* ]]; then
        echo -n "*"
        touch ".dirty"
    fi
    echo ""
    cd ../
done
