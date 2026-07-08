import sys
import os
import json
import requests

# import les traces des solveurs lancés avec submit COP/CSP
# add timestamps to all lines


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
    unsupported = 0
    bug = 0
    for line2 in f:
        tmp2 = line2.split()
        line = " ".join(tmp2[1:])
        timestamp = tmp2[0]
        if line.startswith("Bench"):
            bench = line[6:]
            print(bench)
        if line.startswith("s SAT") or line.startswith('\033[92ms SAT'):
            time = get_time(timestamp, _time)
            sat = 1
            status = "SAT"
        if line.startswith("s UNSUP") or line.startswith('\033[92ms UNSUP'):
            unsupported = 1
        if line.startswith("s UNSAT") or line.startswith('\033[92ms UNSAT'):
	        sat = -1
	        status = "UNSAT"
	        time = get_time(timestamp, _time)
    results.append({'name': bench, "status": status, "time": time if sat == 1 or sat == -1  else 10000, 'unsupported': unsupported})

print("nb results=", len(results))
#print(results)

result= {
    "competition" : 16,
    "solver" : 66,
    "trust" : 1, # if 1 we trust results of the solver and update status on bench and best bound. If no trust can be removed
    "results" : results
}

# for each result :
 # name : the name of the benchmark
 # bounds: a json array  {"bound": 10, "time" : 3} for each new bound. The array is empty if no bound found are found
 # time : the time when optimality is proven (not set otherwise)
 # unsupported set to 1 if the solver can not deal with this problem (not set otherwise)
 # bug : set to 1 if the solver is buggy on this problem (not set otherwise)

print(result)
url = "https://my-backend-analyzer.alfweb.net/api"
response = requests.post(url + "/competitions/solvers", json = result)
print(response.status_code)
print(response.json())
