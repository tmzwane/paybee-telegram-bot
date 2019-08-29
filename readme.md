# PROJECT OVERVIEW

This is a laravel project integrated with a [Telegram](https://www.telegram.org/) bot which accesses [Coindesk API](http://www.coindesk.com/api/) to get data about Bitcoin market prices and outputs this data on request to a telegram channel. Telegram is a non-profit cloud-based instant messaging service that has apps running in multiple operating systems and offers a great security.

* Read [Telegram API](https://core.telegram.org/api) for creating a bot
* Frontend site endpoint [base_url]/bot-config is restricted to configure the bot

## The bot

Try the [paybeetelbot](https://t.me/paybeetelbot) on Telegram

The bot only offers two default commands, then the rest of the commands can be enabled by the user on their  PayBee profile.

The first default command fetches the current BTC rate from [Coindesk](http://www.coindesk.com/api/) and display the BTC equivalent for a given amount to the user in the chat.

The currency is chosen by the user.

Example call:

    /getBTCEquivalent 30 USD

Example response:

    30 USD is 0.08 BTC (760.45 USD - 1 BTC)

If no currency is given the default currency should be used.

The second command should return the paybee user_id of the user.

This requires some sort of linking process between the paybee-user and the telegram-user when the bot is initially started.

Example call:

    /getUserID

Example response:

    2


## The frontend component

The url

    bot-config

Is a restricted site which lets the logged in user define the following things:

* Default currency of the bot

* The rest of the bot settings settable by the user:
	<br>[ ] Configure start method to get a special greeting when calling the start command. 
	<br>[ ] Configure menu method to find all of the available commands. 
	<br>[ ] Configure getGlobal method to view all the exchange rates available against BTC.

* Also this site should show if a telegram account is already linked to the user account.

* If yes, then show the telegram-id or phone number of the user.

* If not, then show the user what's  needed to link his paybee account to his telegram account.


## General

The app uses laravel's default authentication to restrict the site.

Run migrations to create database tables.

Bootstrap used for the frontend site.


# SETTING UP THE PROJECT VIA GIT CLONE ON A LINUX VPS

## Set up the VPS using Apache as the server

SSH into your VPS, examples below are from a VPS running Ubuntu 18.04 Bionic
Run the following commands:
	
	sudo apt update
	sudo apt upgrade

Change directory to Apache and copy the default vhost config to the one of your web app URL:

	cd etc/apache2/sites-available/
	cp 000-default.conf paybeetelbot.co.za.conf

Enable the vhost config and restart apache:

	sudo a2ensite paybeetelbot.co.za
	systemctl reload apache2

Edit the hosts file to add your web app URL:

	nano /etc/hosts
	xxx.xxx.xxx.xxx www.paybeetelbot.co.za


Install certbot for SSL from Lets Encrypt:

	sudo apt-get update
    sudo apt-get install software-properties-common
    sudo add-apt-repository universe
    sudo add-apt-repository ppa:certbot/certbot
    sudo apt-get install certbot python-certbot-apache
    sudo certbot --apache

Install NodeJS

	sudo apt update
	sudo apt-get install curl
	curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash -
	sudo apt-get install nodejs

Install Composer

	sudo apt update
	sudo apt install curl php-cli php-mbstring git unzip
	cd ~
	curl -sS https://getcomposer.org/installer -o composer-setup.php
	HASH="$(wget -q -O - https://composer.github.io/installer.sig)"
	php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer


## Housekeeping

Clone the project, on your terminal type the command:
	
	git clone https://github.com/tmzwane/paybee-telegram-bot.git

Create the .env file, on your terminal type the command:

	cp env_example .env

Edit the .env to set options:

	nano .env

	```
	1. TELEGRAM_BOT_TOKEN={Your Bot Token}
	2. APP_URL={Your App URL}
	... The rest is database configs according to your database settings
	```

I've used mysql for this setup, now to setup the database:

	sudo mysql

	create database homestead;

	CREATE USER 'homestead'@'localhost' IDENTIFIED BY '@@@ViVa2019';

	GRANT ALL PRIVILEGES ON homestead . * TO 'homestead'@'localhost';

	FLUSH PRIVILEGES;

	sudo apt-get install php7.2-mysql

## Main Course

Install updates using composer for all of the packages and dependencies at once, type in the following command in the terminal:

	composer update

Same thing for node packages:

	npm install

Now compile everything:

	npm run dev

Run migrate to create the databases:

	php artisan migrate

Set the webhook for the Telegram Bot:

	https://paybeetelbot.co.za/set-hook

If the hook already exists and pointing to wrong URL then unset it and set it with the correct URL:
	
	https://paybeetelbot.co.za/remove-hook
