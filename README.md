SlimmeMeter
===========
SlimmeMeter is an application to read the P1 port of the dutch SlimmeMeter and shows some graphs in a WebInterface.

Screenshot
----------
![Alt text](/docs/screenshot.png?raw=true "Screenshot")

Requirements
------------
SlimmeMeter requires the following:

* PHP 5.1.x or higher
* PHP rrd extension
* Apache webserver
* Supervisor daemon


Installation
------------

Crontab
```
* * * * * cd /var/www/slimmemeter/current && ./app/console log:export-graph -q
```

Supervisor (/etc/supervisor/conf.d/slimmemeter.conf)
```
[program:fetch_data]
environment=PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
command=/var/www/slimmemeter/current/app/console log:fetch-data -r 86400 -q --env=prod
directory=/var/www/slimmemeter/current
process_name=%(program_name)s_%(process_num)s
numprocs=1
autostart=true
autorestart=true
stopsignal=TERM
stopwaitsecs=10
stdout_logfile=/var/www/slimmemeter/current/app/logs/worker_fetch_data_stdout.log
stdout_logfile_maxbytes=1MB
stderr_logfile=/var/www/slimmemeter/current/app/logs/worker_fetch_data_stderr.log
stderr_logfile_maxbytes=1MB
user=pi
```


TODO
----
* Clean some code
* Write installation manual
* Change name of the project to something better

Licence
-------
SlimmeMeter is MIT licenced.

Support
-------
If you're looking for help, you can reach me by:

*  Twitter: @sndpl ([http://twitter.com/sndpl](http://twitter.com/sndpl))
*  Github: [https://github.com/sndpl/slimmemeter](https://github.com/sndpl/slimmemeter)
