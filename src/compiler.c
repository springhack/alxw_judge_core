#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/wait.h>
#include <sys/signal.h>
#include <ctype.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/user.h>
#include <sys/syscall.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <sys/ptrace.h>
#include <time.h>
#include <stdarg.h>
#include <fcntl.h>

#define STD_MB 1048576

int main(int argc, char *argv[])
{
    int java = atoi(argv[1]);
    struct rlimit LIM;
    char *args[20];
    int i;
    for (i=2;i<argc;++i)
        args[i - 2] = argv[i];
    args[argc - 2] = (char *)NULL;
    LIM.rlim_max = 60;
    LIM.rlim_cur = 60;
    setrlimit(RLIMIT_CPU, &LIM);
    alarm(60);
    LIM.rlim_max = 10 * STD_MB;
    LIM.rlim_cur = 10 * STD_MB;
    setrlimit(RLIMIT_FSIZE, &LIM);
    if (java)
    {
        LIM.rlim_max = STD_MB<<11;
        LIM.rlim_cur = STD_MB<<11;	
    } else {
        LIM.rlim_max = STD_MB*256 ;
        LIM.rlim_cur = STD_MB*256 ;
    }
    setrlimit(RLIMIT_AS, &LIM);
    execvp(args[0], (char * const *)args);
    fprintf(stderr, "%s\n", "Error while run compiler !");
    return 1;
}
