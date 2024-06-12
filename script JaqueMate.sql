-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema jaquemate
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema jaquemate
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `jaquemate` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `jaquemate` ;

-- -----------------------------------------------------
-- Table `jaquemate`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jaquemate`.`usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `nombreUsuario` VARCHAR(40) NOT NULL,
  `correoElectronico` VARCHAR(60) NOT NULL,
  `password` CHAR(60) NOT NULL,
  `fechaRegistro` DATE NOT NULL,
  `ultimoAcceso` DATE NULL DEFAULT NULL,
  `piezaFavorita` VARCHAR(20) NOT NULL DEFAULT 'Moderno',
  `admin` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idUsuario`),
  UNIQUE INDEX `nombreUsuario` (`nombreUsuario` ASC) VISIBLE,
  UNIQUE INDEX `correo` (`correoElectronico` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `jaquemate`.`aperturas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jaquemate`.`aperturas` (
  `idApertura` INT NOT NULL AUTO_INCREMENT,
  `idUsuario` INT NULL DEFAULT NULL,
  `nombreApertura` VARCHAR(50) NOT NULL,
  `PGN` VARCHAR(10000) NULL DEFAULT NULL,
  PRIMARY KEY (`idApertura`),
  INDEX `idUsuario` (`idUsuario` ASC) VISIBLE,
  CONSTRAINT `aperturas_ibfk_1`
    FOREIGN KEY (`idUsuario`)
    REFERENCES `jaquemate`.`usuario` (`idUsuario`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 29
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `jaquemate`.`baneos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jaquemate`.`baneos` (
  `idBaneo` INT NOT NULL AUTO_INCREMENT,
  `nombreUsuario` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`idBaneo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `jaquemate`.`datos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jaquemate`.`datos` (
  `idDato` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(500) NULL DEFAULT NULL,
  PRIMARY KEY (`idDato`))
ENGINE = InnoDB
AUTO_INCREMENT = 67
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

--
-- Dumping data for table `datos`
--

