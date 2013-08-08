--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: a8r_blastec; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE a8r_blastec (
    pept_id character varying(14) NOT NULL,
    ec_id character varying(16) NOT NULL,
    descr text,
    besthit character varying(14) NOT NULL,
    bestscore double precision,
    bestev double precision,
    hitnum integer,
    maxhits integer,
    fraction double precision
);


ALTER TABLE public.a8r_blastec OWNER TO ben;

--
-- Name: a8r_blastgo; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE a8r_blastgo (
    pept_id character varying(14) NOT NULL,
    go_term character varying(16) NOT NULL,
    pcf character varying(2),
    descr text,
    slim character varying(16),
    besthit character varying(14) NOT NULL,
    bestscore double precision,
    bestev double precision,
    hitnum integer,
    maxhits integer,
    fraction double precision
);


ALTER TABLE public.a8r_blastgo OWNER TO ben;

--
-- Name: a8r_blastkegg; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE a8r_blastkegg (
    pept_id character varying(14) NOT NULL,
    ko_id character varying(16) NOT NULL,
    path character varying(16),
    descr text,
    besthit character varying(14) NOT NULL,
    bestscore double precision,
    bestev double precision,
    hitnum integer,
    maxhits integer,
    fraction double precision
);


ALTER TABLE public.a8r_blastkegg OWNER TO ben;

--
-- Name: blast; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE blast (
    clus_id character varying(10) NOT NULL,
    prog character varying(10),
    db character varying(30),
    date character(11),
    score double precision,
    eval double precision,
    id text,
    description text,
    frame smallint,
    b_start integer,
    b_end integer,
    contig smallint,
    hit integer
);


ALTER TABLE public.blast OWNER TO ben;

--
-- Name: blast_top; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE blast_top (
    clus_id character varying(10),
    prog character varying(10),
    db character varying(30),
    date character(11),
    score double precision,
    eval double precision,
    id text,
    description text,
    frame smallint,
    b_start integer,
    b_end integer,
    contig smallint,
    hit integer
);


ALTER TABLE public.blast_top OWNER TO ben;

--
-- Name: clone_name; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE clone_name (
    est_id character varying(15) NOT NULL,
    clone_id character varying(15)
);


ALTER TABLE public.clone_name OWNER TO ben;

--
-- Name: cluster; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE cluster (
    clus_id character varying(10) NOT NULL,
    num_ests integer,
    contig integer NOT NULL,
    consensus text,
    retired integer
);


ALTER TABLE public.cluster OWNER TO ben;

--
-- Name: ec2description; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE ec2description (
    ec character varying(20),
    description character varying(100)
);


ALTER TABLE public.ec2description OWNER TO ben;

--
-- Name: est; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE est (
    est_id character varying(15) NOT NULL,
    clus_id character varying(10) NOT NULL,
    contig integer,
    type smallint,
    library integer,
    a_start integer,
    a_end integer,
    q_start integer,
    q_end integer
);


ALTER TABLE public.est OWNER TO ben;

--
-- Name: est_seq; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE est_seq (
    est_id character varying(15) NOT NULL,
    sequence text
);


ALTER TABLE public.est_seq OWNER TO ben;

--
-- Name: genome_pep; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE genome_pep (
    genomepep_id character varying(20) NOT NULL,
    seq text,
    date character varying(12),
    species character(30),
    wormpep character varying(20)
);


ALTER TABLE public.genome_pep OWNER TO ben;

--
-- Name: stage_count; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE stage_count (
    clus_id character varying(10),
    adult integer,
    eggs integer,
    l1 integer,
    l2 integer,
    l3 integer,
    l4 integer,
    mixed integer,
    unknown integer,
    total_ests integer,
    spid character varying(5),
    adult_p real,
    eggs_p real,
    l1_p real,
    l2_p real,
    l3_p real,
    l4_p real,
    mixed_p real,
    unknown_p real
);


ALTER TABLE public.stage_count OWNER TO ben;

--
-- Name: hit_table; Type: VIEW; Schema: public; Owner: ben
--

