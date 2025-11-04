#!/bin/sh

# Do not mix test output with other stuff
clear

# Every path mentioned here is relative to this file
cd "$(dirname "$0")"

echo 'Borrando base de datos...'

# Deletes every value on the database!
# The backend automatically regenerates the tables
# Change `podman` to `docker` if the other is preferred
podman exec satellite-database \
  mariadb -B -u root --password=password \
  -e 'drop database if exists satellite;'

echo 'Ejecutando pruebas...'

# Creates user "test" with password "123456"
hurl --test auth/signup.hurl
# Requires user "test" with password "123456", expects user "nonexistent" to not exist
hurl --test auth/login.hurl
# Requires user "test" with password "123456"
hurl --test auth/logout.hurl
