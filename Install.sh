#!/bin/bash

install()
{
	mkdir -p /home/AJC/rumtime
	cp -rvf data /home/AJC/
	cp -rvf ajcserver /home/AJC/
	cp -rvf libs /home/AJC/
	cp -rvf runtime /home/AJC/
	cp -rvf vj_root/* /var/www/html/alxwvj/
	chmod -R 777 /home/AJC
}

help()
{
		echo 'AJC (Alxwvj judge core) module for Alxwvj'
		echo 'Create by SpringHack'
		echo 'https://github.com/springhack/alxwvj_judge_core.git'
		echo 'All data are located at /home/AJC'
		echo '==> "sudo ./Install.sh install" to install'
		echo '==> Before run you have to setup lo-runner'
		echo '==> Edit "Config.Daemon.php", add "AJC" at "OJ_LIST", add prefix "" as AJC prefix !!!'
		echo '==> "cd /home/AJC/ajcserver && ./start.sh" to run'
}

$1