CREATE VIEW hit_table AS
    SELECT DISTINCT ON (stage_count.total_ests, blast.score, stage_count.clus_id, blast.db) stage_count.clus_id, stage_count.total_ests, blast.description, blast.id, blast.score, blast.prog, blast.db FROM stage_count, blast WHERE (((stage_count.adult_p > (50)::double precision) AND ((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((stage_count.spid)::text = 'XIC'::text) OR ((stage_count.spid)::text = 'DVC'::text)) OR ((stage_count.spid)::text = 'HSC'::text)) OR ((stage_count.spid)::text = 'ODC'::text)) OR ((stage_count.spid)::text = 'PAC'::text)) OR ((stage_count.spid)::text = 'TIC'::text)) OR ((stage_count.spid)::text = 'PSC'::text)) OR ((stage_count.spid)::text = 'SCC'::text)) OR ((stage_count.spid)::text = 'SFC'::text)) OR ((stage_count.spid)::text = 'PPC'::text)) OR ((stage_count.spid)::text = 'CBC'::text)) OR ((stage_count.spid)::text = 'CEC'::text)) OR ((stage_count.spid)::text = 'CGC'::text)) OR ((stage_count.spid)::text = 'CJC'::text)) OR ((stage_count.spid)::text = 'CRC'::text)) OR ((stage_count.spid)::text = 'CSC'::text)) OR ((stage_count.spid)::text = 'HBC'::text)) OR ((stage_count.spid)::text = 'TMC'::text)) OR ((stage_count.spid)::text = 'TSC'::text)) OR ((stage_count.spid)::text = 'TVC'::text)) OR ((stage_count.spid)::text = 'AIC'::text)) OR ((stage_count.spid)::text = 'ALC'::text)) OR ((stage_count.spid)::text = 'ASC'::text)) OR ((stage_count.spid)::text = 'TCC'::text)) OR ((stage_count.spid)::text = 'TLC'::text)) OR ((stage_count.spid)::text = 'BMC'::text)) OR ((stage_count.spid)::text = 'BPC'::text)) OR ((stage_count.spid)::text = 'DIC'::text)) OR ((stage_count.spid)::text = 'LLC'::text)) OR ((stage_count.spid)::text = 'LSC'::text)) OR ((stage_count.spid)::text = 'OCC'::text)) OR ((stage_count.spid)::text = 'OFC'::text)) OR ((stage_count.spid)::text = 'OVC'::text)) OR ((stage_count.spid)::text = 'WBC'::text)) OR ((stage_count.spid)::text = 'PTC'::text)) OR ((stage_count.spid)::text = 'SRC'::text)) OR ((stage_count.spid)::text = 'SSC'::text)) OR ((stage_count.spid)::text = 'BUC'::text)) OR ((stage_count.spid)::text = 'BXC'::text)) OR ((stage_count.spid)::text = 'DAC'::text)) OR ((stage_count.spid)::text = 'GMC'::text)) OR ((stage_count.spid)::text = 'GPC'::text)) OR ((stage_count.spid)::text = 'GRC'::text)) OR ((stage_count.spid)::text = 'HGC'::text)) OR ((stage_count.spid)::text = 'PEC'::text)) OR ((stage_count.spid)::text = 'PVC'::text)) OR ((stage_count.spid)::text = 'RSC'::text)) OR ((stage_count.spid)::text = 'ZPC'::text)) OR ((stage_count.spid)::text = 'AAC'::text)) OR ((stage_count.spid)::text = 'ABC'::text)) OR ((stage_count.spid)::text = 'ACC'::text)) OR ((stage_count.spid)::text = 'AYC'::text)) OR ((stage_count.spid)::text = 'HCC'::text)) OR ((stage_count.spid)::text = 'NAC'::text)) OR ((stage_count.spid)::text = 'NBC'::text)) OR ((stage_count.spid)::text = 'OOC'::text)) OR ((stage_count.spid)::text = 'TDC'::text)) OR ((stage_count.spid)::text = 'MAC'::text)) OR ((stage_count.spid)::text = 'MCC'::text)) OR ((stage_count.spid)::text = 'MHC'::text)) OR ((stage_count.spid)::text = 'MIC'::text)) OR ((stage_count.spid)::text = 'MJC'::text)) OR ((stage_count.spid)::text = 'MPC'::text))) AND ((stage_count.clus_id)::text = (blast.clus_id)::text)) ORDER BY stage_count.total_ests DESC, blast.score DESC;


ALTER TABLE public.hit_table OWNER TO ben;

SET default_with_oids = true;

--
-- Name: interpro; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE interpro (
    pept_id character varying(20) NOT NULL,
    dom_id character varying(12),
    d_start integer,
    d_end integer,
    score double precision,
    date character varying(12),
    spid character varying(5)
);


ALTER TABLE public.interpro OWNER TO ben;

--
-- Name: interpro_key; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE interpro_key (
    dom_id character varying(12) NOT NULL,
    description text,
    database text,
    ipr_id character varying(12),
    short_desc text
);


