# Demo Application on Symfony Scheduler

This application is a factory simulator. In terms of operations, the factory runs six days a week (Monday through Saturday). 
In addition to Sundays, the factory doesn’t run on Christmas Eve, Christmas, New Year’s Eve, and New Year. 
On the days which the factory runs, it operates on 4 hour shifts starting at midnight with a 2 hour break between each shift.

To keep track of things, the factory requires reports to be generated. In particular, three reports are required:
- A production report which is expected at the end of each day
- An incident report which is expected after every shift
- A compensation report which is expected on the last day of the month

This application shows how to use the [Symfony scheduler](https://symfony.com/doc/current/scheduler.html#symfony-scheduler-basics) to handle the nuances associated with this task. 

System Requirements
------------

* PHP 8.2 or above
* PDO-SQLite PHP extension enabled;
* [Git][2]
* [Composer][3]
* [Symfony CLI][4]
* and the [usual Symfony application requirements][5].


Installation
------------

1. Clone the repository

```bash
 git clone https://github.com/ybjozee/symfony_scheduler_demo.git
 cd symfony_scheduler_demo
```

2. Install dependencies

```bash
 composer install
```

3. Update `DATABASE_URL` as required - by default, SQLite is used

``` ini
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

4. Setup database

```bash
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate -n
symfony console messenger:setup-transports
symfony console doctrine:fixtures:load -n
```

5. Debug schedule

```bash
symfony console debug:schedule
```

6. Consume `async` transport

```bash
symfony console messenger:consume -v async
```

7. Consume `scheduler_tasks` transport

```bash
symfony console messenger:consume -v scheduler_tasks
```

[2]: https://git-scm.com/
[3]: https://getcomposer.org/
[4]: https://symfony.com/download
[5]: https://symfony.com/doc/current/reference/requirements.html
