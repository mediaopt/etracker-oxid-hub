#!/usr/bin/env bash
if [ "$#" -ne 1 ]; then
  echo "$0 <git-user>" >&2
  exit 1
fi
project=`basename $PWD`.git
ssh -t -p4242 mediaopt@kerker.mediaopt.de "./initialize-git.sh \"$project\""
git init
git remote add origin https://$1@kerker.mediaopt.de/git/$project
git add .
git commit -a -m "Initial commit."
git push origin master