ALTER TABLE public.interpro_key OWNER TO ben;

SET default_with_oids = false;

--
-- Name: lib; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE lib (
    lib_id integer NOT NULL,
    name text,
    organism text,
    strain text,
    sex text,
    stage text,
    tissue text,
    vector text,
    type text,
    rs1 text,
    rs2 text,
    description text,
    direction smallint
);


ALTER TABLE public.lib OWNER TO ben;

--
-- Name: lib_count; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE lib_count (
    clus_id character varying(10),
    lib_1 integer,
    lib_21267 integer,
    lib_10251 integer,
    lib_11963 integer,
    lib_12004 integer,
    lib_12013 integer,
    lib_9532 integer,
    lib_9775 integer,
    lib_9981 integer,
    lib_13889 integer,
    lib_13890 integer,
    lib_17416 integer,
    lib_17417 integer,
    lib_18940 integer,
    lib_11114 integer,
    lib_17046 integer,
    lib_10064 integer,
    lib_21294 integer,
    lib_21752 integer,
    lib_20627 integer,
    lib_16694 integer,
    lib_16695 integer,
    lib_21753 integer,
    lib_22038 integer,
    lib_22039 integer,
    lib_22520 integer,
    lib_2703 integer,
    lib_9785 integer,
    lib_9922 integer,
    lib_10067 integer,
    lib_10083 integer,
    lib_10242 integer,
    lib_10243 integer,
    lib_10390 integer,
    lib_11123 integer,
    lib_11909 integer,
    lib_12003 integer,
    lib_12063 integer,
    lib_18134 integer,
    lib_18135 integer,
    lib_19440 integer,
    lib_19715 integer,
    lib_21074 integer,
    lib_22717 integer,
    lib_22718 integer,
    lib_22881 integer,
    lib_22882 integer,
    lib_22883 integer,
    lib_22884 integer,
    lib_22885 integer,
    lib_22886 integer,
    lib_2572 integer,
    lib_2573 integer,
    lib_6807 integer,
    lib_8884 integer,
    lib_9807 integer,
    lib_10358 integer,
    lib_11962 integer,
    lib_11964 integer,
    lib_1240 integer,
    lib_9808 integer,
    lib_9810 integer,
    lib_9897 integer,
    lib_9898 integer,
    lib_9899 integer,
    lib_1263 integer,
    lib_12737 integer,
    lib_1374 integer,
    lib_14174 integer,
    lib_1549 integer,
    lib_2371 integer,
    lib_2372 integer,
    lib_2605 integer,
    lib_2700 integer,
    lib_279 integer,
    lib_281 integer,
    lib_282 integer,
    lib_283 integer,
    lib_314 integer,
    lib_2756 integer,
    lib_4121 integer,
    lib_531 integer,
    lib_604 integer,
    lib_19832 integer,
    lib_19833 integer,
    lib_19834 integer,
    lib_19835 integer,
    lib_20926 integer,
    lib_23282 integer,
    lib_22273 integer,
    lib_22528 integer,
    lib_22924 integer,
    lib_17039 integer,
    lib_17560 integer,
    lib_22530 integer,
    lib_20517 integer,
    lib_21631 integer,
    lib_10055 integer,
    lib_385 integer,
    lib_10885 integer,
    lib_22887 integer,
    lib_10056 integer,
    lib_16607 integer,
    lib_3759 integer,
    lib_9763 integer,
    lib_10088 integer,
    lib_12967 integer,
    lib_17026 integer,
    lib_17027 integer,
    lib_3758 integer,
    lib_12014 integer,
    lib_12015 integer,
    lib_12384 integer,
    lib_12743 integer,
    lib_1841 integer,
    lib_20744 integer,
    lib_4031 integer,
    lib_5396 integer,
    lib_7043 integer,
    lib_7044 integer,
    lib_7045 integer,
    lib_8631 integer,
    lib_12375 integer,
    lib_12376 integer,
    lib_12377 integer,
    lib_12702 integer,
    lib_15080 integer,
    lib_15097 integer,
    lib_15878 integer,
    lib_20793 integer,
    lib_2718 integer,
    lib_5577 integer,
    lib_14435 integer,
    lib_1726 integer,
    lib_5578 integer,
    lib_5604 integer,
    lib_8676 integer,
    lib_8668 integer,
    lib_13712 integer,
    lib_17155 integer,
    lib_2534 integer,
    lib_17016 integer,
    lib_20367 integer,
    lib_20441 integer,
    lib_22989 integer,
    lib_22990 integer,
    lib_22991 integer,
    lib_10355 integer,
    lib_10506 integer,
    lib_11124 integer,
    lib_19708 integer,
    lib_19824 integer,
    lib_12971 integer,
    lib_12981 integer,
    lib_13907 integer,
    lib_14510 integer,
    lib_11998 integer,
    lib_14265 integer,
    lib_14516 integer,
    lib_10331 integer,
    lib_10894 integer,
    lib_13925 integer,
    lib_15032 integer,
    lib_15492 integer,
    lib_15112 integer,
    lib_5579 integer,
    lib_5580 integer,
    lib_584 integer,
    lib_8656 integer,
    lib_8894 integer,
    lib_15493 integer,
    lib_15742 integer,
    lib_20941 integer,
    lib_3728 integer,
    lib_5334 integer,
    lib_15046 integer,
    lib_18922 integer,
    lib_1959 integer,
    lib_8759 integer,
    lib_8760 integer,
    lib_19353 integer,
    lib_5355 integer,
    lib_22927 integer,
    lib_22928 integer,
    lib_10058 integer,
    lib_10900 integer,
    lib_10906 integer,
    lib_19437 integer,
    lib_19438 integer,
    lib_19439 integer,
    lib_20734 integer,
    lib_20735 integer,
    lib_1575 integer,
    lib_1264 integer,
    lib_14173 integer,
    lib_10102 integer,
    lib_10103 integer,
    lib_10354 integer,
    lib_10482 integer,
    lib_10483 integer,
    lib_10959 integer,
    lib_8790 integer,
    lib_8885 integer,
    lib_8886 integer,
    lib_8887 integer,
    lib_11094 integer,
    lib_10907 integer,
    lib_16397 integer,
    lib_16398 integer,
    lib_20956 integer,
    lib_10084 integer,
    lib_15480 integer,
    lib_15699 integer,
    lib_17495 integer,
    lib_10919 integer,
    lib_16319 integer,
    lib_21257 integer,
    lib_22111 integer,
    lib_22112 integer,
    lib_10353 integer,
    lib_378 integer,
    lib_381 integer,
    lib_382 integer,
    lib_16212 integer,
    lib_18133 integer,
    lib_22888 integer,
    lib_22889 integer,
    lib_23021 integer,
    lib_23022 integer,
    lib_548 integer,
    lib_9732 integer,
    lib_12695 integer,
    lib_12696 integer,
    lib_22393 integer,
    lib_22394 integer,
    lib_8673 integer,
    lib_8674 integer,
    lib_8935 integer,
    lib_9534 integer,
    lib_9535 integer,
    lib_9601 integer,
    lib_12449 integer,
    lib_12472 integer,
    lib_21553 integer,
    lib_8888 integer,
    lib_9910 integer,
    lib_21364 integer,
    lib_10181 integer,
    lib_10908 integer,
    lib_10982 integer,
    lib_12324 integer,
    lib_21056 integer,
    lib_8660 integer,
    lib_8716 integer,
    lib_8717 integer,
    lib_370 integer,
    lib_16317 integer,
    lib_22929 integer,
    lib_22930 integer,
    lib_2808 integer,
    lib_1018 integer,
    lib_518 integer,
    lib_4072 integer,
    lib_16315 integer,
    lib_16316 integer,
    lib_16318 integer,
    lib_16529 integer,
    lib_16530 integer,
    lib_16531 integer,
    lib_10250 integer,
    lib_10564 integer,
    lib_1001 integer,
    lib_1245 integer,
    lib_389 integer,
    lib_393 integer,
    lib_953 integer,
    lib_10065 integer,
    lib_10066 integer,
    lib_12335 integer,
    lib_17426 integer,
    lib_9809 integer,
    lib_269 integer,
    lib_23281 integer,
    lib_18131 integer,
    lib_18132 integer,
    lib_22529 integer,
    lib_22925 integer,
    lib_22926 integer,
    lib_10850 integer,
    lib_21293 integer,
    lib_19436 integer,
    lib_13926 integer,
    lib_12385 integer,
    lib_12742 integer,
    lib_9688 integer,
    lib_13927 integer,
    lib_14259 integer,
    lib_9734 integer,
    lib_14254 integer,
    lib_15113 integer,
    lib_22992 integer,
    lib_23250 integer,
    lib_14512 integer,
    lib_15045 integer,
    lib_1375 integer,
    lib_1376 integer,
    lib_1576 integer,
    lib_1577 integer,
    lib_1916 integer,
    lib_284 integer,
    lib_3756 integer,
    lib_651 integer,
    lib_10093 integer,
    lib_10241 integer,
    lib_8845 integer,
    lib_9660 integer,
    lib_9661 integer,
    lib_9733 integer,
    lib_16211 integer,
    lib_10882 integer,
    lib_10391 integer,
    lib_10505 integer,
    lib_13855 integer,
    lib_478 integer,
    lib_479 integer,
    lib_648 integer,
    lib_12524 integer,
    lib_1630 integer,
    lib_15044 integer,
    lib_869 integer,
    libraries integer,
    total_ests integer
);


