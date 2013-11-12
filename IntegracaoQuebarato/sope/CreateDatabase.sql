SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `quebarato` ;
CREATE SCHEMA IF NOT EXISTS `quebarato` DEFAULT CHARACTER SET cp850 ;
USE `quebarato` ;

-- -----------------------------------------------------
-- Table `quebarato`.`Anuncio_Veiculo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `quebarato`.`Anuncio_Veiculo` ;

CREATE  TABLE IF NOT EXISTS `quebarato`.`wtb_integracao_quebarato` (
  `cd_integracao` INT NULL ,
  `id_veiculo` INT NULL ,
  `id_quebarato` int NULL ,
  PRIMARY KEY (`integracao`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = cp850;


-- -----------------------------------------------------
-- procedure AtualizaAnuncio
-- -----------------------------------------------------

USE `quebarato`;
DROP procedure IF EXISTS `quebarato`.`AtualizaAnuncio`;

DELIMITER $$
USE `quebarato`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AtualizaAnuncio`(
p_veiculoid varchar(20), 
p_anuncioid varchar(20)
)
BEGIN
    insert into Anuncio_Veiculo 
    (idVeiculo, idanuncio)
    values 
    (p_veiculoid, p_anuncioid);
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure ValidaAnuncio
-- -----------------------------------------------------

USE `quebarato`;
DROP procedure IF EXISTS `quebarato`.`ValidaAnuncio`;

DELIMITER $$
USE `quebarato`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidaAnuncio`(
p_veiculoid varchar(20)
)
BEGIN
    select t.idVeiculo, t.idanuncio 
    from Anuncio_Veiculo t 
    where t.idVeiculo = p_veiculoid;
END$$

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
