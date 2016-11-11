#!/usr/bin/env python
# coding=utf-8

import os
import config
import sys
import shutil
import subprocess
import codecs
import logging
import socket
import shlex
import time
import json
import threading
import traceback
import signal
import MySQLdb
from db import run_sql
from Queue import Queue
import multiprocessing






'''Socket List'''
socks = []
for i in range(config.count_thread):
    socks.append('/home/AJC/runtime/%d.sock' % i)

'''任务队列'''
q = multiprocessing.JoinableQueue(config.queue_size)





'''初始化权限检测'''
if 0 != int(os.popen("id -u").read()):
    logging.error("please run this program as root!")
    sys.exit(-1)


'''工具类函数'''
def low_level():
    pass

def kill_proc(p):
    try:
        p.kill()
        print 'After few second(s) so we kill thr compiler ...'
    except:
        pass

'''任务执行相关'''
def run(problem_id, solution_id, language, data_count, user_id, sock):
    time_limit, mem_limit = get_problem_limit(problem_id)
    program_info = {
        "solution_id": solution_id,
        "problem_id": problem_id,
        "take_time": 0,
        "take_memory": 0,
        "user_id": user_id,
        "result": 0,
    }
    compile_result = compileCode(solution_id, language)
    if compile_result is False:
        program_info['result'] = config.result_code["Compile Error"]
        return program_info
    if data_count == 0:
        program_info['result'] = config.result_code["System Error"]
        return program_info
    result = judge(solution_id, problem_id, data_count, time_limit, mem_limit, program_info, language, sock)
    logging.debug(result)
    return result

def judge(solution_id, problem_id, data_count, time_limit, mem_limit, program_info, language, sock):
    max_mem = 0
    max_time = 0
    time_limit = int(time_limit)
    mem_limit = int(mem_limit)
    if language.lower() in ["java",]:
        time_limit = time_limit * 2
        mem_limit = mem_limit * 2
    for i in range(data_count):
        ret = judge_one_mem_time(solution_id, problem_id, i + 1, int(time_limit) + 10, int(mem_limit), language, sock)
        if ret == False:
            continue
        if ret['result'] == 5:
            program_info['result'] = config.result_code["Runtime Error"]
            return program_info
        elif ret['result'] == 2:
            program_info['result'] = config.result_code["Time Limit Exceeded"]
            program_info['take_time'] = time_limit + 10
            return program_info
        elif ret['result'] == 3:
            program_info['result'] = config.result_code["Memory Limit Exceeded"]
            program_info['take_memory'] = mem_limit
            return program_info
        if max_time < ret["timeused"]:
            max_time = ret['timeused']
        if max_mem < ret['memoryused']:
            max_mem = ret['memoryused']
        result = judge_result(problem_id, solution_id, i + 1)
        if result == False:
            continue
        if result == "Wrong Answer" or result == "Output Limit Exceeded":
            program_info['result'] = config.result_code[result]
            break
        elif result == 'Presentation Error':
            program_info['result'] = config.result_code[result]
        elif result == 'Accepted':
            if program_info['result'] != 'Presentation Error':
                program_info['result'] = config.result_code[result]
        else:
            logging.error("judge did not get result")
    program_info['take_time'] = max_time
    program_info['take_memory'] = max_mem
    return program_info