ALTER TABLE public.lib_count OWNER TO ben;

--
-- Name: lib_key; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE lib_key (
    lib_id text NOT NULL,
    species text,
    spec_id text,
    sex text,
    stage text
);


ALTER TABLE public.lib_key OWNER TO ben;

--
-- Name: node2tribe; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE node2tribe (
    node integer,
    tribe integer,
    inf integer,
    species_specific integer,
    singleton integer,
    members integer DEFAULT 0,
    non_nematode integer DEFAULT 0
);


ALTER TABLE public.node2tribe OWNER TO ben;

--
-- Name: node_stats; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE node_stats (
    node integer NOT NULL,
    inf integer DEFAULT 0,
    tribes integer DEFAULT 0,
    non_nematode integer DEFAULT 0,
    singleton integer DEFAULT 0,
    species_specific integer DEFAULT 0
);


ALTER TABLE public.node_stats OWNER TO ben;

--
-- Name: p4e_hsp; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE p4e_hsp (
    pept_ref integer NOT NULL,
    hsp_num integer NOT NULL,
    p_start integer,
    p_end integer,
    frame integer,
    evalue double precision,
    bit_score double precision,
    db_xref_id character varying(20)
);


ALTER TABLE public.p4e_hsp OWNER TO ben;

--
-- Name: p4e_ind; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE p4e_ind (
    pept_ref integer NOT NULL,
    pept_id character varying(10) NOT NULL,
    clus_id character varying(10) NOT NULL,
    contig integer,
    date character varying(12),
    method character varying(25),
    gen_code character varying(25),
    active boolean,
    seq text,
    genomepep_id character varying(20) DEFAULT 0,
    wormpep character varying(20)
);


