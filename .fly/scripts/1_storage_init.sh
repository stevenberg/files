#!/usr/bin/env bash

FOLDER=/var/www/html/storage/app
if [ ! -d "$FOLDER" ]; then
    echo "$FOLDER is not a directory, copying .fly/storage_init content to storage"
    cp -r /var/www/html/.fly/storage_init/. /var/www/html/storage
fi


FOLDER=/var/www/html/storage/database
if [ ! -d "$FOLDER" ]; then
    echo "$FOLDER is not a directory, initializing database"
    mkdir /var/www/html/storage/database
    touch /var/www/html/storage/database/database.sqlite
fi
