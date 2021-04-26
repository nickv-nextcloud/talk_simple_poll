# Simple chat polls for Nextcloud Talk

## Usage

### Start a poll

```
/poll Do you like polls
Yes
No
```

**Output:**
```
Poll: Do you like polls?
0 votes have been casted

1. Yes
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ 0% (Votes: 0)
2. No
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ 0% (Votes: 0)
```

### Vote on a poll or change your mind

```
/vote 1
```

**Output:**
```
Poll: Do you like polls?
1 votes have been casted

/vote 1 - Yes
/vote 2 - No
```

```
/vote 2
```

**Output:**
```
Poll: Do you like polls?
1 votes have been casted

/vote 1 - Yes
/vote 2 - No
```

### Close the poll

```
/poll close
```

**Output:**
```
Poll: Do you like polls?
100 votes have been casted

1. Yes
████████████████████████████████████████████████░░ 96% (Votes: 96)
2. No
██░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ 4% (Votes: 4)
```

### See the last poll again

```
/poll show
```

**Output:**
```
Poll: Do you like polls?
100 votes have been casted

1. Yes
████████████████████████████████████████████████░░ 96% (Votes: 96)
2. No
██░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ 4% (Votes: 4)
```

## Installation

1. Install the app
2. Setup the commands (you need to use your full path to occ, also don't use `sudo -u www-data` inside the command, because it is automatically run as www-data):  
  
    Normal installation:
    ```
    sudo -u www-data php /var/www/nextcloud/occ talk:command:add poll Poll 'php /var/www/nextcloud/occ talk:poll {ROOM} {USER} {ARGUMENTS}' 2 3
    sudo -u www-data php /var/www/nextcloud/occ talk:command:add vote Poll 'php /var/www/nextcloud/occ talk:poll:vote {ROOM} {USER} {ARGUMENTS}' 2 3
    ```
    Linuxserver.io Docker (edit docker name if necessary):
    ```
    docker exec -it nextcloud sudo -u abc php7 /config/www/nextcloud/occ talk:command:add poll Poll 'php7 /config/www/nextcloud/occ talk:poll {ROOM} {USER} {ARGUMENTS}' 2 3
    docker exec -it nextcloud sudo -u abc php7 /config/www/nextcloud/occ talk:command:add vote Poll 'php7 /config/www/nextcloud/occ talk:poll:vote {ROOM} {USER} {ARGUMENTS}' 2 3
    ```
