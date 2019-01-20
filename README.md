# Kontest

Easy and ready to use contest website. Install and let's get the game on !

  - Author: Talos
  - Contact: Twitter @Talos51
  - Website: https://www.private-ts.tk

### Version
1.0

## Tech

### Installation

Kontest only require a valid *LAMP* ( or equivalent ) installation. Just install or use pre-existing Apache, PHP and MySQL and you're ready to go.

#### Database
A dump example file is provided "AS IS" in ***docs/SQL*** to help you start immediately your app. If you want to create manually your database please refer to the dump example.

#### Recaptacha

In order to use registration system you'll need to create and retrieve Recaptcha tokens at https://www.google.com/recaptcha

Then just copy them at sign-up.php lines 14 and 15 :
~~~php
14. $captcha_secret="PUT_YOUR_TOKEN_SECRET_HERE";
15. $captcha_datakey="PUT_YOUR_DATAKEY_HERE";
~~~

### Admin account

If you choose to use provided MySQL dump ( see Database section for more information ) a default admin account is created with following credentials :

```
Login : admin
Password : demo1234
```

Otherwise you need to register on Kontest and manually change your role to Administrator (100) database-side.

### Translation

As I'm French the default language of Kontest is indeed in French. Feel free to translate it simply by updating language.php variables :

~~~php
// language.php
$_SITE_TITLE_HOME = "Accueil"; // EN = Home
~~~

## Development

Want to contribute? Great just fork the project and submit a request !

## Todos

 - Nothing ATM
