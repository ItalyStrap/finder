@ECHO OFF
wp plugin deactivate --all && wp site empty --yes && wp plugin activate finder && wp db export tests/_data/dump.sql