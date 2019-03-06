# B7

System for showing and managing simple surveys.

## Getting started

### Prerequisites

You need Docker for running this project.

The [itkdev-docker-compose](https://github.com/aakb/itkdev-docker#helper-scripts) helper scripts is nice to have, but not required. Although almost all following examples of interactions with the docker containers will use the helper scripts. 

### Installing

Start docker containers:

```
$ docker-compose up -d
```

Make composer install dependencies and create needed directories:

```
$ itkdev-docker-compose composer install
```

Run the migrations:
```
$ itkdev-docker-compose bin/console doctrine:migrations:migrate --no-interaction
```

Create a super admin user:

```
$ itkdev-docker-compose bin/console fos:user:create --super-admin
```

Open up the url for the nginx container:
```
$ itkdev-docker-compose url # Outputs the url to the site
$ itkdev-docker-compose open # Opens the site in the default browser
``` 