def judge_one_mem_time(solution_id, problem_id, data_num, time_limit, mem_limit, language, sock):
    language = language.lower()
    if language == 'java':
        cmd = '/usr/bin/java -Xms%dM -Xmx%dM -Djava.security.manager -Djava.security.policy=/home/AJC/ajcserver/java.policy -cp %s Main' % (int(mem_limit/1024), int(mem_limit/1024), os.path.join(config.work_dir, str(solution_id)))
        main_exe = shlex.split(cmd)
    else:
        main_exe = [os.path.join(config.work_dir, str(solution_id), 'main'), ]
    input_path = os.path.join(config.data_dir, str(problem_id), 'data%s.in' % data_num)
    output_path = os.path.join(config.work_dir, str(solution_id), 'out%s.txt' % data_num)
    error_path = os.path.join(config.work_dir, str(solution_id), 'err%s.txt' % data_num)
    file(output_path, 'w').close()
    file(error_path, 'w').close()
    if language == 'java':
        runcfg = {
            'args': main_exe,
            'fd_in': input_path,
            'fd_out': output_path,
            'fd_err': error_path,
            'timelimit': time_limit,
            'memorylimit': mem_limit,
            'java' : True
        }
    else:
        runcfg = {
            'args': main_exe,
            'fd_in': input_path,
            'fd_out': output_path,
            'fd_err': error_path,
            'timelimit': time_limit,
            'memorylimit': mem_limit,
            'java' : False,
            'trace': True,
            'calls': config.white_list,
            'files': {}
        }
    rst = SendAndTest(sock, runcfg)
    rst['memoryused'], rst['result'] = fix_java_mis_judge(error_path, rst['result'], rst['memoryused'], mem_limit)
    logging.debug(rst)
    return rst

def SendAndTest(sock, runcfg):
    try:
        client = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
        client.connect(sock)
        client.send(json.dumps(runcfg))
        ret = json.loads(client.recv(1024))
        client.close()
    except:
        return {'result':11, 'memoryused':0,'timeused':0}
        commit_socket_error(sock)
    return ret

def compileCode(solution_id, language):
    language = language.lower()
    dir_work = os.path.join(config.work_dir, str(solution_id))
    if language not in config.build_cmd.keys():
        return False
    if language == 'java':
        flag = 1
    else:
        flag = 0
    p = subprocess.Popen("/home/AJC/ajcserver/compiler %d %s" % (flag, config.build_cmd[language]), shell=True, cwd=dir_work, stdout=subprocess.PIPE, stderr=subprocess.PIPE, close_fds=True)
    out, err = p.communicate()
    if p.returncode == 0:
        return True
    update_compile_info(solution_id, err + out)
    return False


'''结果处理相关'''
def fix_java_mis_judge(stderr, res_now, mem_now, mem_limit):
    err_str = ''
    result = res_now
    memory = mem_now
    try:
        with open(stderr, 'r') as fp:
            err_str = fp.read()
            fp.close()
    except:
        return memory, result
    if 'Exception' in err_str:
        result = 5
    if 'java.lang.OutOfMemoryError' in err_str:
        result = 3
        memory = mem_limit + 10
    if 'Could not create' in err_str:
        result = 5
    return memory, result

def judge_result(problem_id, solution_id, data_num):
    logging.debug("Judging result")
    correct_result = os.path.join(config.data_dir, str(problem_id), 'data%s.out' % data_num)
    user_result = os.path.join(config.work_dir, str(solution_id), 'out%s.txt' % data_num)
    try:
        correct = file(correct_result).read().replace('\r', '').rstrip()
        user = file(user_result).read().replace('\r', '').rstrip()
    except:
        return False
    if correct == user:
        return "Accepted"
    if correct.split() == user.split():
        return "Presentation Error"
    if correct in user:
        return "Output Limit Exceeded"
    return "Wrong Answer"

'''任务收尾工作'''
def update_solution_status(solution_id, status='Waiting'):
    sql = "update Record set `rid`='%s',`result`='Waiting' where `id`='%s'" % (solution_id, solution_id)
    run_sql(sql)


def update_compile_info(solution_id, result):
    sql = "update Record set `compileinfo`='%s' where `id`='%s'" % (MySQLdb.escape_string(result), solution_id)
    run_sql(sql)

