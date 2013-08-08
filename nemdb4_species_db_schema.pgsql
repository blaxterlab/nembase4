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

SET default_with_oids = true;

--
-- Name: info; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE info (
    spec_id character varying(5) NOT NULL,
    name text NOT NULL,
    short_des text,
    long_des text,
    lifecyc text,
    taxonomy text,
    links text,
    clade character varying(15),
    photo text
);


ALTER TABLE public.info OWNER TO ben;

--
-- Name: org; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE org (
    spec_id character varying(5) NOT NULL,
    seq_cent text,
    e_name text,
    email text
);


ALTER TABLE public.org OWNER TO ben;

SET default_with_oids = false;

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
    num_lib integer
);


ALTER TABLE public.species OWNER TO ben;

SET default_with_oids = true;

--
-- Name: stats; Type: TABLE; Schema: public; Owner: ben; Tablespace: 
--

CREATE TABLE stats (
    spec_id character varying(15) NOT NULL,
    num_seq integer,
    num_clus integer,
    num_lib integer,
    directory text,
    file text,
    last_update date,
    anno_update date
);


ALTER TABLE public.stats OWNER TO ben;

--
-- Name: info_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY info
    ADD CONSTRAINT info_pkey PRIMARY KEY (spec_id);


--
-- Name: species_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY species
    ADD CONSTRAINT species_pkey PRIMARY KEY (spec_id);


--
-- Name: stats_pkey; Type: CONSTRAINT; Schema: public; Owner: ben; Tablespace: 
--

ALTER TABLE ONLY stats
    ADD CONSTRAINT stats_pkey PRIMARY KEY (spec_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: info; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE info FROM PUBLIC;
REVOKE ALL ON TABLE info FROM ben;
GRANT ALL ON TABLE info TO ben;
GRANT SELECT ON TABLE info TO webuser;


--
-- Name: org; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE org FROM PUBLIC;
REVOKE ALL ON TABLE org FROM ben;
GRANT ALL ON TABLE org TO ben;
GRANT SELECT ON TABLE org TO webuser;


--
-- Name: species; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE species FROM PUBLIC;
REVOKE ALL ON TABLE species FROM ben;
GRANT ALL ON TABLE species TO ben;
GRANT SELECT ON TABLE species TO webuser;


--
-- Name: stats; Type: ACL; Schema: public; Owner: ben
--

REVOKE ALL ON TABLE stats FROM PUBLIC;
REVOKE ALL ON TABLE stats FROM ben;
GRANT ALL ON TABLE stats TO ben;
GRANT SELECT ON TABLE stats TO webuser;


--
-- PostgreSQL database dump complete
--

