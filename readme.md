# Your task

You're starting with a new laravel project.
Implement a [Telegram](https://www.telegram.org/) bot which accesses an external api to get data and which outputs this data on request to a telegram channel.

* add telegram bot with the help of [Telegram API](https://core.telegram.org/api)
* create a restricted frontend site to configure the bot

## The bot 

The bot only needs to offer two commands. 

The first command should fetch the current BTC rate from [Coindesk](http://www.coindesk.com/api/) and display the BTC equivalent for a given amount to the user in the chat.

The currency can be chosen by the user.

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
    
should show a restricted site which lets the logged in user define the following things:

* default currency of the bot
* all settings for the bot you think should be settable by the user

Also this site should show if a telegram account is already linked to the user account.

If yes, then show the telegram-id or phone number of the user.

If not, then show the user what's  needed to link his paybee account to his telegram account.



## General
Use the laravel default authentication to setup a restricted site.

Use migrations for creating database tables.

Use bootstrap for the frontend site.

The useage of third party plugins is allowed.

