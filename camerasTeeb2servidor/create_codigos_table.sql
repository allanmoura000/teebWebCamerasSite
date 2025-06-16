CREATE TABLE IF NOT EXISTS `codigos_verificacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_expiracao` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `data_expiracao` (`data_expiracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 