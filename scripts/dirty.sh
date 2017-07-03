#!/usr/bin/env bash

clear
cd ../../

shopt -s dotglob
find * -prune -type d | while read d; do
    cd "$d"
    echo -n "$d"
    touch ".dirty"
    echo ""
    cd ../
done
