DROP TABLE IF EXISTS `models`;
DROP TABLE IF EXISTS `relations`;
DROP TABLE IF EXISTS `indexs`;
DROP TABLE IF EXISTS `model_archives`;

CREATE TABLE IF NOT EXISTS `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `type_version` int(11) NOT NULL,
  `attributes` text NOT NULL,
  `attributes_version` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id_1` int(11) NOT NULL,
  `model_type_1` varchar(255) NOT NULL,
  `model_id_2` int(11) NOT NULL,
  `model_type_2` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `indexs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `model_archives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_type_version` int(11) NOT NULL,
  `model_attributes` text NOT NULL,
  `model_attributes_version` int(11) NOT NULL,
  `ip_version` varchar(50) NOT NULL,
  `date_version` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
