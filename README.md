What comes in mind when you here about Redis ?
- In memory data store
- Caching

## Installing Redis Server 
------------------------------------

```bash
sudo apt update
sudo apt install redis-server
```

edit :
```bash
    sudo nano /etc/redis/redis.conf
    # change supervised no -> supervised systemd
```

check :
```bash
    sudo systemctl status redis
```

Note: This setting is desirable for many common use cases of Redis. If, however, you prefer to start up Redis manually every time your server boots, you can configure this with the following command:
    sudo systemctl disable redis

Starting CLI -
```bash
redis-cli
127.0.0.1:6379> ping
```
Output: PONG

```bash
127.0.0.1:6379> set test "It's working!"
```
Output: OK
```bash
127.0.0.1:6379> get test
```
Output: "It's working!"
```bash
127.0.0.1:6379> exit
```
Restart
```bash
sudo systemctl restart redis
```

To correct this, open the Redis configuration file for editing:
```bash
sudo nano /etc/redis/redis.conf
```

Locate this line and make sure it is uncommented (remove the # if it exists):
    bind 127.0.0.1 ::1


Then Restart -
```bash
    sudo systemctl restart redis
```

To check that this change has gone into effect, run the following netstat command:
```bash
sudo netstat -lnp | grep redis
```

if by default netstat is not available
```bash
sudo apt install net-tools
```

#### Configuring a Redis Password
    CTRL + W search : requirepass
    # requirepass foobared uncomment and instead of foobared give a secure password
    and save


###### Warning: since Redis is pretty fast an outside user can try up to
###### 150k passwords per second against a good box. This means that you should
###### use a very strong password otherwise it will be very easy to break.

Thus, it’s important that you specify a very strong and very long value as your password. Rather than make up a password yourself, you can use the openssl command to generate a random one, as in the following example. By piping the output of the first command to the second openssl command, as shown here, it will remove any line breaks produced by that the first command:

```bash
openssl rand 60 | openssl base64 -A
```
Output : 9smsdIUXqJW7NCY3C/nESQ1xb4q/TpgsZKBJwFJIfN/GsaKQlzDbw35GQjobvg2MRKRv5JNbUPx8Sqp+

After copying and pasting the output of that command as the new value for requirepass, it should read:
```bash
/etc/redis/redis.conf
requirepass RBOJ9cCNoGCKhlEBwQLHri1g+atWgn4Xn4HwNUbtzoVxAYxkiYBi7aufl4MILv1nxBqR4L6NNzI0X6cE
```

Restart Redis :
```bash
sudo systemctl restart redis.service
```

Start CLI :
```bash
redis-cli
```

The following shows a sequence of commands used to test whether the Redis password works. The first command tries to set a key to a value before authentication:
```bash
set key1 10
```

That won’t work because you didn’t authenticate, so Redis returns an error:

Output
(error) NOAUTH Authentication required.


The next command authenticates with the password specified in the Redis configuration file:
```bash
auth your_redis_password
```

Redis acknowledges:

Output
OK

After that, running the previous command again will succeed:
```bash
set key1 10
```

Output
OK

```bash
get key1
```

```bash
quit
```

###### When run by unauthorized users, such commands can be used to reconfigure, destroy, or otherwise wipe your data. Like the authentication password, renaming or disabling commands is configured in the same SECURITY section of the /etc/redis/redis.conf file.

Some of the commands that are considered dangerous include: FLUSHDB, FLUSHALL, KEYS, PEXPIRE, DEL, CONFIG, SHUTDOWN, BGREWRITEAOF, BGSAVE, SAVE, SPOP, SREM, RENAME, and DEBUG. This is not a comprehensive list, but renaming or disabling all of the commands in that list is a good starting point for enhancing your Redis server’s security.

Whether you should disable or rename a command depends on your specific needs or those of your site. If you know you will never use a command that could be abused, then you may disable it. Otherwise, it might be in your best interest to rename it.

To rename or disable Redis commands, open the configuration file once more:

```bash
sudo nano  /etc/redis/redis.conf
```

Warning: The following steps showing how to disable and rename commands are examples. You should only choose to disable or rename the commands that make sense for you. You can review the full list of commands for yourself and determine how they might be misused at https://redis.io/commands/ .

To disable a command, simply rename it to an empty string (signified by a pair of quotation marks with no characters between them), as shown below:
```bash
. . .
# It is also possible to completely kill a command by renaming it into
# an empty string:
#
rename-command FLUSHDB ""
rename-command FLUSHALL ""
rename-command DEBUG ""
. . .

. . .
# rename-command CONFIG ""
rename-command SHUTDOWN SHUTDOWN_MENOT
rename-command CONFIG ASC12_CONFIG
. . .

```

Save your changes and close the file.

After renaming a command, apply the change by restarting Redis:
```bash
sudo systemctl restart redis.service
```

To test the new command, enter the Redis command line:
```bash
redis-cli
```
Then, authenticate:
```bash
auth your_redis_password
```

Output :
OK

Let’s assume that you renamed the CONFIG command to ASC12_CONFIG, as in the preceding example. First, try using the original CONFIG command. It should fail, because you’ve renamed it:
```bash
config get requirepass
```

127.0.0.1:6379> config get requirepass
(error) ERR unknown command `config`, with args beginning with: `get`, `requirepass`, 

Calling the renamed command, however, will be successful. It is not case-sensitive:
```bash
asc12_config get requirepass
```

Output
1) "requirepass"
2) "your_redis_password"

Finally, you can exit from redis-cli:
```bash
exit
```

Note that if you’re already using the Redis command line and then restart Redis, you’ll need to re-authenticate. Otherwise, you’ll get this error if you type a command:
Output
NOAUTH Authentication required.

Regarding the practice of renaming commands, there’s a cautionary statement at the end of the SECURITY section in /etc/redis/redis.conf which reads:

/etc/redis/redis.conf
. . .
###### Please note that changing the name of commands that are logged into the
###### AOF file or transmitted to replicas may cause problems.
. . .


##  Setting up Redis with laravel
Installing Predis
```bash
composer require predis/predis
```

First see the settings by opening redis-console
```bash
redis-cli
```

### configure your application's Redis settings via the config/database.php or .env. Were are here using .env :
```bash
REDIS_PASSWORD=bysl123456
REDIS_CLIENT=predis

CACHE_DRIVER=redis
# file
```

### moving to demo

In order to move an existing project only for caching purpose -
Then, setting up redis server and changing laravel project CACHE_DRIVER into 'redis' is enough.

If we want to move the entire DB operation to Redis -
We can use Redis key-value store and hasing using composer package 'predis/predis' which is an standalone alternative for PhpRedis PHP extension(complex to install). For this additional configuration required like redis server auth password.

Then, we can use Redis Facade Class to save and retrive data. We can use all of the redis commands through this Facade.

Reference -
Initial knowledge and theory from - youtube.com
Documentation -
https://redis.io/commands/
laravel.com

Demo - https://github.com/ShaonMajumder/redis


### Install package predis -
```bash
composer require predis/predis
```

### .env :
```bash
REDIS_PASSWORD=password
REDIS_CLIENT=predis
```

