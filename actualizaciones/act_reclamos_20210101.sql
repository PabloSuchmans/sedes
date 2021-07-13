CREATE TABLE `usuarios` ( 
  `nro_usuario` INT NOT NULL AUTO_INCREMENT , 
  `nombre` VARCHAR(100) NOT NULL , 
  `email` VARCHAR(320) NOT NULL , 
  `inactivo` BOOLEAN NOT NULL , PRIMARY KEY (`nro_usuario`));