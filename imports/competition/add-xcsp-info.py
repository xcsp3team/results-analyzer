# comes with file provided by Christophe Lecoutre, cf mail 24/06/2022
import json
filename = "/tmp/stats/statsMiniCSP"
isCOP = False
f = open(filename)

lines = f.readlines()
for d in lines :
    tmp = d.split(" ")
    #print("\n\n", tmp)
    file = tmp[0].split("/")[6].split(".")[0]
    nbvar = tmp[1].split("=")[1]
    nbc   = tmp[2].split("=")[1]
    domains = json.loads(tmp[4].split("=")[1][1:-1])
    nDomainTypes = len(domains)
    degrees = json.loads(tmp[7].split('=')[1][1:-1])
    useless = 0 if degrees[0]['degree'] > 0 else degrees[0]['count']

    globals = json.loads(tmp[13].split('=')[1][1:-1])
    #nExtension = tmp[15].split("=")[1]
    #nIntension = tmp[14].split("=")[1]

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

    print(f"UPDATE benchmarks set info_domains='{d}', info_constraints='{constraints}', useless_vars={useless} WHERE name='{file}';")
f.close()
