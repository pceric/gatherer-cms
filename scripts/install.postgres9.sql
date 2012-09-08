--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: articles; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE articles (
    id integer NOT NULL,
    title character varying(128) NOT NULL,
    content text NOT NULL,
    tags character varying(255),
    comments integer DEFAULT 0 NOT NULL,
    published smallint DEFAULT 0 NOT NULL,
    pubdate timestamp without time zone NOT NULL,
    moddate timestamp without time zone,
    hits integer DEFAULT 0 NOT NULL
);


--
-- Name: articles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE articles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: articles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE articles_id_seq OWNED BY articles.id;


--
-- Name: articles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('articles_id_seq', 3, true);


--
-- Name: banners; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE banners (
    id integer NOT NULL,
    client integer NOT NULL,
    size character varying(16) DEFAULT '468x60'::character varying NOT NULL,
    image character varying(255),
    url character varying(255),
    code text,
    active smallint DEFAULT 1 NOT NULL,
    impressions integer DEFAULT 0 NOT NULL,
    clicks integer DEFAULT 0 NOT NULL,
    startdate timestamp without time zone NOT NULL,
    enddate timestamp without time zone
);


--
-- Name: banners_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE banners_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: banners_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE banners_id_seq OWNED BY banners.id;


--
-- Name: banners_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('banners_id_seq', 1, false);


--
-- Name: clients; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE clients (
    id integer NOT NULL,
    name character varying(45) NOT NULL,
    contact character varying(128),
    email character varying(128),
    extrainfo text,
    createddate timestamp without time zone NOT NULL
);


--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('clients_id_seq', 1, false);


--
-- Name: config; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE config (
    key character varying(128) NOT NULL,
    value text
);


--
-- Name: menu; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE menu (
    id integer NOT NULL,
    name character varying(32) NOT NULL,
    link text,
    parent integer,
    weight smallint DEFAULT 0 NOT NULL
);


--
-- Name: menu_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE menu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: menu_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE menu_id_seq OWNED BY menu.id;


--
-- Name: menu_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('menu_id_seq', 6, true);


--
-- Name: news; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE news (
    id integer NOT NULL,
    title character varying(128) NOT NULL,
    content text NOT NULL,
    tags character varying(255),
    comments integer DEFAULT 0 NOT NULL,
    sticky smallint DEFAULT 0 NOT NULL,
    published smallint DEFAULT 0 NOT NULL,
    pubdate timestamp without time zone NOT NULL,
    moddate timestamp without time zone,
    hits integer DEFAULT 0 NOT NULL
);


--
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE news_id_seq OWNED BY news.id;


--
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('news_id_seq', 2, true);


--
-- Name: reader; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE reader (
    id integer NOT NULL,
    title character varying(128) NOT NULL,
    summary text NOT NULL,
    source character varying(255) NOT NULL,
    tags character varying(255),
    pubdate timestamp without time zone NOT NULL,
    annotation text,
    guid character varying(128) DEFAULT NULL 
);


--
-- Name: reader_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE reader_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reader_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE reader_id_seq OWNED BY reader.id;


--
-- Name: reader_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('reader_id_seq', 1, true);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY articles ALTER COLUMN id SET DEFAULT nextval('articles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY banners ALTER COLUMN id SET DEFAULT nextval('banners_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY menu ALTER COLUMN id SET DEFAULT nextval('menu_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY news ALTER COLUMN id SET DEFAULT nextval('news_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY reader ALTER COLUMN id SET DEFAULT nextval('reader_id_seq'::regclass);


--
-- Data for Name: articles; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO articles VALUES (1, 'About', '<p>All about me.</p>', 'about', 0, 1, NOW(), NULL, 0);
INSERT INTO articles VALUES (2, 'Test Article 1.5', '<p>The first page of my article...<!--pagebreak-->..and now the 2nd!</p>', 'first, article', 0, 1, NOW(), NULL, 0);


--
-- Data for Name: banners; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: config; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO config VALUES ('schema', '1');
INSERT INTO config VALUES ('fetchfreq', '1200');
INSERT INTO config VALUES ('siteauthor', 'Webmaster');
INSERT INTO config VALUES ('sitecontact', '');
INSERT INTO config VALUES ('plusid', '');
INSERT INTO config VALUES ('plusapikey', '');
INSERT INTO config VALUES ('siteURL', '');
INSERT INTO config VALUES ('sitekeywords', '');
INSERT INTO config VALUES ('sitetheme', 'default');
INSERT INTO config VALUES ('googlefeed', '');
INSERT INTO config VALUES ('imagedir', '');
INSERT INTO config VALUES ('meta1name', '');
INSERT INTO config VALUES ('meta1value', '');
INSERT INTO config VALUES ('sitename', 'My Gatherer Website');
INSERT INTO config VALUES ('filedir', '');
INSERT INTO config VALUES ('reprivatekey', '');
INSERT INTO config VALUES ('sitedesc', '');
INSERT INTO config VALUES ('plusmyposts', '0');
INSERT INTO config VALUES ('engine', 'disabled');
INSERT INTO config VALUES ('republickey', '');
INSERT INTO config VALUES ('rootuser', '');
INSERT INTO config VALUES ('rootpassword', '');
INSERT INTO config VALUES ('disqus_shortname', '');
INSERT INTO config VALUES ('editor', 'CKEditor');
INSERT INTO config VALUES ('lastfetch', '0');
INSERT INTO config VALUES ('addthis_id', '');
INSERT INTO config VALUES ('siteslogan', '');


--
-- Data for Name: menu; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO menu VALUES (1, 'Home', 'a:3:{s:6:"module";s:7:"default";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', NULL, 0);
INSERT INTO menu VALUES (2, 'Content', NULL, NULL, 1);
INSERT INTO menu VALUES (3, 'Contact', 'a:3:{s:6:"module";s:7:"contact";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', NULL, 2);
INSERT INTO menu VALUES (4, 'About', 'a:4:{s:6:"module";s:7:"article";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";s:6:"params";a:1:{s:2:"id";i:1;}}', NULL, 1);
INSERT INTO menu VALUES (5, 'My First Article', 'a:4:{s:6:"module";s:7:"article";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";s:6:"params";a:1:{s:2:"id";i:2;}}', 2, 0);


--
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO news VALUES (1, 'My First Post', '<p>Welcome!</p>', 'first', 0, 0, 1, NOW(), NULL, 0);


--
-- Name: articles_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: banners_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY banners
    ADD CONSTRAINT banners_pkey PRIMARY KEY (id);


--
-- Name: clients_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_name_key UNIQUE (name);


--
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: config_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY config
    ADD CONSTRAINT config_pkey PRIMARY KEY (key);


--
-- Name: menu_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (id);


--
-- Name: news_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- Name: reader_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY reader
    ADD CONSTRAINT reader_pkey PRIMARY KEY (id);


--
-- Name: guid_u; Type: INDEX; Schema: public; Owner: - 
--

CREATE UNIQUE INDEX guid_u ON reader USING btree (guid);


--
-- Name: client_i; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX client_i ON banners USING btree (client);


--
-- Name: client_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY banners
    ADD CONSTRAINT client_fk FOREIGN KEY (client) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--