LOCK TABLES `datos` WRITE;
/*!40000 ALTER TABLE `datos` DISABLE KEYS */;
INSERT INTO `datos` VALUES (1,'El jugador de ajedrez con más títulos mundiales es el legendario Garry Kasparov, quien ganó el Campeonato Mundial de Ajedrez en 1985, 1986, y 1990.'),(2,'El término Jaque Mate proviene del persa Shah Mat, que significa El Rey está atrapado.'),(3,'El ajedrez moderno se originó en el sur de Europa durante el siglo XV.'),(4,'El campeonato mundial de ajedrez se lleva a cabo desde 1886, cuando Wilhelm Steinitz se convirtió en el primer campeón.'),(5,'El Gran Maestro más joven en la historia del ajedrez es Abhimanyu Mishra, quien obtuvo el título a los 12 años y 4 meses.'),(6,'Anatoly Karpov ostenta el récord de la partida más larga de ajedrez, con una duración de 193 movimientos.'),(7,'En 1997, IBM\'s Deep Blue se convirtió en la primera computadora en derrotar a un campeón mundial de ajedrez, Garry Kasparov.'),(8,'El ajedrez es reconocido como un deporte por el Comité Olímpico Internacional.'),(9,'El enroque es el único movimiento en el ajedrez donde dos piezas se mueven al mismo tiempo: el rey y una torre.'),(10,'El ajedrez es uno de los juegos más estudiados y analizados en el mundo, con una inmensa cantidad de teoría y estrategias desarrolladas a lo largo de los siglos.'),(11,'En 2017, Hou Yifan se convirtió en la primera mujer en ganar la sección abierta del prestigioso Torneo de Ajedrez Tradewise Gibraltar.'),(12,'El título de Gran Maestro Internacional es la máxima distinción otorgada por la FIDE (Federación Internacional de Ajedrez) y es reconocido internacionalmente.'),(13,'El ajedrez es conocido como el \'juego real\', ya que se cree que simula una batalla entre dos reinos.'),(14,'El ajedrez se juega en todas partes del mundo y se estima que hay más de 600 millones de jugadores en todo el mundo.'),(15,'El ajedrez tiene una variante de tres jugadores llamada \'ajedrez 960\' o \'Fischer Random\', en honor a Bobby Fischer, que cambia la posición inicial de las piezas.'),(16,'El ajedrez ha sido utilizado como herramienta educativa en muchas escuelas y universidades para mejorar el pensamiento crítico y la resolución de problemas.'),(17,'El ajedrez se ha utilizado en terapias ocupacionales para ayudar a mejorar la memoria, la concentración y las habilidades cognitivas.'),(18,'En algunas culturas, el ajedrez se considera más que un juego; es visto como una forma de arte, ciencia y filosofía.'),(19,'La partida de ajedrez más antigua registrada se jugó en el siglo X entre un árabe y un visitante extranjero.'),(20,'En el ajedrez por correspondencia, las partidas pueden durar años, ya que los jugadores envían sus movimientos por correo postal o electrónico.'),(21,'El ajedrez es un juego que ofrece infinitas posibilidades y desafíos, lo que lo convierte en una pasión para muchos aficionados en todo el mundo.'),(22,'En la antigua India, el ajedrez se conocía como \'Chaturanga\', que significa \'cuatro miembros\', refiriéndose a las cuatro divisiones del ejército: infantería, caballería, elefantes y carros.'),(23,'El ajedrez fue introducido en Europa durante la conquista árabe de España en el siglo X.'),(24,'La pieza más poderosa en el ajedrez, la dama, solía tener movimientos más limitados y fue conocida como \'alferza\' en el pasado.'),(25,'El rey en el ajedrez originalmente se llamaba \'Shah\', que significa \'rey\' en persa.'),(26,'En 1972, el enfrentamiento entre Bobby Fischer y Boris Spassky en el Campeonato Mundial de Ajedrez se convirtió en un evento mediático global, conocido como el \'Match del Siglo\'.'),(27,'El ajedrez ha sido utilizado como tema en películas, programas de televisión, libros y obras de teatro.'),(28,'El primer libro impreso sobre ajedrez, \'El juego de ajedrez\', fue publicado en 1474 por William Caxton.'),(29,'El ajedrez ha sido utilizado en la informática como un desafío para desarrollar algoritmos de inteligencia artificial y programas de ajedrez.'),(30,'En algunos países, como Rusia, el ajedrez es parte del plan de estudios escolar y se considera una actividad extracurricular importante.'),(31,'En 1996, Deep Blue, la precursora de la máquina que derrotó a Kasparov, perdió ante él en un enfrentamiento previo.'),(32,'El ajedrez es uno de los juegos más antiguos que aún se juega en su forma original.'),(33,'El ajedrez ha sido utilizado como metáfora en la literatura y el cine para representar estrategias y conflictos.'),(34,'El término \'tablas\' en ajedrez se refiere a un empate, donde ninguno de los jugadores gana.'),(35,'En el ajedrez, existen varias aperturas reconocidas y nombradas en honor a jugadores famosos o lugares donde se jugaron por primera vez.'),(36,'El ajedrez ha sido utilizado en psicología para estudiar la toma de decisiones y el pensamiento estratégico.'),(37,'El ajedrez ha sido objeto de estudios en neurociencia para comprender mejor el funcionamiento del cerebro durante la resolución de problemas.'),(38,'El ajedrez se considera un juego de suma cero, lo que significa que el éxito de un jugador se produce a expensas del otro.'),(39,'El ajedrez ha sido utilizado como herramienta terapéutica en la rehabilitación de lesiones cerebrales y trastornos neurológicos.'),(40,'En 1851, se organizó el primer torneo internacional de ajedrez en Londres, donde Adolf Anderssen emergió como el ganador.'),(41,'El ajedrez ha sido objeto de estudio en campos como la sociología, la antropología y la teoría de juegos.'),(42,'El ajedrez ha sido utilizado en la enseñanza de estrategia militar y tácticas de combate.'),(43,'En algunos países, existen organizaciones dedicadas a la promoción y el desarrollo del ajedrez como deporte y actividad cultural.'),(44,'El ajedrez ha sido utilizado en la investigación científica para estudiar el comportamiento humano y la toma de decisiones bajo presión.'),(45,'El ajedrez se originó en la India durante el siglo VI d.C.'),(46,'El ajedrez se menciona por primera vez en textos persas del siglo VI d.C., donde se le llamaba \'chatrang\'.'),(47,'Durante la Edad Media, el ajedrez se convirtió en un pasatiempo popular entre la nobleza y la realeza.'),(48,'El ajedrez era conocido como el \'juego de reyes\' y se enseñaba en las cortes como una forma de educación militar y estratégica.'),(49,'En el siglo XV, se estandarizó el movimiento de las piezas y las reglas del ajedrez en Europa.'),(50,'En el siglo XIX, se establecieron los primeros clubes de ajedrez y se organizaron los primeros torneos internacionales.'),(51,'En el siglo XX, el ajedrez se convirtió en un deporte profesional con la formación de la FIDE (Federación Internacional de Ajedrez) en 1924.'),(52,'En 2013, Magnus Carlsen se convirtió en el campeón mundial de ajedrez más joven de la historia a los 22 años.'),(53,'El ajedrez se jugaba originalmente con dados en la antigua India, donde cada resultado determinaba un movimiento posible.'),(54,'El primer tratado conocido sobre ajedrez, \'El arte de la guerra del ajedrez\', fue escrito por el persa al-Shatranj.'),(55,'La palabra \'ajedrez\' proviene del persa \'shāh\' que significa \'rey\' y \'māt\' que significa \'derrotado\' o \'muerto\', lo que se traduce literalmente como \'el rey está muerto\'.'),(56,'En el siglo XV, se introdujeron algunas de las reglas más importantes del ajedrez moderno, como el movimiento de la dama y el enroque.'),(57,'En 1984, se produjo una división en el campeonato mundial de ajedrez, lo que llevó a la existencia de dos campeones rivales, Anatoly Karpov y Garry Kasparov.'),(58,'El ajedrez ha sido incluido en los programas deportivos de los Juegos Olímpicos de la Juventud desde 2010, destacando su estatus como deporte reconocido a nivel internacional.'),(59,'Se estima que en todo el mundo hay alrededor de 500 millones de personas que juegan al ajedrez, lo que le convierte en el juego más popular.'),(60,'La primera versión del ajedrez surgió en Persia en torno al año 600 a.C.'),(61,'Según la Fundación Estadounidense para el Ajedrez, hay 169.518.829.100.544.000.000.000.000.000 formas de jugar los primeros 10 movimientos de una partida de ajedrez.'),(62,'El diseño de las piezas del ajedrez tal y como las conocemos hoy en día es obra del inglés Howard Staunton.'),(63,'Un sacerdote inventó el tablero de ajedrez plegable.');
/*!40000 ALTER TABLE `datos` ENABLE KEYS */;
UNLOCK TABLES;

