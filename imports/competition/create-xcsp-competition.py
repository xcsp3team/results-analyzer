#  come with json file provided by Chris,
# see mail 5/2/23

import json
with open('features2024/minicop24.json', 'r') as f:
    data = json.load(f)

isCOP = True
c_id = 4 # create the competition before...
#/home/cril/audemard/benchs/

for instance in data:
    fullname = instance['instance']
    name = fullname.split('/')[-1].split(".")[0]
    family = name.split('-')[0]
    nbvar = instance['n']
    nbc = instance['e']
    domains = instance['domainSizes']
    nDomainTypes = len(domains)
    degrees = instance['variableDegrees']
    useless = 0 if degrees[0]['degree'] > 0 else degrees[0]['count']
    globals = instance['globalConstraints']
    if isCOP:
        type = instance['objectiveType']

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
# Normal
        #print(f"INSERT INTO benchmarks_cop VALUES(NULL, '{name}', '{fullname}', '{family}', '{c_id}', '{nbvar}', '{nbc}',NULL, 0, '{d}', '{constraints}','{useless}', '{type}',  NULL, NULL );")
# My backend
        print(f"INSERT INTO benchmarks_cop VALUES(NULL, '{name}', '{fullname}', '{family}', '{c_id}', '{nbvar}', '{nbc}',NULL, 0,  '{d}', '{constraints}', '{useless}', '{type}'  ,NULL, NULL );")
    else:
        print(f"INSERT INTO benchmarks VALUES(NULL, '{name}', '{fullname}', '{family}',  '{c_id}', 'UNKNOWN', '{nbvar}', '{nbc}', '{d}', '{constraints}', '{useless}', NULL, NULL);")




































