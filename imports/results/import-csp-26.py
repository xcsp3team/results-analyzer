import sys
import os
import json
import requests
import csv

# import les traces des solveurs lancés avec submit COP/CSP
# add timestamps to all lines

#CD_ID,CD_SCRIPT,RS_WCTIME,RS_CPUTIME,RS_USERTIME,RS_SYSTEMTIME,RS_CPUUSAGE,RS_MAXVM,RS_TIMEOUT,RS_MEMOUT,SC_instance_name,SC_track,SC_checker,SC_o_lines
solver_id = 44
competition_id = 3
prefix="/minicsp/"
_time = 3 # 3 -> CPU or 2 -> WC
results = []
status = "UNKNOWN"
with open(sys.argv[1]+"results.csv") as csvfile:
    csvreader = csv.reader(csvfile, delimiter=',', quotechar='|')
    next(csvreader)
    for row in csvreader:
        sat = 0
        status = "UNKNOWN"
        unsupported = 0
        time = -1
        bug = 0
        unsupported = 0

        if row[12] == "OK_SATISFIABLE":
            status = "SAT"
            sat = 1
        if row[12] == "CLAIMED_UNSAT":
            status = "UNSAT"
            sat = 1
        if row[12] == "S_UNSUPPORTED":
            unsupported = 1
            status = "UNSUPPORTED"
        time = row[_time]
        bench =  prefix + os.path.basename(row[1]).removesuffix("_c26.sh").removesuffix("-mc26.sh").removesuffix("_mc26.sh")
        print(bench)
        results.append({'name': bench, "status": status, "time": time if sat == 1 else 10000, 'unsupported': unsupported})

result= {
    "competition" : competition_id,
    "solver" : solver_id,
    "trust" : 1, # if 1 we trust results of the solver and update status on bench and best bound. If no trust can be removed
    "results" : results
}


print(result)
url = "https://xcsp26.alfweb.net/api"
response = requests.post(url + "/competitions/solvers", json = result)
print(response.status_code)
print(response.json())
