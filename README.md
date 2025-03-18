Symfony project in a group of 3, with tenyega and ArobaseDev

CREATING NEW WEB PROJECT SYMFONY
symfony new gestion_salle --webapp

INSTALLING TAILWIND
composer require symfonycasts/tailwind-bundle
php bin/console tailwind:init
php bin/console tailwind:build --watch

ENTITIES
EVENTTYPE
symfony console make:entity EventType
name varchar 255 not null
description varchar 255 nullable

    ADDRESS
    	symfony console make:entity Address
    	number int not null
    	street varchar 255 not null
    	country varchar120 not null
    	city varchar120 not null
    	codePostal varchar120 not null

    ERGONOMY
    	symfony console make:entity Ergonomy
    	name varchar180 not null
    	description varchar255  nullable

    EQUIPMENT
    	symfony console make:entity Equipment
    	name varchar180 not null
    	description varchar255  nullable
    	type varchar180 not null

    USER
    	symfony console make:user

    NOTIFICATION
    	symfony console make:entity Notification
    	message varchar255 not null
    	createdAt datetime_immutable not null
    	isRead boolean not null
    	userId M21 with User (Notification.userId allowed nullable?: No, OrphanRemoval?: no)


    HALL
    	symfony console make:entity Hall
    	name varchar255 not null
    	area varchar120 not null
    	accessibility varchar255 not null
    	capacityMax integer not null
    	pricePerHour decimal not null (Precision 5, scale 2)
    	openingTime time not null
    	closingTime time not null
      	eventTypeId 121 with EventType(Hall.eventTypeId nullable?: No, eventType->getHall(): yes)
      	addresseId 121 with Address(Hall.addressId nullable?: No)
    	mainImg varchar255 not null


    RESERVATION
    	symfony console make:entity Reservation
    	startDate date not null
    	endDate date not null
    	startTime time not null
    	endTime time not null
    	isConfirmed boolean not null
    	specialRequest varchar 255 nullable
    	userId ManyToOne with User (Reservation.userId nullable?: No, OrphanRemoval? :no)
    	hallId ManyToOne with Hall( Reservation.hallId nullable? : No, OrphanRemoval? : no)
    	createdAt datetime_immutable  not null
    	updatedAt datetime_immutable  not null

.env.local
database name db_hall_management

CREATION BDD
symfony console d:d:c
symfony console make:migration
symfony console d:m:m

FIXTURES
composer require orm-fixtures --dev
composer require fakerphp/faker

LOADED THE FIXTURES
symfony console d:f:l

app_hall_edit
app_hall_show
app_hall_show_all
app_hall_delete

app_ergonomy_edit
app_ergonomy_delete

app_equipment_edit

CREATING SECURITY CONTROLLER AND REGISTRATION FORM
symfony console make:security:form-login
SecurityController with /logout and no phpUnit test

INSTALLED UX ICON FROM SYMFONY
composer require symfony/ux-twig-component
composer require symfony/ux-icon

ADMIN
composer req easycorp/easyadmin-bundle
symfony console make:admin:dashboard
symfony console make:admin:crud

CREATING HOME CONTROLLER

## symfony console make:controller HomeController

CREATING PROFILE CONTROLLER

## symfony console make:controller ProfileController

CREATING HALL CONTROLLER

## symfony console make:controller HallController

CREATING NOTIFICATION CONTROLLER

## symfony console make:controller NotificationController

CREATING RESERVATION CONTROLLER

## symfony console make:controller ReservationController

    symfony console make:registration-form (UniqueEntity validation?: Yes, send Email?: Yes, include userid in verifcation link?: No, email?: hall4all@email.com,name?:  hall4all)
    	 composer require symfonycasts/verify-email-bundle

    To launch the server mailer
    	symfony console messenger:consume async -vv

ADDED NEW ENTITY
symfony console make:entity Images
title varchar 120 not null
img varchar255 not null

symfony console make:entity HallEquipment
hallId M21 with Hall (HallEquipment.hallId nullable?: No, $hall->getHallEquipment()?: yes , orphanRemoval?: Yes)
equipmentId M21 with equipment

symfony console make:entity HallErgonomy
hallId M21 with Hall
ergonomyId M21 with Ergonomy

symfony console make:entity HallImage
hallId M21 with Hall
imgId M21 with Images

PAYMENT WITH STRIPE
	composer require stripe/stripe-php

	add PK and SK of Stripe in your .env

PAYMENT Table
	symfony console make:entity Payment
	reservationId 121 with Reservation (payment.reservationId nullable ?: No, reservation->getPayment()?: Yes)
	type varchar 180 Not null 
	amount decimal Not Null (precision:5, scale:2, nullable?: No)
	paymentStatus varchar 255 Not Null
	createdAt datetime_immutable Not Null
	updatedAt datetime_immutable Not Null
	paymentDate datetime_immutable Not Null

Added PaymentService and HourCalculator  inside src/Service 
and changed in service.yaml (added Stripe api under parameter and different services made under services)
	parameters:
        STRIPE_API_PK: '%env(STRIPE_API_PK)%'
        STRIPE_API_SK: '%env(STRIPE_API_SK)%'
	services:
	    App\Service\HourCalculator: ~
    	App\Service\PaymentService: ~

CHANGE IN USER ENTITY
	symfony console make:entity User 
	fullName varchar 255 not null
	address varchar 255 Nullable
  
  
  ## symfony console make:form reservation

creation twig CRUD with form
index.html.twig
new.html.twig
edit.html.twig
delete.html.twig


CREATED PAYMENT CONTROLLER 
	symfony console make:controller Payment

Reservvation table changed to add totalPrice
	symfony console make:entity Reservation
	totalPrice decimal(Precision: 10; Scale 2) Nullable

Full Calender Library Added with its JS and CSS code under public-CSS and -JS
and a fullCalender.html.twig with the display of the calender and route for the calender is defined under HallController 

 ## Formulaire contact

#### Messenger.yaml 

sync: 'sync://'

routing:
            Symfony\Component\Mailer\Messenger\SendEmailMessage: sync
            Symfony\Component\Notifier\Message\ChatMessage: sync
            Symfony\Component\Notifier\Message\SmsMessage: sync

#### .env

MAILER_DSN=smtp://localhost:1025

Dans src, créer le dossier DTO, le fichier contactDTO.php

Creer make:form
Contact
=> ContactType.php

ContactContoller

Contact/index.html.twig
#### Notice
!!! composer mailer et composer require symfony/twig-bundle sont déjà installé lorsque composer require webapp

#### Search symfony doc and configure mailer

Sending Emails with Mailer https://symfony.com/doc/current/mailer.html

Creating Sending Messages. https://symfony.com/doc/current/mailer.html#creating-sending-messages

HTML Content  :  https://symfony.com/doc/current/mailer.html#html-content

#### install Mailtrip
https://help.mailtrap.io/article/12-getting-started-guide

#### Contact page, email page Twig


