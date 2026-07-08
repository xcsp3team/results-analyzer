# ./rec.sh | python import.py
import sys
import os
# //{"results" : [{"name": "maincsp/AztecDiamond-025_c22", "status": "sat", "time": 3}, {"name":"maincsp/AztecDiamond-030_c22", "status": "unknown"}]
time = 10000

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
results = {'results': []}

dirlist = os.listdir(path)
for p in dirlist:
    f = open(path+"/"+p+"/execution.out")
    time = 10000
    sat="UNKNOWN"
    bounds = []
    unsupported = 0
    bug = 0
    for line2 in f:
        tmp2 = line2.split()
        line = " ".join(tmp2[1:])
        timestamp = tmp2[0]
        if line.startswith("Bench"):
            bench = line[6:]
        if line.startswith("s SAT") or line.startswith('\033[92ms SAT'):
            sat = "SATISFIABLE"
            time = get_time(timestamp, _time)
        if line.startswith("s UNSUP") or line.startswith('\033[92ms UNSUP'):
            unsupported = 1
        if line.startswith("s UNSAT") or line.startswith('\033[92ms UNSAT'):
	        sat = "UNSATISFIABLE"
	        time = get_time(timestamp, _time)
    results['results'].extend([{'name': bench, "status": sat, "time": time, 'unsupported': unsupported, 'bug': 0}])
print(results)
