#!/bin/bash

install()
{
	mkdir -p /home/AJC/runtime
	cp -rvf data /home/AJC/
	cp -rvf ajcserver /home/AJC/
	cp -rvf ajcserver/ajcd /etc/init.d/
	cp -rvf libs /home/AJC/
	cp -rvf vj_root/* /var/www/html/alxwvj/
    cp -rvf src/runner.js /home/AJC/ajcserver/
    gcc src/compiler.c -o /home/AJC/ajcserver/compiler
    git clone https://github.com/springhack/alxw_judge_core_src
    cd alxw_judge_core_src
    make
    cp runner /home/AJC/ajcserver
    cd ..
	chmod -R 777 /home/AJC
}

help()
{
		echo 'AJC (Alxwvj judge core) module for Alxwvj'
		echo 'Create by SpringHack'
		echo 'https://github.com/springhack/alxwvj_judge_core.git'
		echo 'All data are located at /home/AJC'
		echo '==> "sudo ./Install.sh install" to install'
		echo '==> Before run you have to setup lo-runner and install python-mysqldb'
		echo '==> Edit "Config.Daemon.php", add "AJC" at "OJ_LIST", add prefix "" as AJC prefix !!!'
		echo '==> Browse "AJC_ProblemManager.php" as admin to initial system !!!'
		echo '==> Edit "/home/AJC/ajcserver/config.py" change db config !!!'
		echo '==> "cd /home/AJC/ajcserver && ./start.sh" to run'
}

$1