ALTER TABLE public.p4e_ind OWNER TO ben;

--
-- Name: p4e_loc; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE p4e_loc (
    pept_ref integer NOT NULL,
    xtn_s integer,
    conf_s integer,
    frame_s integer,
    conf_e integer,
    xtn_e integer,
    frame_e integer
);


ALTER TABLE public.p4e_loc OWNER TO ben;

--
-- Name: pathway_id2name; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE pathway_id2name (
    id character varying(10),
    name character(100)
);


ALTER TABLE public.pathway_id2name OWNER TO ben;

--
-- Name: pathway_map; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE pathway_map (
    id character varying(15),
    ec character varying(15)
);


ALTER TABLE public.pathway_map OWNER TO ben;

--
-- Name: reciprocals; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE reciprocals (
    pept_id character varying(11) NOT NULL,
    db character varying(3),
    hit_id character varying(11) NOT NULL,
    e_val double precision,
    bit_score double precision,
    class character varying(4)
);


ALTER TABLE public.reciprocals OWNER TO ben;

--
-- Name: sex_count; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE sex_count (
    clus_id character varying(10),
    female integer,
    male integer,
    mixed integer,
    total_ests integer
);


ALTER TABLE public.sex_count OWNER TO ben;

--
-- Name: signalp; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE signalp (
    pept_id character varying(20),
    nn_cmax numeric(5,3),
    nn_cmax_pos integer,
    nn_cmax_yn boolean,
    nn_ymax numeric(5,3),
    nn_ymax_pos integer,
    nn_ymax_yn boolean,
    nn_smax numeric(5,3),
    nn_smax_pos integer,
    nn_smax_yn boolean,
    nn_smean numeric(5,3),
    nn_smean_yn boolean,
    nn_d numeric(5,3),
    nn_d_yn boolean,
    hmm_pept_id character varying(20),
    hmm_asq character varying(1),
    hmm_cmax numeric(5,3),
    hmm_cmax_pos integer,
    hmm_cmax_yn boolean,
    hmm_sprob numeric(5,3),
    hmm_sprob_yn boolean,
    id_count integer
);


ALTER TABLE public.signalp OWNER TO ben;