def update_result(result, user, problem):
    sql = "update Record set `result`='%s',`long`='%dMS',`memory`='%dK' where `id`='%s'" % (config.re_result_code[result['result']], result['take_time'], result['take_memory'], result['solution_id'])
    run_sql(sql)
    if result['result']== 1:
        t_res = run_sql("select distinct oid from Record where `user`='%s' and result='Accepted'" % user)
        t_res = map(lambda ptr:ptr[0], t_res)
        run_sql("update Users set `plist`='%s',`ac`='%d' where `user`='%s'" % (' '.join(t_res), len(t_res), user))
        logging.info('Solved problem list updated')
        run_sql('update AJC_Problem set accepted=accepted+1,submissions=submissions+1 where id=%s' % problem)
    else:
        run_sql('update AJC_Problem set submissions=submissions+1 where id=%s' % problem)

def clean_work_dir(solution_id):
    dir_name = os.path.join(config.work_dir, str(solution_id))
    shutil.rmtree(dir_name)

'''任务详情相关'''
def put_code(solution_id, problem_id, pro_lang, code):
    try:
        work_path = os.path.join(config.work_dir, str(solution_id))
        os.mkdir(work_path)
    except OSError as e:
        if str(e).find("exist") > 0:
            pass
        else:
            logging.error(e)
            return False
    try:
        real_path = os.path.join(config.work_dir, str(solution_id), config.file_name[pro_lang.lower()])
    except KeyError as e:
        logging.error(e)
        return False
    try:
        f = codecs.open(real_path, 'w', 'utf-8')
        try:
            f.write(code)
        except:
            logging.error("%s not write code to file" % solution_id)
            f.close()
            return False
        f.close()
    except OSError as e:
        logging.error(e)
        return False
    return True

def get_problem_limit(problem_id):
    select_sql = "select `time`,`memory` from `AJC_Problem` where `id`='%s'" % problem_id
    feh = run_sql(select_sql)
    if feh is not None:
        try:
            time_t,memory_t  = feh[0]
        except:
            logging.error("1 cannot get code of runid %s" % solution_id)
            return False
    return time_t,memory_t

def get_data_count(problem_id):
    full_path = os.path.join(config.data_dir, str(problem_id))
    try:
        files = os.listdir(full_path)
    except OSError as e:
        logging.error(e)
        return 0
    count = 0
    for item in files:
        if item.endswith(".in") and item.startswith("data"):
            count += 1
    return count


'''任务分发相关'''
def worker(sock):
    logging.info("Work for socket %s" % (sock))
    while True:
        if q.empty() is True:
            logging.info("%s idle" % (multiprocessing.current_process().name))
        task = q.get()
        solution_id = task['solution_id']
        problem_id = task['problem_id']
        language = task['pro_lang']
        user_id = task['user_id']
        data_count = get_data_count(task['problem_id'])
        logging.info("judging %s" % solution_id)
        result = run(problem_id, solution_id, language, data_count, user_id, sock)
        logging.info("%s result %s" % (result['solution_id'], result['result']))
        update_result(result, user_id, problem_id)
        if config.auto_clean:
            clean_work_dir(result['solution_id'])
        q.task_done()

def put_task_into_queue():
    while True:
        q.join()
        data = run_sql("select `id`,`tid`,`user`,`contest`,`lang`,`code` from Record where `rid`='__' and oj='AJC'")
        time.sleep(0.2)
        for i in data:
            solution_id, problem_id, user_id, contest_id, pro_lang, code = i
            put_code(solution_id, problem_id, pro_lang, code)
            task = {
                "solution_id": solution_id,
                "problem_id": problem_id,
                "contest_id": contest_id,
                "user_id": user_id,
                "pro_lang": pro_lang,
            }
            q.put(task)
            update_solution_status(solution_id)
        time.sleep(0.5)

def start_work_thread():
    for i in range(config.count_thread):
        t = multiprocessing.Process(target=worker,args=(socks[i],))
        t.deamon = True
        t.start()

def start_get_task():
    t = multiprocessing.Process(target=put_task_into_queue, name="get_task")
    t.deamon = True
    t.start()

def main():
    logging.basicConfig(level=logging.INFO, format='%(asctime)s --- %(message)s',)
    start_get_task()
    start_work_thread()

if __name__ == '__main__':
    main()
