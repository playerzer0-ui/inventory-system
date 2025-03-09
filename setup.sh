#!/bin/bash
sudo composer install
sudo php artisan key:generate
sudo php artisan migrate:fresh
sudo php artisan db:seed