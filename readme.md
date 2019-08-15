# PROJECT OVERVIEW

This is a laravel project integrated with a [Telegram](https://www.telegram.org/) bot which accesses [Coindesk API](http://www.coindesk.com/api/) to get data about Bitcoin market prices and outputs this data on request to a telegram channel. Telegram is a non-profit cloud-based instant messaging service that has apps running in multiple operating systems and offers a great security.

* Read [Telegram API](https://core.telegram.org/api) for creating a bot
* Frontend site endpoint [base_url]/bot-config is restricted to configure the bot

## The bot

Try the [paybeetelbot](https://t.me/paybeetelbot) on Telegram

The bot only offers two commands default commands, then the rest of the commands can be enabled by the user on thei  PayBee profile.

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

* All settings for the bot you think should be settable by the user:
	[ ] Configure start method to get a special gretting when calling the start command. 
	[ ] Configure menu method to find all of the available commands. 
	[ ] Configure getGlobal method to view all the exchange rates available against BTC.

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


# FUTURE IMPROVEMENTS SUGGESTIONS
References [Luno API]()

[ ] Get other currencies exchange rates against Bitcoin and Ethereum
[ ] Create wallet addresses for the users

# SETTING UP THE PROJECT FROM SCRETCH

* Reference [Creating A Cryptocurrency Telegram Bot API With Laravel](https://tutsforweb.com/creating-a-cryptocurrency-telegram-bot-with-laravel/)

To create a new project, run the following command:

	composer create-project laravel/laravel paybee-telegram-bot

To communicate with Telegram API, I've used [Telegram Bot API PHP SDK](https://github.com/irazasyed/telegram-bot-sdk) wrapper.
To install this package, require it through composer by running the following command:

	composer require irazasyed/telegram-bot-sdk

Publish configuration file for the above package by running the following command:
	
	php artisan vendor:publish --provider="Telegram\Bot\Laravel\TelegramServiceProvider"

The command above will publish a telegram.php configuration file in config directory.

## Creating a Telegram Bot

To create a Telegram Bot, you need to have telegram app installed, and then you have to talk to the BotFather for the creation of Telegram bot. For this, you need to send the message to [@BotFather](https://telegram.me/botfather). Visit this link through mobile and click Send Message to start your talk with BotFather.

Now click start at the bottom. Your communication will start with botfather by sending `/start`. To create a new bot, send `/newbot` to botfather. It will ask for your bot name. I've called mine `paybeetelbot`. BotFather will further ask for bot username. Username must end with the word bot, and I've named it the same. You can call it whatever you want.

After bot is created successfully, BotFather will send a message with a token to access HTTP API.

Copy the bot token and insert it into .env file under the key TELEGRAM_BOT_TOKEN. An entry will be added like this in .env file. Replace the bot token with your bot token in the line below.

## Testing SDK

To test if the SDK is configured correctly, set up a call for the getMe() method. To do this, run the following command to create model, migration, and controller.

	php artisan make:model Telegram -mc

Set up a route for this endpoint. So, add the following entry in `routes/web.php file`:
	
	Route::get('get-me', 'TelegramController@getMe');

Add getMe method in `app/Http/Controllers/TelegramController.php` file:
```php
use Telegram\Bot\Api;
 
class TelegramController extends Controller
{
    protected $telegram;
 
    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }
 
    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }
}
```

SDK is working fine if you get a response like:

	{"id":924412220,"is_bot":true,"first_name":"paybeetelbot","username":"paybeetelbot"}

## Setting Up WebHook

Webhook only works with HTTPS website. 
Create a route and a method to setup the webhook url that will be used by telegram.

Add the following route in your `route/web.php` file:

	Route::get('set-hook', 'TelegramController@setWebHook');

Now in `TelegramController.php` file add a setWebHook method:

```php
public function setWebHook()
{
    $url = 'https://paybeetelbot.co.za/webhook';
    $response = $this->telegram->setWebhook(['url' => $url]);
 
    return $response == true ? redirect()->back() : dd($response);
}
```

Now you need to create a post route with the url that we hooked in the url variable in the setWebHook method. Add the following entry to your routes file:

	Route::post(env('TELEGRAM_BOT_TOKEN') . '/webhook', 'TelegramController@handleRequest');

Laravel includes CSRF protection which will cause exception when using third party webhooks. We need to disable csrf protection for this route. Go to `app\Http\Middleware\VerifyCsrfToken.php` and add the following entry in the `$except` array:

```php
protected $except = ['YOUR_BOT_TOKEN/webhook',];
```
Replace `YOUR_BOT_TOKEN` above with your bot token.

Finally to setup webhook, hit the set-hook endpoint from your domain. 

It will be like `https://paybeetelbot.co.za/set-hook`.

`getUpdates()` method will not work when webhook is setup. You can use `removeWebHook()` method to remove the webhook.
	

## Setting Up front-end Authentication
Execute the following command to start:
	
	php artisan make:auth

After executing the command. Some of the files known as Authentication scaffolding generated into our application, routes have also been updated. `Route::auth()` is a method that cleanly contains all the login and register routes. The following routes will be created with their front-end:

	[base_url]/register
	[base_url]/login
	[base_url]/http://playground/password/reset

## Errors and Fixes (on Linux - Ubuntu)

### Apache
* The requested URL /get-me was not found on this server.
	
	Enable mod_rewrite on the apache server: `sudo a2enmod rewrite`

* Run `sudo nano /etc/apache2/apache2.conf`, changing the "AllowOverride" directive for the /var/www directory (which is my main document root): 
	```
	<Directory "/path/to/your/laravel/project/">
		Allowoverride All
	</Directory>
	```

* Then restart the Apache server: `service apache2 restart`

# UnexpectedValueException
`The stream or file "/project/path/storage/logs/laravel-2019-08-13.log" could not be opened: failed to open stream: Permission denied`
	
	ps aux | egrep '(apache|httpd)'
	sudo chown -R user:www-data storage
	sudo chown -R root:www-data bootstrap/cache
	chmod -R 775 storage
	chmod -R 775 bootstrap/cache


# ABOUT AUTHOR

## Thabang Zwane

[TMZwane.com](https://tmzwane.com) that's my personal website

[Twiter](https://twitter.com/tm_zwane) follow me on twitter, I will follow back.

[Telegram](https://t.me/tmzwane) let's have a chat!

[Instagram](https://www.instagram.com/tmzwane/) I don't post much, but I'm there.