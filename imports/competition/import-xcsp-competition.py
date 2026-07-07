# comes with file provided by Christophe Lecoutre, cf mail 24/06/2022
import json
filename = "/tmp/stats/statsCOP"
prefix= "parallelcop"
competition_id = 6
isCOP = True
f = open(filename)

lines = f.readlines()
for d in lines :
    tmp = d.split(" ")
    nbvar = tmp[1].split("=")[1]
    nbc   = tmp[2].split("=")[1]
    file = tmp[0].split("/")[6].split(".")[0]
    if isCOP :
        m = tmp[-2].split("'")[1].capitalize()
        type = tmp[-1].split("'")[0].lower()
        print(f"INSERT INTO benchmarks_cop values(NULL,'{file}','{prefix}/{file}',{competition_id},{nbvar},{nbc},NULL,0,'{m} {type}',NULL,NULL);")
    else :
        print(f"INSERT INTO benchmarks values(NULL,'{file}','{prefix}/{file}',{competition_id},'UNKNOWN',{nbvar},{nbc},NULL,NULL);")

f.close()
