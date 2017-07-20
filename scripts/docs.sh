#!/bin/bash

base='/home/william/Development/gitlab.com/php@contasobcontrole/project'

cd  ${base}/back/vendor/phpzm/core

rm -rf .docs/

composer run sami

cp -R ${base}/back/vendor/phpzm/core/.docs/html/* ${base}/docs

cp ${base}/docs/css/sami.css.sample ${base}/docs/css/sami.css

cd ${base}/docs

git add --all

git commit -m "Update doc $(date)"

git push -u origin master