--
-- Name: species; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE species (
    spec_id character varying(5) NOT NULL,
    species text,
    short_des text,
    long_des text,
    name text,
    email text,
    num_seq integer,
    num_clus integer,
    num_lib integer,
    clade character(5)
);


ALTER TABLE public.species OWNER TO ben;

--
-- Name: sqlmapfile; Type: TABLE; Schema: public; Owner: webuser; Tablespace: 
--

CREATE TABLE sqlmapfile (
    data character(10000)
);


ALTER TABLE public.sqlmapfile OWNER TO webuser;

--
-- Name: tribe; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE tribe (
    spid character varying(5),
    pept_id character varying(50),
    inf11 integer,
    inf15 integer,
    inf20 integer,
    inf25 integer,
    inf30 integer,
    inf35 integer,
    inf40 integer,
    inf45 integer,
    inf50 integer,
    non_nematode integer DEFAULT 0,
    eval double precision,
    top_hit integer
);


ALTER TABLE public.tribe OWNER TO ben;

--
-- Name: tribe_info; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE tribe_info (
    tribe integer NOT NULL,
    inflation double precision NOT NULL,
    num_pepts integer,
    num_sp integer
);


ALTER TABLE public.tribe_info OWNER TO ben;

--
-- Name: tribe_node; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE tribe_node (
    node integer NOT NULL,
    description character varying(50)
);


ALTER TABLE public.tribe_node OWNER TO ben;

--
-- Name: clone_name_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY clone_name
    ADD CONSTRAINT clone_name_pkey PRIMARY KEY (est_id);


--
-- Name: cluster_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY cluster
    ADD CONSTRAINT cluster_pkey PRIMARY KEY (clus_id, contig);


--
-- Name: est_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY est
    ADD CONSTRAINT est_pkey PRIMARY KEY (est_id);


--
-- Name: genome_pep_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY genome_pep
    ADD CONSTRAINT genome_pep_pkey PRIMARY KEY (genomepep_id);


--
-- Name: interpro_key_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY interpro_key
    ADD CONSTRAINT interpro_key_pkey PRIMARY KEY (dom_id);


--
-- Name: lib_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY lib
    ADD CONSTRAINT lib_pkey PRIMARY KEY (lib_id);


--
-- Name: p4e_hsp_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY p4e_hsp
    ADD CONSTRAINT p4e_hsp_pkey PRIMARY KEY (pept_ref, hsp_num);


--
-- Name: p4e_ind_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY p4e_ind
    ADD CONSTRAINT p4e_ind_pkey PRIMARY KEY (pept_ref);


--
-- Name: reciprocals_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY reciprocals
    ADD CONSTRAINT reciprocals_pkey PRIMARY KEY (pept_id, hit_id);


--
-- Name: species_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY species
    ADD CONSTRAINT species_pkey PRIMARY KEY (spec_id);


--
-- Name: tribe_info_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY tribe_info
    ADD CONSTRAINT tribe_info_pkey PRIMARY KEY (tribe, inflation);


--
-- Name: tribe_node_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY tribe_node
    ADD CONSTRAINT tribe_node_pkey PRIMARY KEY (node);


--
-- Name: interpro_domid; Type: INDEX; Schema: public; Owner: ben; Tablespace: 
--

CREATE INDEX interpro_domid ON interpro USING btree (dom_id, spid);


--
-- Name: interprokey_domid; Type: INDEX; Schema: public; Owner: ben; Tablespace: 
--

CREATE INDEX interprokey_domid ON interpro_key USING btree (dom_id);


--
-- Name: interprokey_iprid; Type: INDEX; Schema: public; Owner: ben; Tablespace: 
--

CREATE INDEX interprokey_iprid ON interpro_key USING btree (ipr_id);


--
-- Name: sp; Type: INDEX; Schema: public; Owner: ben; Tablespace: 
--

CREATE INDEX sp ON cluster USING btree (clus_id);


--
-- Name: est_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: ben
--

ALTER TABLE ONLY est_seq
    ADD CONSTRAINT est_id_fk FOREIGN KEY (est_id) REFERENCES est(est_id);


--
-- Name: pept_ref_fk; Type: FK CONSTRAINT; Schema: public; Owner: ben
--

ALTER TABLE ONLY p4e_loc
    ADD CONSTRAINT pept_ref_fk FOREIGN KEY (pept_ref) REFERENCES p4e_ind(pept_ref);


