CREATE TEMPORARY TABLE config_old SELECT * FROM config;
DROP TABLE config;
CREATE TABLE config (
    `key` VARCHAR(255) NOT NULL,
    `value` VARCHAR(255),
    PRIMARY KEY(`key`)
);
INSERT INTO config VALUES('schema', '1');
INSERT INTO config VALUES('rootuser', '');
INSERT INTO config VALUES('rootpassword', '');
INSERT INTO config VALUES('engine', 'disabled');
INSERT INTO config VALUES('fetchfreq', (SELECT fetchfreq FROM config_old));
INSERT INTO config VALUES('sitename', (SELECT sitename FROM config_old));
INSERT INTO config VALUES('siteauthor', (SELECT siteauthor FROM config_old));
INSERT INTO config VALUES('sitecontact', (SELECT sitecontact FROM config_old));
INSERT INTO config VALUES('sitedesc', (SELECT sitedesc FROM config_old));
INSERT INTO config VALUES('sitekeywords', (SELECT sitekeywords FROM config_old));
INSERT INTO config VALUES('siteslogan', (SELECT siteslogan FROM config_old));
INSERT INTO config VALUES('sitetheme', 'default');
INSERT INTO config VALUES('editor', (SELECT editor FROM config_old));
INSERT INTO config VALUES('lastfetch', (SELECT lastfetch FROM config_old));
INSERT INTO config VALUES('imagedir', (SELECT imagedir FROM config_old));
INSERT INTO config VALUES('republickey', (SELECT publickey FROM recaptcha));
INSERT INTO config VALUES('reprivatekey', (SELECT privatekey FROM recaptcha));
ALTER TABLE banners ADD startdate DATETIME NOT NULL, ADD enddate DATETIME DEFAULT NULL;
ALTER TABLE clients ADD contact varchar(128), ADD email varchar(128), ADD extrainfo TEXT, ADD createdate DATETIME NOT NULL;
ALTER TABLE reader ADD pubdate DATETIME NOT NULL;
UPDATE reader SET pubdate = FROM_UNIXTIME(timestamp);
ALTER TABLE reader DROP timestamp;
DROP TABLE recaptcha;
DELETE FROM menu;
ALTER TABLE menu MODIFY link TEXT DEFAULT NULL, MODIFY weight SMALLINT(5) NOT NULL DEFAULT '0';
INSERT INTO menu (`id`, `name`, `link`, `parent`, `weight`) VALUES
(1, 'Home', 'a:3:{s:6:"module";s:7:"default";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', NULL, 0),
(2, 'Content', NULL, NULL, 0),
(3, 'About', 'a:4:{s:6:"module";s:7:"article";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";s:6:"params";a:1:{s:2:"id";i:1;}}', NULL, 1),
(4, 'Contact', 'a:3:{s:6:"module";s:7:"contact";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', NULL, 2);

