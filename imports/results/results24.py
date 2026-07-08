import requests
import json
url = "http://localhost:8000/api"



# get solver's id
response = requests.get(url + "/solvers")
solvers = {}
for solver in response.json():
    solvers[solver['name']] = solver['id']

# get competitions
competitions = {}
response = requests.get(url + "/competitions")
for competition in response.json():
    competitions[competition['track']] = competition['id']
print(competitions)

#load results
f = open('/tmp/results-240718.json')
data = json.load(f)

tracks = {}
benchs = set()
# extract solvers in tracks
for tmp in data:
    if tmp is None:
        continue
    t = tmp["track"]
    if t not in tracks:
        tracks[t] = set()
    tracks[t].add(tmp["solver"])

for k in tracks:
    print(k, sorted(tracks[k]), "\n")

solver = 'cpmpy_ortools'
wanted_time = "wallclock_time"  # for parallel else : cpu_time
competition = "parallelCOP"
isCOP = True
prefix = "/parallelcop/"
timeout = 10000
allstatus = { "OPTIMUM FOUND" : "OPT", "UNSUPPORTED": "UNKNOWN","SATISFIABLE": "SAT", "UNSATISFIABLE": "UNSAT", "UNKNOWN": "UNKNOWN", "UNKNOWNMemoryError": "UNKNOWN"}

resultscsp = {
    "competition" : 6, # the id of the competition
    "solver" : solvers[solver],     # the id of the solver
    "trust" : 1, # if 1 we trust results of the solver and update status on bench and best bound. If no trust can be removed
    "results" : []
}


for tmp in data:
    if tmp is None or tmp['track'] != competition or tmp['solver'] != solver:
        continue
    if tmp['s_line'] is not None and tmp['s_line']["status"] == "UNSUPPORTED":
        print(tmp)
    name = prefix + (tmp['instance'] if "_c18" not in tmp['instance'] and "_c24" not in tmp['instance'] else tmp['instance'][:-4])
    status = allstatus[tmp['s_line']["status"]] if tmp["s_line"] is not None else "UNKNOWN"
    tt = tmp['s_line'][wanted_time] if status != "UNKNOWN" else timeout

    data = {
    "name": name,
    "status": status,
    "unsupported" : 1 if tmp['s_line'] is not None and tmp['s_line']["status"] == "UNSUPPORTED" else 0,

    }


    if isCOP:
        if tmp['s_line'] is not None and tmp['s_line']["status"] == "OPTIMUM FOUND":
            data['time'] = int(tmp['s_line'][wanted_time])
        data['bounds'] = []
        last = None
        if tmp['o_lines'] is not None:
            for b in tmp['o_lines']:
                if last is None or int(b[wanted_time]) != last:
                    data['bounds'].append({'bound':b['objective_value'], 'time':int(b[wanted_time])})
                    last = int(b[wanted_time])
                else:
                    if last is  not None:
                        data['bounds'][-1] = {'bound':b['objective_value'], 'time':int(b[wanted_time])}

        resultscsp['results'].append(data)
    else:
        data['status'] = status
        data['time'] = time
        resultscsp['results'].append(data)

print('nb results to store:', len(resultscsp["results"]))
response = requests.post(url + "/competitions/solvers", json = resultscsp)
print(response.status_code)
print("insert", response.json(), "results for solver", solver)



