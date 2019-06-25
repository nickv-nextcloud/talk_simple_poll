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
2. Setup the commands (you need to use your full path to occ):
    ```
    sudo -u www-data occ talk:command:add poll Poll '/var/www/nextcloud/occ talk:poll {ROOM} {USER} "{ARGUMENTS_DOUBLEQUOTE_ESCAPED}"' 2 3
    sudo -u www-data occ talk:command:add vote Poll '/var/www/nextcloud/occ talk:poll:vote {ROOM} {USER} "{ARGUMENTS_DOUBLEQUOTE_ESCAPED}"' 2 3
    ```
