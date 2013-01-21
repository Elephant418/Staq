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
