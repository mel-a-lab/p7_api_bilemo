# Mélanie API
> OpenClassRooms Project
[![SymfonyInsight](https://insight.symfony.com/projects/7f94ef16-ed89-4e1a-903b-2541455b116b/big.svg)](https://insight.symfony.com/projects/7f94ef16-ed89-4e1a-903b-2541455b116b)

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
