# Import XCSP competition using json file provided in the archive.

import json
import sys

if len(sys.argv) != 3:
    print("Usage: python3 import-xcsp-evaluation.py jsonfile [0/1]")
    print("  jsonfile: the file with all information (see archive from XCSP competitions)")
    print("  0: CSP evaluation or 1: COP evaluation")
    exit(1)

with open(sys.argv[1], encoding="utf-8") as f:
    data = json.load(f)

isCOP = sys.argv[2] == "1"

benchmarks = []

for instance in data:
    fullname = instance['instance']
    name = fullname.split('/')[-1].split(".")[0]
    family = name.split('-')[0]
    nb_variables = instance['n']
    nb_constraints = instance['e']
    domains = instance['domainSizes']
    nDomainTypes = len(domains)
    degrees = instance['variableDegrees']
    useless = 0 if degrees[0]['degree'] > 0 else degrees[0]['count']
    globals = instance['globalConstraints']
    if isCOP:
        type = instance['objectiveType']
    else:
        type = None

    nValues = 0
    d1 = 0
    dmin = 0
    ndmin = 0
    for d in domains:
        if 'count' in d:
            if d["size"] == 1:
                d1 = d["count"]
            if d["size"] > 1 and dmin == 0:
                dmin = d["size"]
                ndmin = d["count"]

            nValues += d["size"]*d['count']
    d = f"#types:{nDomainTypes} #values:{nValues} ("
    if d1 != 0 :
        d += f"#1:{d1} "
    d += f"#{dmin}:{ndmin} "
    last = domains[-1]
    if domains[-1]["size"] > dmin:
        if int(nDomainTypes) > 2:
            d = d + "... "
        d += f"#{last['size']}:{last['count']} "
    d = d[0:-1] + ")"
    #print(f"#vars:{nbvar} (#useless:{useless}) #ctrs:{nbc}")
    #print(f"Domains-> {d}")
    constraints = ""
    for c in globals:
        constraints = constraints + f"#{c['type']}:{c['count']}" + " "
    #print(f"Constraints-> {constraints}")


    if isCOP:
        benchmarks.append({
            "name": name,
        	"fullname": fullname,
        	"family": family,
        	"nb_variables": nb_variables,
        	"nb_constraints": nb_constraints,
        	"info_domains": d,
        	"type": type,
        	"info_constraints": constraints,
        	"useless_vars": useless
        })
    else:
        benchmarks.append({
            "name": name,
        	"fullname": fullname,
        	"family": family,
        	"nb_variables": nb_variables,
        	"nb_constraints": nb_constraints,
        	"info_domains": d,
        	"info_constraints": constraints,
        	"useless_vars": useless
        })

results = {"benchmarks" :   benchmarks}
print(json.dumps(results))


































