#!/bin/bash

# Use : bash ./bin/zip.sh 1-2-8

version=$1
version=${version//-/.}

#上の階層へ
cd ../

# 不要なファイルを除いてzip化
zip -r highlighting-code-block.zip highlighting-code-block -x "*/.*" "*/__*" "*bin*" "*node_modules*" "*vendor*" "*/package*" "*/composer*" "*postcss.config.js" "*/webpack*" "*phpcs.xml*" "*README.md*"

# zipから不要なファイルを削除
zip --delete highlighting-code-block.zip  "highlighting-code-block/.*"
