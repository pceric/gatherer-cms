-- phpMyAdmin SQL Dump
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `comments` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `moddate` datetime DEFAULT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `tags`, `comments`, `published`, `pubdate`, `moddate`, `hits`) VALUES
(1, 'About', '<p>All about me.</p>', 'about', 0, 1, NOW(), NULL, 0),
(2, 'My First Article', '<p>The first page of my article...<!--pagebreak-->..and now the 2nd!</p>', 'first, article', 0, 1, NOW(), NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client` int(10) unsigned NOT NULL,
  `size` varchar(16) NOT NULL DEFAULT '468x60',
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `code` text,
  `clicks` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `impressions` int(10) unsigned NOT NULL DEFAULT '0',
  `startdate` datetime NOT NULL,
  `enddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `contact` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `extrainfo` text,
  `createdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`key`, `value`) VALUES
('editor', 'CKEditor'),
('engine', 'disabled'),
('fetchfreq', '1200'),
('filedir', ''),
('googlefeed', ''),
('imagedir', ''),
('lastfetch', '0'),
('meta1name', ''),
('meta1value', ''),
('reprivatekey', ''),
('republickey', ''),
('rootpassword', ''),
('rootuser', ''),
('schema', '1'),
('siteauthor', 'Webmaster'),
('sitecontact', ''),
('sitedesc', ''),
('sitekeywords', ''),
('sitename', 'My Gatherer Website'),
('siteslogan', ''),
('sitetheme', 'default'),
('siteURL', ''),
('disqus_shortname', ''),
('addthis_id', '');

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL DEFAULT '',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `link` text,
  `parent` int(10) unsigned DEFAULT NULL,
  `weight` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `weight` (`weight`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `link`, `parent`, `weight`) VALUES
(1, 'Home', 'a:3:{s:6:"module";s:7:"default";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', NULL, 0),
(2, 'Content', NULL, NULL, 0),
(3, 'About', 'a:4:{s:6:"module";s:7:"article";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";s:6:"params";a:1:{s:2:"id";i:1;}}', NULL, 1),
(4, 'Contact', 'a:3:{s:6:"module";s:7:"contact";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', NULL, 2),
(5, 'Article', 'a:4:{s:6:"module";s:7:"article";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";s:6:"params";a:1:{s:2:"id";i:2;}}', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `comments` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) unsigned DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `moddate` datetime DEFAULT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `tags`, `comments`, `sticky`, `published`, `pubdate`, `moddate`, `hits`) VALUES
(1, 'My First Post', '<p>Welcome!</p>', 'first', 0, 0, 1, NOW(), NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reader`
--

CREATE TABLE IF NOT EXISTS `reader` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `summary` text NOT NULL,
  `source` varchar(128) NOT NULL DEFAULT '',
  `tags` varchar(255) DEFAULT NULL,
  `annotation` text,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
