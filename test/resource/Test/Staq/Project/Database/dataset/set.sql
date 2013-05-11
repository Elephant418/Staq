CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

TRUNCATE `user`;

INSERT INTO `user` ( `id`, `name` ) VALUES
  ( 1, 'Thomas'  ),
  ( 2, 'Romaric' ),
  ( 3, 'Simon'   ),
  ( 4, 'Sylvain' );



CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` int(11) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

TRUNCATE `article`;

INSERT INTO `article` ( `id`, `title`, `author` ) VALUES
  ( 1, 'Staq' , 1    ),
  ( 2, 'Ubiq' , 1    ),
  ( 3, 'Dataq', NULL );