--
-- Name: pept_ref_fk; Type: FK CONSTRAINT; Schema: public; Owner: ben
--

ALTER TABLE ONLY p4e_hsp
    ADD CONSTRAINT pept_ref_fk FOREIGN KEY (pept_ref) REFERENCES p4e_ind(pept_ref);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: a8r_blastec; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE a8r_blastec FROM PUBLIC;
REVOKE ALL ON TABLE a8r_blastec FROM ben;
GRANT ALL ON TABLE a8r_blastec TO ben;
GRANT SELECT ON TABLE a8r_blastec TO webuser;


--
-- Name: a8r_blastgo; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE a8r_blastgo FROM PUBLIC;
REVOKE ALL ON TABLE a8r_blastgo FROM ben;
GRANT ALL ON TABLE a8r_blastgo TO ben;
GRANT SELECT ON TABLE a8r_blastgo TO webuser;


--
-- Name: a8r_blastkegg; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE a8r_blastkegg FROM PUBLIC;
REVOKE ALL ON TABLE a8r_blastkegg FROM ben;
GRANT ALL ON TABLE a8r_blastkegg TO ben;
GRANT SELECT ON TABLE a8r_blastkegg TO webuser;


--
-- Name: blast; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE blast FROM PUBLIC;
REVOKE ALL ON TABLE blast FROM ben;
GRANT ALL ON TABLE blast TO ben;
GRANT SELECT ON TABLE blast TO webuser;


--
-- Name: blast_top; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE blast_top FROM PUBLIC;
REVOKE ALL ON TABLE blast_top FROM ben;
GRANT ALL ON TABLE blast_top TO ben;
GRANT SELECT ON TABLE blast_top TO webuser;


--
-- Name: clone_name; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE clone_name FROM PUBLIC;
REVOKE ALL ON TABLE clone_name FROM ben;
GRANT ALL ON TABLE clone_name TO ben;
GRANT SELECT ON TABLE clone_name TO webuser;


--
-- Name: cluster; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE cluster FROM PUBLIC;
REVOKE ALL ON TABLE cluster FROM ben;
GRANT ALL ON TABLE cluster TO ben;
GRANT SELECT ON TABLE cluster TO webuser;


--
-- Name: ec2description; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE ec2description FROM PUBLIC;
REVOKE ALL ON TABLE ec2description FROM ben;
GRANT ALL ON TABLE ec2description TO ben;
GRANT SELECT ON TABLE ec2description TO webuser;


--
-- Name: est; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE est FROM PUBLIC;
REVOKE ALL ON TABLE est FROM ben;
GRANT ALL ON TABLE est TO ben;
GRANT SELECT ON TABLE est TO webuser;


--
-- Name: est_seq; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE est_seq FROM PUBLIC;
REVOKE ALL ON TABLE est_seq FROM ben;
GRANT ALL ON TABLE est_seq TO ben;
GRANT SELECT ON TABLE est_seq TO webuser;


--
-- Name: genome_pep; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE genome_pep FROM PUBLIC;
REVOKE ALL ON TABLE genome_pep FROM ben;
GRANT ALL ON TABLE genome_pep TO ben;
GRANT SELECT ON TABLE genome_pep TO webuser;


--
-- Name: stage_count; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE stage_count FROM PUBLIC;
REVOKE ALL ON TABLE stage_count FROM ben;
GRANT ALL ON TABLE stage_count TO ben;
GRANT SELECT ON TABLE stage_count TO webuser;


--
-- Name: hit_table; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE hit_table FROM PUBLIC;
REVOKE ALL ON TABLE hit_table FROM ben;
GRANT ALL ON TABLE hit_table TO ben;
GRANT SELECT ON TABLE hit_table TO webuser;


--
-- Name: interpro; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE interpro FROM PUBLIC;
REVOKE ALL ON TABLE interpro FROM ben;
GRANT ALL ON TABLE interpro TO ben;
GRANT SELECT ON TABLE interpro TO webuser;


--
-- Name: interpro_key; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE interpro_key FROM PUBLIC;
REVOKE ALL ON TABLE interpro_key FROM ben;
GRANT ALL ON TABLE interpro_key TO ben;
GRANT SELECT ON TABLE interpro_key TO webuser;


--
-- Name: lib; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE lib FROM PUBLIC;
REVOKE ALL ON TABLE lib FROM ben;
GRANT ALL ON TABLE lib TO ben;
GRANT SELECT ON TABLE lib TO webuser;


