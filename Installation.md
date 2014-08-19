###Installation
=====================================================================
Please change the permissions of “install” and “assets/img/profile” to 0755. Then go to http://example.com/
install/ and run the installation wizard.


###OAuth
=====================================================================
This script has the capability for users to login though Facebook and Twitter. To enable OAuth, you will first
need to create an app at Facebook (https://developers.facebook.com/) and Twitter (https://dev.twitter.com/ ).
Please see below screenshots for the required setup.
Once you have your app codes, you will then need to add them into the configuration file (includes/
configuration/config.php) under the appropriate fields.
Now you will need to edit the AUTHPATH constant to correctly match the auth folders location. For example,
if the auth folder is located in your documents root, you will need to enter:
defined("AUTHPATH") ? null : define("AUTHPATH", “/auth/");
However, if the auth folder is located elsewhere, you will need to edit it accordingly. Here is the auth path for
our demo:
defined("AUTHPATH") ? null : define("AUTHPATH", "/askfoxos/auth/");


###Captchas
=====================================================================
This script contains a number of captchas, however, some require a site code and others require your PHP
version to be greater than 5.3. This script contains a number of checks to make sure you can only display the
captchas which can run inside of your environment.
reCaptcha from google, requires you to enter a private and public key, to obtain these keys, please visit their
website at http://www.google.com/recaptcha. Sign in and create a site. Once you have the required codes.
Please enter them in the following fields, located inside of "includes/configuration/config.php".
defined("RECAPTCHA_PUBLIC") ? null : define("RECAPTCHA_PUBLIC", "HERE");
defined("RECAPTCHA_PRIVATE") ? null : define("RECAPTCHA_PRIVATE", "HERE");

###by
=====================================================================

▒█▀▀█ █▀▀ █▀▀ ▀▀█▀▀ █░░ █▀▀ ▒█▀▀▄ █▀▀ ▀█░█▀ █▀▀ █░░ █▀▀█ █▀▀█ █▀▀ █▀▀█ ▒█▀▀▄ █▀▀ █▀▀ ░░█░░ █░░ █▀▀ ▒█░▒█ █▀▀ ░█▄█░ █▀▀ █░░ █░░█ █░░█ █▀▀ █▄▄▀ ▒█▄▄█ ▀▀▀ ▀▀▀ ░░▀░░ ▀▀▀ ▀▀▀ ▒█▄▄▀ ▀▀▀ ░░▀░░ ▀▀▀ ▀▀▀ ▀▀▀▀ █▀▀▀ ▀▀▀ ▀░▀▀
