#!/usr/bin/python
#! -*- coding: utf8 -*-

import gc
import json    
import lorun
import socket

os      =   __import__('os')
sys     =   __import__('sys')

if __name__ == '__main__':
    if len(sys.argv) != 2:
        print 'No socket file !'
        sys.exit(2)
    sock = sys.argv[1]
    print 'Daemon listen on socket %s' % sock
    del sys
    server = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
    if os.path.exists(sock):
        os.unlink(sock)
    del os
    server.bind(sock)
    server.listen(0)
    del sock
    del socket
    gc.collect()
    while True:
        connection, addr = server.accept()
        print 'New task ...'
        del addr
        runcfg = json.loads(connection.recv(4096))
        if 'fd_in' in runcfg:
            fd_in = file(runcfg['fd_in'])
            runcfg['fd_in'] = fd_in.fileno()
        if 'fd_out' in runcfg:
            fd_out = file(runcfg['fd_out'], 'w')
            runcfg['fd_out'] = fd_out.fileno()
        if 'fd_err' in runcfg:
            fd_err = file(runcfg['fd_err'], 'w')
            runcfg['fd_err'] = fd_err.fileno()
        gc.collect()
        ret = lorun.run(runcfg)
        del runcfg
        fd_err.close()
        fd_out.close()
        fd_in.close()
        del fd_in
        del fd_out
        del fd_err
        connection.send(json.dumps(ret))
        connection.close()
        del connection
        gc.collect()
