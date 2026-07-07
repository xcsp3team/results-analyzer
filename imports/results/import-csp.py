# ./rec.sh | python import.py
import sys
# //{"results" : [{"name": "maincsp/AztecDiamond-025_c22", "status": "sat", "time": 3}, {"name":"maincsp/AztecDiamond-030_c22", "status": "unknown"}]
results = {'results': []}
for line in sys.stdin:
    tmp = line.split()
    if len(tmp) == 0:
        continue
    results['results'].extend([{'name': tmp[0], "status": tmp[1], "time": tmp[2]}])
print(results)
