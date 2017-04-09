import networkx as nx
import os

edge_list = "edgeList.txt"
G = nx.read_edgelist("edgeList.txt", create_using = nx.DiGraph())
pr = nx.pagerank(G, alpha = 0.85, personalization = None, max_iter = 30, 
	tol = 1e-06, nstart = None, weight = 'weight', dangling = None)

base = "/Users/asifzubair/projects/information_retrieval/solr/data/NBCNewsData/NBCNewsDownloadData/"
with open('external_PageRankFile.txt', 'w') as out_file:
	for key, value in pr.items():
		out_file.write(os.path.join(base, key) + "=" + str(value) + "\n")
