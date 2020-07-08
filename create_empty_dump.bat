@ECHO OFF
wp plugin deactivate --all && wp site empty --yes && wp plugin activate module && wp db export tests/_data/dump.sql