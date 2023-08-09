# Mélanie API
> OpenClassRooms Project

This project is for my training.

## Installing / Getting started

#### step 1) 
Clone this repository

#### step 2)
Run composer
```shell
composer install
```

#### step 3)
Setup your database in your own .env.local

#### step 4)
Create your database
```shell
bin/console doctrine:schema:update -f
```

#### step 5)
Create a user with the below command
```shell
bin/console app:create-user your-email@domain.com your-password
```

#### step 6)
Generate your key pair for JWT
```shell
bin/console lexik:jwt:generate-keypair
```
