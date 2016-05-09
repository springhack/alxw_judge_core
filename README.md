# alxwvj_judge_core

AJC (Alxwvj judge core) module for Alxwvj
Create by SpringHack
https://github.com/springhack/alxwvj_judge_core.git

Base on https://github.com/ma6174/acmjudger

Need to change syscalls table !!!

Import Lo-runner to run this reop

All data are located at /home/AJC
==> "sudo ./Install.sh install" to install
==> Before run you have to setup lo-runner and install python-mysqldb
==> Edit "Config.Daemon.php", add "AJC" at "OJ_LIST", add prefix "" as AJC prefix !!!
==> Edit "/home/AJC/ajcserver/config.py" change db config !!!
==> "cd /home/AJC/ajcserver && ./start.sh" to run
