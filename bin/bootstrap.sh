#!/usr/bin/env bash
if [ "$#" -ne 1 ]; then
  echo "$0 <git-user>" >&2
  exit 1
fi
rm -fR .git
echo "skeleton" >> .gitignore
git clone https://$1@kerker.mediaopt.de/git/bundle.git generic-bundler