--
-- Name: lib_count; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE lib_count FROM PUBLIC;
REVOKE ALL ON TABLE lib_count FROM ben;
GRANT ALL ON TABLE lib_count TO ben;
GRANT SELECT ON TABLE lib_count TO webuser;


--
-- Name: lib_key; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE lib_key FROM PUBLIC;
REVOKE ALL ON TABLE lib_key FROM ben;
GRANT ALL ON TABLE lib_key TO ben;
GRANT SELECT ON TABLE lib_key TO webuser;


--
-- Name: node2tribe; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE node2tribe FROM PUBLIC;
REVOKE ALL ON TABLE node2tribe FROM ben;
GRANT ALL ON TABLE node2tribe TO ben;
GRANT SELECT ON TABLE node2tribe TO webuser;


--
-- Name: node_stats; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE node_stats FROM PUBLIC;
REVOKE ALL ON TABLE node_stats FROM ben;
GRANT ALL ON TABLE node_stats TO ben;
GRANT SELECT ON TABLE node_stats TO webuser;


--
-- Name: p4e_hsp; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE p4e_hsp FROM PUBLIC;
REVOKE ALL ON TABLE p4e_hsp FROM ben;
GRANT ALL ON TABLE p4e_hsp TO ben;
GRANT SELECT ON TABLE p4e_hsp TO webuser;


--
-- Name: p4e_ind; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE p4e_ind FROM PUBLIC;
REVOKE ALL ON TABLE p4e_ind FROM ben;
GRANT ALL ON TABLE p4e_ind TO ben;
GRANT SELECT ON TABLE p4e_ind TO webuser;


--
-- Name: p4e_loc; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE p4e_loc FROM PUBLIC;
REVOKE ALL ON TABLE p4e_loc FROM ben;
GRANT ALL ON TABLE p4e_loc TO ben;
GRANT SELECT ON TABLE p4e_loc TO webuser;


--
-- Name: pathway_id2name; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE pathway_id2name FROM PUBLIC;
REVOKE ALL ON TABLE pathway_id2name FROM ben;
GRANT ALL ON TABLE pathway_id2name TO ben;
GRANT SELECT ON TABLE pathway_id2name TO webuser;


--
-- Name: pathway_map; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE pathway_map FROM PUBLIC;
REVOKE ALL ON TABLE pathway_map FROM ben;
GRANT ALL ON TABLE pathway_map TO ben;
GRANT SELECT ON TABLE pathway_map TO webuser;


--
-- Name: reciprocals; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE reciprocals FROM PUBLIC;
REVOKE ALL ON TABLE reciprocals FROM ben;
GRANT ALL ON TABLE reciprocals TO ben;
GRANT SELECT ON TABLE reciprocals TO webuser;


--
-- Name: sex_count; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE sex_count FROM PUBLIC;
REVOKE ALL ON TABLE sex_count FROM ben;
GRANT ALL ON TABLE sex_count TO ben;
GRANT SELECT ON TABLE sex_count TO webuser;


--
-- Name: signalp; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE signalp FROM PUBLIC;
REVOKE ALL ON TABLE signalp FROM ben;
GRANT ALL ON TABLE signalp TO ben;
GRANT SELECT ON TABLE signalp TO webuser;


--
-- Name: species; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE species FROM PUBLIC;
REVOKE ALL ON TABLE species FROM ben;
GRANT ALL ON TABLE species TO ben;
GRANT SELECT ON TABLE species TO webuser;


--
-- Name: sqlmapfile; Type: ACL; Schema: public; Owner: webuser
--

REVOKE ALL ON TABLE sqlmapfile FROM PUBLIC;
REVOKE ALL ON TABLE sqlmapfile FROM webuser;
GRANT ALL ON TABLE sqlmapfile TO webuser;


--
-- Name: tribe; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE tribe FROM PUBLIC;
REVOKE ALL ON TABLE tribe FROM ben;
GRANT ALL ON TABLE tribe TO ben;
GRANT SELECT ON TABLE tribe TO webuser;


--
-- Name: tribe_info; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE tribe_info FROM PUBLIC;
REVOKE ALL ON TABLE tribe_info FROM ben;
GRANT ALL ON TABLE tribe_info TO ben;
GRANT SELECT ON TABLE tribe_info TO webuser;


--
-- Name: tribe_node; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE tribe_node FROM PUBLIC;
REVOKE ALL ON TABLE tribe_node FROM ben;
GRANT ALL ON TABLE tribe_node TO ben;
GRANT SELECT ON TABLE tribe_node TO webuser;


--
-- PostgreSQL database dump complete
--

