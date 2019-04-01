# B7

System for showing and managing simple surveys.

**Built with:**
- [Symfony](https://symfony.com)
- [EasyAdmin](https://symfony.com/doc/master/bundles/EasyAdminBundle/index.html)
- [EasyAdmin Extension](https://github.com/alterphp/EasyAdminExtensionBundle)
- [FOSUserBundle](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html)
- [Encore](https://symfony.com/doc/current/frontend/encore/installation.html) enabled

**Extendings of existing functionality**

This project adds some extra functionality based on roles when creating entities in the ui and listing entities in the ui.
This means that when you set the role property on a field in config/packages/easy_admin.yaml it will affect the fields in 
the create and edit form and in the table when listing entities.

Example for list action:

```yaml
# config/packages/easy_admin.config

# Here only users with the ROLE_ADMIN role will see the user field in the listing view.
Survey:
  class: App\Entity\Survey
  role: ROLE_USER
  list:
    fields:
      - id
      - title
      - question
      - { property: 'user', role: ROLE_ADMIN } 
```

Example for form:

```yaml
# config/packages/easy_admin.config

# Here only users with the ROLE_ADMIN role will see the user field when creating and editing Surveys.
# NB! This overrides the EasyAdmin extension functionality that hides fields when setting the role property.
Survey:
  class: App\Entity\Survey
  role: ROLE_USER
  form:
    fields:
      - title
      - question
      - positive_follow_up
      - negative_follow_up
      - follow_up_text_1
      - follow_up_text_2
      - follow_up_text_3
      - follow_up_text_4
      - follow_up_text_5
      - { property: 'user', role: ROLE_ADMIN }
```

## Getting started

### Prerequisites

You need Docker for running this project.

The [itkdev-docker-compose](https://github.com/aakb/itkdev-docker#helper-scripts) helper scripts is nice to have, but not required. Although almost all following examples of interactions with the docker containers will use the helper scripts. 

### Installing

Create local copy of .env file in your project directory:
```
$ cp .env .env.local
```

And fill out the database settings and mail settings in your .env.local:
```
# .env.local
DATABASE_URL=mysql://db:db@mariadb_1:3306/db

MAILER_URL=smtp://mailhog_1
```

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