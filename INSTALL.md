# Vk extractor 
## requirement 
### backend
- Laravel : version 5.3
    - packages:
        - guzzlehttp/guzzle : http client for php
        - fabpot/goutte : http client crawler
        - tymon/jwt-auth : json web token authentication

### FrontEnd
- Vuejs : javascript framework
- vuex : flux model for vue
- vue-router : router for single page application
- vue-resource : http request (promise, URI, resource)

## installation
### backend 
``` bash
git clone https://github.com/drkwolf/vkextractor
cd vkextractor
php composer install
# generate key for laravel
mv .env.example .env
# create sqlite database
touch database/database.sql
```
* edit .env and change
```env
DB_CONNECTION=sqlite
DB_DATABASE=Full_PATH/database/database.sqlite
```
``` bash
# generate secret tokens
artisan key:generate
php artisan jwt:generate
# create database fron migration
php artisna migrate
```

### frontend
npm install 


## Running
php artisan serve --host 0.0.0.0

## Running the Queue 
```
# execute queue jobs
artisan queue:listen
```
