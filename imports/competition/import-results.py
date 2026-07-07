# json file coming from emmanuel Lonca

import json

filename = "/Users/audemard/Temp/xcsp23/dataframe-cop_parallel.json"
competition_id = 7
fullnameprefix = '/parallelcop'
track = "XP_cop_parallel"
solver = "toulbar2mpi"
isCOP = True

# XCSP_cop_seq ['ACE', 'sat4j-csp-resolution', 'toulbar2', 'lintoulbar2', 'cosoco', 'sat4j-csp-both', 'choco', 'RBO', 'picat', 'mistral']

# // : ['toulbar2mpi', 'choco']
# XP_cop_fast ['ACE', 'cosoco', 'choco', 'picat', 'mistral']
# XCSP mini cop = ['seapearl', 'sat4j-csp-resolution', 'toulbar2', 'lintoulbar2', 'exchequer', 'sat4j-csp-both', 'choco', 'miniRBO', 'mistral']
#XCSP_csp_seq ['ACE', 'fun-scop-glueminisat', 'minicpbp', 'sat4j-csp-resolution', 'cosoco', 'BTD', 'fun-scop-kissat', 'choco', 'sat4j+roundingsat', 'picat', 'mistral']
# XCSP_csp_mini ['seapearl', 'nacre', 'miniBTD', 'sat4j-csp-resolution', 'exchequer', 'choco', 'sat4j+roundingsat', 'mistral']



f = open(filename)
data = json.load(f)
print(data[0])
f.close()

solvers = []
for result in data :
    if result['track'] != track :
        continue
    if result['experiment_ware'] not in solvers:
        solvers.append(result["experiment_ware"])
print(solvers)
print()
print()

results = {'results': []}

for result in data :
#    if result['result'] == 'Unsupported':
#      print(track, " ", result['experiment_ware'], result['input'])
    if result['track'] != track or result['experiment_ware'] != solver:
        continue
    #print(int(result['wallclock_time']))
    #print(result)
    name = result['input']
    if isCOP :
        time = -1 if result['result'] in  ("Satisfiable", "NoStatusLine", "Unknown", 'Unsupported') else result['wallclock_time']
        dict = {}
        unsupported = 1 if result['result'] == "Unsupported" else 0
        if result['o_history'] is not None:
            bounds = result['o_history']
            for b in bounds :
                dict[round(b['wct'])] = b['o']
                #b.pop('wct', None)
                #b['bound'] = b.pop('o')
                #b['time'] = round(b.pop('cput'))
        bounds = [{'bound': dict[t], 'time': t} for t in sorted(dict)]
        bb = json.dumps(bounds)
        #print(f"{fullnameprefix}/{name};{time};{bb};{unsupported}")
        results['results'].extend([{'name': f"{fullnameprefix}/{name}", "time": time, "bounds": bounds, 'unsupported': unsupported}])
        #print(result)

        #print(result['input'], result['result'])
    else :
        status = "SAT" if result['result'] == "Satisfiable" else "UNSAT" if result['result'] == "Unsatisfiable" else "UNSUPPORTED" if result['result'] == "Unsupported" else "UNKNOWN"
        time = 10000 if status == "UNKNOWN" or status == "UNSUPPORTED" else round(result['cpu_time'])
        results['results'].extend([{'name': bench, "time": -1 if sat == 1 else time, "bounds": bounds, 'unsupported': unsupported}])
        #print(f"{fullnameprefix}/{name};{status};{time}")

print(results)
#print(f"solver in {track}", solvers)

# INSERT SOLVERS
#for result in data :
#    if result['experiment_ware'] not in solvers:
#        solvers.append(result["experiment_ware"])
#print(solvers)
#for s in solvers :
#    print(f"INSERT INTO solvers values(NULL,'{s}','1', 'default', '', NULL,NULL);")

#/home/evaluation/evaluation/pub/bench/XCSP18/Tal/Tal-02_c18.xml.lzma 1 [{"bound":106,"time":0.0290289},{"bound":94,"time":0.031498},{"bound":91,"time":0.034081},{"bound":90,"time":0.0363889},{"bound":86,"time":0.178196}]