-- -----------------------------------------------------
-- Table `jaquemate`.`problemas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `jaquemate`.`problemas` (
  `idProblema` INT NOT NULL AUTO_INCREMENT,
  `fen` VARCHAR(255) NOT NULL,
  `solucion` TEXT NOT NULL,
  PRIMARY KEY (`idProblema`))
ENGINE = InnoDB
AUTO_INCREMENT = 41
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


--
-- Dumping data for table `problemas`
--

LOCK TABLES `problemas` WRITE;
/*!40000 ALTER TABLE `problemas` DISABLE KEYS */;
INSERT INTO `problemas` VALUES (1,'3q1rk1/5pbp/5Qp1/8/8/2B5/5PPP/6K1 w - - 0 1','f6-g7'),(2,'3q1rk1/5pbp/5Qp1/8/8/2B5/5PPP/6K1 w - - 0 1','f6-g7'),(3,'2r2rk1/2q2p1p/6pQ/4P1N1/8/8/PPP5/2KR4 w - - 0 1','h6-h7'),(4,'r2q1rk1/pp1p1p1p/5PpQ/8/4N3/8/PP3PPP/R5K1 w - - 0 1','h6-g7'),(5,'6r1/7k/2p1pPp1/3p4/8/1R6/5PPP/5K2 w - - 0 1','b3-h3'),(6,'1r4k1/1q3p2/5Bp1/8/8/8/PP6/1K5R w - - 0 1','h1-h8'),(7,'r4rk1/5p1p/8/8/8/8/1BP5/2KR4 w - - 0 1','d1-g1'),(8,'4r2k/4r1p1/6p1/8/2B5/8/1PP5/2KR4 w - - 0 1','d1-h1'),(9,'8/2r1N1pk/8/8/8/2q2p2/2P5/2KR4 w - - 0 1','d1-h1'),(10,'r7/4KNkp/8/8/b7/8/8/1R6 w - - 0 1','b1-g1'),(11,'2kr4/3n4/2p5/8/5B2/8/6PP/5B1K w - - 0 1','f1-a6'),(12,'r1b1kb1r/5ppp/8/6B1/8/8/5PPP/3R3K w - - 0 1','d1-d8'),(13,'r4rk1/p6p/1n6/6N1/3B4/3B4/6PP/7K w - - 0 1','d3-h7'),(14,'r1bqk1nr/pppp1ppp/2n5/2b1p3/2B1P3/5Q2/PPPP1PPP/RNB1K1NR w KQkq - 0 1','f3-f7'),(15,'rnbqkbnr/ppppp2p/5p2/6p1/3PP3/8/PPP2PPP/RNBQKBNR w KQkq - 0 1','d1-h5'),(16,'6k1/5ppp/r1p5/3b4/8/1pB5/1Pr2PPP/3RR1K1 w - - 0 1','e1-e8'),(17,'rnbq1rk1/ppp1nppp/3bp3/3p3Q/3P4/3BPN2/PPP2PPP/RNB1K2R w KQ - 0 1','h5-h7'),(18,'6k1/p1p2rpp/1q6/2p5/4P3/PQ6/1P4PP/3R3K w - - 0 1','d1-d8'),(19,'rnb4k/p5pp/8/4N3/8/1B6/PPP5/2K4R w - - 0 1','e5-g6'),(20,'5k2/4np2/5N2/2B5/8/8/8/6RK w - - 0 1','g1-g8'),(23,'2kr4/3p4/8/4B3/8/3B4/3K4/8 w - - 0 1','d3-a6'),(24,'2kr4/8/8/8/Q7/6B1/6K1/8 w - - 0 1','a5-c7'),(25,'3r4/1pk5/3pP3/2N5/8/8/8/2R4K w - - 0 1','c5-d7'),(26,'8/8/3Rp3/1P2k3/3Np3/2B5/8/5RK1 w - - 0 1','d4-f5'),(27,'5rk1/8/6P1/8/7Q/8/6K1/8 w - - 0 1','h4-h7'),(28,'8/5B2/4p3/4kp2/3Np3/8/4P3/Q2R2K1 w - - 0 1','d4-e6'),(29,'7k/8/8/5BN1/8/8/8/6RK w - - 0 1','f5-f7'),(30,'4kq2/3p4/4B1P1/B7/8/8/8/4R2K w - - 0 1','e6-f7'),(31,'5rk1/8/7P/6N1/8/8/1B6/2K5 w - - 0 1','h6-h7'),(32,'5nk1/6b1/8/8/8/2Q5/7K/6R1 w - - 0 1','c3-g7'),(33,'3bk3/R3p3/3P4/8/8/8/8/1R4K1 w - - 0 1','d6-d7'),(34,'k1r5/8/P7/8/1Q6/8/8/6K1 w - - 0 1','b4-b7'),(35,'2kr4/2p3R1/3P4/8/8/8/8/1R4K1 w - - 0 1','g7-c7'),(36,'6rk/7p/8/8/4B3/8/8/K6R w - - 0 1','h1-h7'),(37,'6rk/6n1/5R2/8/8/8/8/6K1 w - - 0 1','f6-h6'),(38,'1rkr2R1/1pnb4/8/8/3Q4/8/8/3R2K1 w - - 0 1','d4-d7'),(39,'3qkn2/1R6/4N3/8/8/8/8/5K2 w - - 0 1','e6-g7'),(40,'4b3/3k4/4q3/B7/5Q2/8/8/6K1 w - - 0 1','f4-c7');
/*!40000 ALTER TABLE `problemas` ENABLE KEYS */;
UNLOCK TABLES;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
