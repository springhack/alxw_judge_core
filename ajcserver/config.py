#!/usr/bin/env python
#coding=utf-8
#开启评测线程数目
count_thread = 4
#评测程序队列容量
queue_size = 4
#数据库地址
db_host = "127.0.0.1"
#数据库用户名
db_user = "root"
#数据库密码
db_password = "sksks"
#数据库名字
db_name = "build_vj"
#数据库编码
db_charset = "utf8"
#work评判目录
work_dir = "/home/AJC/runtime"
#data测试数据目录
data_dir = "/home/AJC/data"
#自动清理评work目录
auto_clean = True
#编译超时时间
compile_timeout = 5


'''syscall白名单，需要根据具体系统重写!!!'''
white_list = [0,1,2,3,4,5,6,9,10,11,12,21,33,45,59,85,91,122,125,158,192,197,231,243,252]
