#!/usr/bin/env python
#coding=utf-8

#import platform

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


'''syscall白名单，需要根据具体系统重写!!!
if '32' in platform.architecture()[0]:
    white_list = [3, 4, 5, 6, 8, 11, 13, 33, 45, 85, 91, 122, 125, 140, 192, 197, 243, 252]
if '64' in platform.architecture()[0]:
    white_list = [0, 1, 2, 3, 4, 5, 8, 9, 10, 11, 12, 21, 59, 63, 89, 158, 201, 205, 231, 240, 252]
'''


file_name = {
    "gcc": "main.c",
    "g++": "main.cpp",
    "java": "Main.java"
}

build_cmd = {
    "gcc": "gcc main.c -o main -fno-asm --static -Wall -lm -std=c99 -DONLINE_JUDGE",
    "g++": "g++ main.cpp -o main -fno-asm --static -Wall -lm -std=c++0x -DONLINE_JUDGE",
    "java": "javac -J-Xms32m -J-Xmx256m Main.java -encoding UTF8"
}

re_result_code = {
    0: "Waiting",
    1: "Accepted",
    2: "Time Limit Exceeded",
    3: "Memory Limit Exceeded",
    4: "Wrong Answer",
    5: "Runtime Error",
    6: "Output Limit Exceeded",
    7: "Compile Error",
    8: "Presentation Error",
    11: "System Error",
    12: "Judging",
}

result_code = {
    "Waiting": 0,
    "Accepted": 1,
    "Time Limit Exceeded": 2,
    "Memory Limit Exceeded": 3,
    "Wrong Answer": 4,
    "Runtime Error": 5,
    "Output Limit Exceeded": 6,
    "Compile Error": 7,
    "Presentation Error": 8,
    "System Error": 11,
    "Judging": 12,
}

