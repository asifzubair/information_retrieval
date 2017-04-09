from bs4 import BeautifulSoup
import glob
import os
from collections import defaultdict

map_file = 'data/NBCNewsData/mapNBCNewsDataFile.csv'
data_dir = 'data/NBCNewsData/NBCNewsDownloadData'

#fileUrlMap = defaultdict(str)
#urlFileMap = defaultdict(str)
fileUrlMap = {}
urlFileMap = {}
with open(map_file) as m:
	for line in m:
		(key, val) = line.split(',')
		fileUrlMap[key] = val.strip()
		urlFileMap[val.strip()] = key

with open('edgeList_bu.txt', 'w') as out_file:
	for f in glob.glob(os.path.join(data_dir, "*.html")):
		soup = BeautifulSoup(open(f))
		for link in soup.find_all('a', href = True):
			url = link['href'].strip()
			if url in urlFileMap.keys():
				out_file.write(os.path.basename(f) + ' ' + urlFileMap[url] + '\n')
