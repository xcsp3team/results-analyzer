import sys
import os
import json
import requests
import csv
import re
#python3 import-cop-26.py ../xcsp26/COP26-fast/ACE/

# import les traces des solveurs lancés avec submit COP/CSP
# add timestamps to all lines

solver_id = 8
competition_id = 6
prefix="/parallelcop/"
_time = 3 # 3 -> CPU or 2 -> WC


#CD_ID,CD_SCRIPT,RS_WCTIME,RS_CPUTIME,RS_USERTIME,RS_SYSTEMTIME,RS_CPUUSAGE,RS_MAXVM,RS_TIMEOUT,RS_MEMOUT,SC_instance_name,SC_track,SC_checker,SC_o_lines
def parse_bounds(line):
    # Extrait tous les triplets (wct, o)
    entries = re.findall(r'wct=([\d.]+):cput=([\d.]+):o=(\d+)', line)
    print(entries)
    entries = [(int(float(w)), int(float(c)), int(o)) for w, c, o in entries]
    if not entries:
        return []

    last_bound_per_second = {}

    for w, c, o in entries:
        t = c if _time == 3 else w # numéro de la seconde (relative au début)
        last_bound_per_second[t] = o  # on garde la dernière borne rencontrée dans cette seconde

    bounds = [{"bound": o, "time": t} for t, o in sorted(last_bound_per_second.items())]
    return bounds




results = []
with open(sys.argv[1]+"results.csv") as csvfile:
    csvreader = csv.reader(csvfile, delimiter=',', quotechar='|')
    csv.field_size_limit(sys.maxsize)
    next(csvreader)
    for row in csvreader:
        sat = None
        status = "UNKNOWN"
        unsupported = 0
        time = -1
        bug = 0
        unsupported = 0
        bounds = parse_bounds(row[13])


        if row[12] == "OK_SATISFIABLE":
            sat = 1
        if row[12] == "CLAIMED_UNSAT":
            sat = -1
            time = row[_time]
        if row[12] == "OK_OPTIMUM_FOUND":
            time = row[_time]
            sat = 0
        if row[12] == "S_UNSUPPORTED":
            unsupported = 1
#        if row[12] == "ERR_UNKNOWN":
#            bug = 1

        bench =  prefix + os.path.basename(row[1]).removesuffix("_c26.sh").removesuffix("-mc26.sh").removesuffix("_mc26.sh")
        results.append({'name': bench, "time": -1 if sat == 1 else time, "bounds": bounds, 'unsupported': unsupported, 'bug': bug})
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
