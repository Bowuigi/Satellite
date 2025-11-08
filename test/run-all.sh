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

# auth/signup.hurl creates user "test"
hurl --test auth/signup.hurl

# Every test except auth/signup.hurl requires user "test" with password "123456" and expects user "nonexistent" to not exist
# posts/*.hurl create new posts
# filters/*.hurl creates various filters
hurl --test \
  auth/login.hurl     \
  auth/logout.hurl    \
  users/list.hurl     \
  users/view.hurl     \
  posts/new.hurl      \
  posts/vote.hurl     \
  filters/remove.hurl \
  filters/edit.hurl   \
