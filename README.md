# Synology Chat Driver for Laravel

This is intended to add support for synology chat on logging and other messages.

## Installation

### 1. Add another driver into config/logging.php

e.g.

```php
'synochat' => [
    'driver' => 'custom',
    'url' => env( "SYNOCHAT_LOG_URL","example.ip.or.domain.name"),
    'token' => env("SYNOCHAT_LOG_TOKEN", "%22yourkey%22"),
    'level' => \Monolog\Logger::DEBUG,
    'via' => SynologyChatLogger::class
],
```

### 2. Usage

Following the step above you can then call the following:

```php
use Illuminate\Support\Facades\Log;

Log::channel("synochat")->info("This will be sent to synology chat.");
```

## Known issues

- We implemented a cached flag that prevents blocking your application's IP Address due to failed login attempts.
- There is a api rate limit / throttling implemented in the chat API. It's not possible to send too many messages at once. If you're logging many things make sure not to lose te messages somehow. Maybe you can help me implementing a fallback. 

## Roadmap

- This plugin should eventually work as the slack driver does.
- No support, yet for files.

## Resources

We use this documentation for implementation.

There are also some lines here how to find the **token** etc.

https://www.synology.com/en-us/knowledgebase/DSM/tutorial/Collaboration/How_to_configure_webhooks_and_slash_commands_in_Chat_Integration
