nembase4
========

NEMBASE is a resource for nematode transcriptome analysis, and a research tool for nematode biology, drug discovery and vaccine design.

#### Publication

[NEMBASE4: The nematode transcriptome resource (open access article)](http://www.sciencedirect.com/science/article/pii/S0020751911001044)  
Benjamin Elsworth, James Wasmuth and Mark Blaxter. International Journal for Parasitology Volume 41, Issue 8, July 2011, Pages 881-894

#### Construction

Nembase4 was constructed in 2008 using 679,480 public nematode ESTs from NCBI. The core database was generated using the EST analysis pipeline [PartiGene](http://www.nematodes.org/bioinformatics/PartiGene/) and has since been significantly modified.
Contigs have been annotated using BLAST, [annot8r](http://www.nematodes.org/bioinformatics/annot8r/) and [InterProScan](https://code.google.com/p/interproscan/).

#### Database design

###### nemdb4

|Table|Description|
|------|------|
|a8r_blastec| Enzyme annotations from annot8r|  
|a8r_blastgo| GO annotations from annot8r|  
|a8r_blastkegg|KEGG annotations from annot8r|  
|blast|BLAST data|  
|blast_top|Top BLAST hits|  
|clone_name| Original EST name|
|cluster| Data for each EST cluster, including number of ESTs, contig number and consensus sequence|
|ec2description|Map of EC number and description|
|est|Data for each EST, including cluster ID, contig and library|
|est_seq| Seqeunce data for each EST|
|genome_pep| Data for each peptide including species, data origin and sequence|
|stage_count| Counts of ESTs per lifecycle stage for each cluster|
|hit_table| | A combintation of cluster and BLAST information |
|interpro| InterProScan annotation results |
|interpro_key| Mapping information for InterPro and domain IDs |
|lib| EST Library information|
|lib_count| Counts of ESTs per library|
|lib_key| More library / spcies information |
|node2tribe| Tribe information for each node on the tribe_tree.php plot|
|node_stats| Overall tribe stats for each node |
|p4e_hsp| Prot4EST - information about high scoring pairs (HSPs)|
|p4e_ind| Prot4EST - main information table for each predicted peptide |
|p4e_loc| Prot4EST - details about the translation|
|pathway_id2name||
|pathway_map||
|reciprocals||
|sex_count||
|signalp||
|species||
|sqlmapfile||
|tribe||
|tribe_info||
|tribe_node||
|interpro_domid||
|interprokey_domid||
|interprokey_iprid||

###### species_db4

|Table|Description|
|------|------|
|info||
|org||
|species||
|stats||
