#!/usr/bin/env python
# coding=utf-8

import os
import sys
import config

# Early initial child process
for i in range(config.count_thread):
    pid = os.fork()
    if pid == 0:
        os.execl('/usr/bin/python', 'python', '/home/AJC/ajcserver/runner.py', '/home/AJC/runtime/%d.sock' % i)
        break


# Early initial main process
pid = os.fork()
if pid == 0:
    os.execl('/usr/bin/python', 'python', '/home/AJC/ajcserver/main_runner.py')


# Only wait
while True:
    try:
        res = os.wait()
    except:
        sys.exit(1)
