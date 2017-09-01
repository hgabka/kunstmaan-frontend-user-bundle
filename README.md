KunstmaanFrontendUserBundle
===========================
The bundle adds support for frontend user management for Kunstmaan-based projects. The frontend users are handled completely independently of Kunstmaan users. It also provides FOS bundle-like tools for managing frontend users, like separate User entity, user provider and user manager.
  
Prerequisites
-------------
The bundle requires Symfony 3.0+ and a working installation of the latest Kunstmaan bundles.

Installation
------------
Installation steps:
1. Download the bundle using composer

2. Enable the bundle
3. Create your frontend user class
4. Configure the application's security.yml
5. Configure the bundle
6. Import the routing of the bundle
7. Update the database schema


*Step 1: Download the bundle using composer*

``` bash
$ composer require hgabka/kunstmaan-frontend-user-bundle "~1.0@dev"
```

Composer will install the bundle to your project's vendor/hgabka/kunstmaan-frontend-user-bundle directory.


*Step2: Enable the bundle*

Enable the bundle in the kernel:
``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Hgabka\KunstmaanFrontendUserBundle\HgabkaKunstmaanFrontendUserBundle(),
        // ...
    );
}
```

*Step3: Create your User class*

The goal of the bundle is to add frontend user management to Kunstmaan project. To achieve this goal you must create a frontend user class. The bundle only supports Doctrine ORM at the moment so your only option is to create a Doctrine ORM entity:

``` php
<?php
// src/Acme/PublicBundle/Entity/AcmeFrontendUser.php

namespace Acme\PublicBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="acme_frontend_user")
 */
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUser;

class AcmeFrontendUser extends KunstmaanFrontendUser
{
}
```

Of course you can add fields to the entity that are needed for your application logic.
 
*Step4: Configure your application's security.yml*
 
In order for Symfony's security component to use the Bundle, you must tell it to do so in the security.yml file. The security.yml file is where the basic security configuration for your application is contained. The configuration is slightly different for multi and single language kunstmaan sites.

*_Multi-language site configuration_*

``` yml

# app/config/security.yml

security:
    encoders:
        FOS\UserBundle\Model\UserInterface: bcrypt
        Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface: bcrypt

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: ROLE_ADMIN
        ROLE_NEWS:        ROLE_USER
        ROLE_MEMBER:      ROLE_USER

    providers:
        fos_userbundle:
            id: fos_user.user_provider.username
        hgabka_kunstmaanfrontenduserbundle:
            id: hgabkakunstmaanfrontenduser.user_provider

    firewalls:
        admin:
            pattern:            ^/([^/]*)/admin
            form_login:
                login_path:     fos_user_security_login
                check_path:     fos_user_security_check
                provider:       fos_userbundle
            logout:
              path:             fos_user_security_logout
              target:           KunstmaanAdminBundle_homepage
              invalidate_session: false
            anonymous:          true
            remember_me:
                secret:         %secret%
                lifetime:       604800
                path:           /
                domain:         ~
        main:
            pattern: .*
            form_login:
                login_path: hgabka_kunstmaan_frontend_user_login
                check_path: hgabka_kunstmaan_frontend_user_security_check
                provider: hgabka_kunstmaanfrontenduserbundle
            logout:
                path:   hgabka_kunstmaan_frontend_user_security_logout
                target: your_homepage_route
                invalidate_session: false
            anonymous:    true
            remember_me:
                secret:   "%secret%"
                lifetime: 604800
                path:     /
                domain:   ~

    access_control:
        - { path: ^/([^/]*)/members/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/([^/]*)/members/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/([^/]*)/members/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/([^/]*)/members, role: ROLE_MEMBER }
        - { path: ^/([^/]*)/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/([^/]*)/admin/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/([^/]*)/admin, role: ROLE_ADMIN }
