import sys
import os
import json
import requests

# add timestamps to all lines
# import les traces des solveurs lancés avec submit COP/CSP


def get_time(timestamp, tt):
# tt="wc" or ttt="cpu"
#2329.28/590.70
    tmp = timestamp.split("/")
    if tt == "cpu" :
        return int(float(tmp[0]))
    if tt == "wc" :
        return int(float(tmp[1]))
    assert(False)


_time = "cpu"
path = sys.argv[1]
results = []

dirlist = os.listdir(path)
for p in dirlist:
    f = open(path+"/"+p+"/execution.out")
    time = -1
    status = "UNKNOWN"
    sat = None
    bounds = []
    unsupported = 0
    bug = 0
    for line2 in f:
        tmp2 = line2.split()
        line = " ".join(tmp2[1:])
        if(len(tmp2)) == 0:
            continue
        timestamp = tmp2[0]
        if line.startswith("Bench"):
            bench = line[6:]
        if line.startswith("o "):
            tmp = line.split()
            bounds.append({'bound': int(tmp[1]), 'time': get_time(timestamp, _time)})
        if line.startswith('\033[92mo'):
            tmp = line.split()
            bounds.append({'bound': int(tmp[1].split('\x1b')[0]), 'time': get_time(timestamp, _timeœ)})
        if line.startswith("s SAT") or line.startswith('\033[92ms SAT'):
            status = "SAT"
            sat = 1
        if line.startswith("s UNSUP") or line.startswith('\033[92ms UNSUP'):
            unsupported = 1
        if line.startswith("s UNSAT") or line.startswith('\033[92ms UNSAT'):
	        sat = -1
	        status = "UNSAT"
	        time = get_time(timestamp, _time)
        if line.startswith("s OPT") or line.startswith('\033[92ms OPT'):
            sat = 0
            status = "OPTIMUM"
            time = get_time(timestamp, _time)
    results.append({'name': bench, "time": -1 if sat == 1 else time, "status": status, "bounds": bounds, 'unsupported': unsupported})

export = {
    "results" : results
}


print(json.dumps(export))
