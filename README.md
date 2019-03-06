# B7

System for showing and managing simple surveys.

## Getting started

### Prerequisites

You need Docker for running this project.

The [itkdev-docker-compose](https://github.com/aakb/itkdev-docker#helper-scripts) helper scripts is nice to have, but not required.

### Installing

Start docker containers:

```
$ docker-compose up -d
```

Make composer install dependencies and create needed directories:

```
# Run inside docker container:
$ composer install

# If you have the itkdev-docker-compose helper scripts:
$ itkdev-docker-compose composer install
```

Open up the url for the nginx container. If you have the itkdev-docker-compose helper scripts installed, then the following commands can help:
```
$ itkdev-docker-compose url # Outputs the url to the site
$ itkdev-docker-compose open # Opens the site in the default browser
``` 