```

Under the ```encoders``` section you have to add a new key for the frontend users' password encryption (sha512 or bcrypt). Under ```role_hierarchy``` a new role is added (ROLE_MEMBER). Of course this is just an example you are free to add roles based on your application logic here. The providers section the bundle's provider is added.

Next, take a look at and examine the firewalls section. Here we have declared a firewall named admin. This defines the Kunstmaan's administration area. All the settings here are the same as before except that now they are under a different firewall. This is needed to separate the login/logout behaviour for backend and frontend. By this addition everything works as before in the backend, all the Kunstmaan administration area functionalities remain unchanged. The ```main``` section is new here, the provider must be the bundle's one defined in the ```providers``` section.

The ```access_control``` section is where you specify the credentials necessary for users trying to access specific parts of your application. The first four entries are new here, they are used to control the protected area access of the frontend. Again, the patterns are only examples here, they can vary to suit the application logic. 


*_Single language site differences_*


As Kunstmaan defines different routes in the case of single language sites, some changes need to be made in security.yml for this case. The routes for single language sites do not contain the locale so the patterns for the ```firewall``` and ```access control``` section have to be adjusted:

``` yml

# app/config/security.yml

security:

    ...
    
    firewalls:
        admin:
            pattern:            ^/admin
    
    ...        

    access_control:
        - { path: ^/members/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/members/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/members/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/members, role: ROLE_MEMBER }
        - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin, role: ROLE_ADMIN }


```

*Step5: Configure the bundle*

After configuring security.yml the next step is to configure the bundle to work with the specific needs of your application.

Add the following configuration to your config.yml file:

``` yml

# app/config/config.yml

hgabka_kunstmaan_frontend_user:
    user_class: Acme\PublicBundle\Entity\AcmeFrontendUser
    firewall_name: main
    
```

The class name here is the fully qualified class name of the User entity you created in Step3. The name of the firewall is the one that you set in security.yml
 
 
*Step6: Import the bundle routing files*

Now that you have activated and configured the bundle, all that is left to do is import the bundle routing files. It is important to note that to function correctly you not only have to import the bundle's routing.yml but also have to remove the import of the KunstmaanAdminBundle routing from the configuration file.

So first import the bundle routing yml by editing the routing.yml of the application:

``` yml

# app/config/routing.yml

HgabkaKunstmaanFrontendUserBundle:
    resource: "@HgabkaKunstmaanFrontendUserBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/members/
    requirements:
        _locale: "%requiredlocales%"

    
```
Here the 'members' part of the prefix corresponds to the ```access_control``` entries of security.yml. Again you are free to change it, for example you can omit or change the 'members' part from the prefix and the access_control patterns.
And as mentioned delete the KunstmaanAdminBundle key from this file.

As for security.yml the configuration slightly differs in the case of single language sites as for them there is no 'locale' part in the url:

``` yml

# app/config/routing.yml

HgabkaKunstmaanFrontendUserBundle:
    resource: "@HgabkaKunstmaanFrontendUserBundle/Resources/config/routing.yml"
    prefix:   /members/
   
```

*Step7: Update your database schema*:

Now that the bundle is configured, the last thing you need to do is update your database schema because you have added a new entity, the AcmeFrontendUser class which you created in Step 4.

``` bash
$ php bin/console doctrine:schema:update --force
```

Or if you use migrations:

``` bash
$ php bin/console doctrine:migrations:diff && php bin/console doctrine:migrations:migrate
```
Controllers:
============

The bundle provides three basic functionality:
- registration (with or without confirmation)
- profile editing
- password reset

The form types that are used in the controllers are customizable as well as the templates of the emails that are sent from them (see Configuration reference).


Configuration reference:
=======================

``` yml

# app/config/config.yml

hgabka_kunstmaan_frontend_user:
    user_class: Acme\PublicBundle\Entity\AcmeFrontendUser  # Required
    firewall_name: main                                    # Required
    email:                                                 # sender of bundle emails, can be overriden by TwigSwiftMailer::setFromEmail
        address: test@test.hu
        sender_name: Test
    registration:
        form_type: Acme\PublicBundle\Form\RegistrationFormType
        confirmation:
            enabled: true
            email_template: AcmePublicBundle:Registration:email.txt.twig
    resetting:
        form_type: Acme\PublicBundle\Form\ResettingFormType
        email_template: AcmePublicBundle:Resetting:email.txt.twig
    profile:
        form_type: Acme\PublicBundle\Form\ProfileFormType
    

   
```

Notes:
======

As the bundle is defined as the child bundle of KunstmaanAdminBundle be careful not to accidentally override the Kunstmaan templates. To avoid this all the template files of the bundle has the 'frontend_' prefix. Of course you can override them in the usual way.

As mentioned in the routing section the import of the KunstmaanAdminBundle routing.yml file must be removed from the application routing. And of course the routing.yml of this bundle must be imported instead.

