install()
{
	cp -rvf data /var/www/html/
	cp -rvf libs /var/www/html/
	cp -rvf rumtime /var/www/html/
	cp -rvf vj_root/* /var/www/html/alxwvj/
}

help()
{
		echo 'AJC (Alxwvj judge core) module for Alxwvj'
		echo 'Create by SpringHack'
		echo 'https://github.com/springhack/alxwvj_judge_core.git'
		echo '==> "install.sh install" to install'
}

$1
