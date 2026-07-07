# json file coming from emmanuel Lonca
# get the data from a solver
import json
import sys
filename = "/Users/audemard/Temp/xcsp23/dataframe-cop_mini.json"
solver = sys.argv[1]


f = open(filename)
data = json.load(f)
print(data[0])
f.close()

for result in data :
    if result['experiment_ware'] != solver:
        continue
    print(result)
