CREATE TABLE users (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `dt_nascimento` DATE NULL,
  `password` VARCHAR(255) NULL,
  `perfil_img` TEXT NULL,
  `telefone` VARCHAR(14) NULL,
  PRIMARY KEY (`id`));
