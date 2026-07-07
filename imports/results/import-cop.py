import sys
import os
import json

path = sys.argv[1]
results = {'results': []}

dirlist = os.listdir(path)
for p in dirlist:
    f = open(path+"/"+p+"/execution.out")
    time = -1
    sat = None
    bounds = []
    unsupported = 0
    bug = 0
    for line in f:
        if line.startswith("Bench"):
            bench = line[6:-1]
        if line.startswith("o"):
            tmp = line.split()
            bounds.append({'bound': int(tmp[1]), 'time': int(float(tmp[2]))})
        if line.startswith('\033[92mo'):
            tmp = line.split()
            bounds.append({'bound': int(tmp[1].split('\x1b')[0]), 'time': int(float(tmp[2]))})	
        if line.startswith("s SAT") or line.startswith('\033[92ms SAT'):
            sat = 1
        if line.startswith("s UNSUP") or line.startswith('\033[92ms UNSUP'):
            unsupported = 1
        if line.startswith("s UNSAT") or line.startswith('\033[92ms UNSAT'):
	        sat = -1
        if line.startswith("s OPT") or line.startswith('\033[92ms OPT'):
            sat = 0
        if line.startswith("c CPU"):
            tmp = line.split()
            time = tmp[4]
    results['results'].extend([{'name': bench, "time": -1 if sat == 1 else time, "bounds": bounds, 'unsupported': unsupported}])
print(results)


