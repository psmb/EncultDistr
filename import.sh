#!/bin/sh
#Export dump stuff from server, sync and import.

set -e
set -u

# No need to do this, as daily dump is created anyways via cron
ssh www@izm.io -p 1122 'cd /data/www/izm.io/surf/releases/current/ && ./flow db:export --mode=all --sql-file="Data/Persistent/db.sql"'
rsync -rlhvz -e "ssh -p 1122" www@izm.io:/data/www/izm.io/surf/shared/Data/Persistent/ Data/Persistent/
./flow db:import --sql-file="Data/Persistent/db.sql"
./flow flow:cache:flush --force
