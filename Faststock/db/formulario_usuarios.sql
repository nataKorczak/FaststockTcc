-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: formulario
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `email` varchar(110) NOT NULL,
  `username` varchar(45) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `confirmarsenha` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(2,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(3,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(4,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(5,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(6,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(7,'marcelino','marcelino@gmail.com','MJplay','19983246761','testando','testando'),(8,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(9,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(10,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(11,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(12,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(13,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(14,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(15,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(16,'fasdasd','fasadas@asfasd.com','asfasdasd','124124213123','129381293','192471293'),(17,'Leticia Roberta','leticia@gmail.com','letz','34123213213','testando1234','testando1234'),(18,'Leticia Roberta','leticia@gmail.com','letz','34123213213','testando1234','testando1234'),(19,'Leticia Roberta','leticia@gmail.com','letz','34123213213','$2y$10$WjzYayfQfNSV7V4tbrqRSusLmhCotPoJYx8t4QCZveVftndtd8OBy','$2y$10$WjzYayfQfNSV7V4tbrqRSusLmhCotPoJYx8t4QCZveVftndtd8OBy'),(20,'Leticia Roberta','leticia@gmail.com','letz','34123213213','$2y$10$Ep3.us5SogubraSTz.edVO5zT6ikigsd34S.OphxgyD7MdiHEr6AS','$2y$10$Ep3.us5SogubraSTz.edVO5zT6ikigsd34S.OphxgyD7MdiHEr6AS'),(21,'Leticia Roberta','leticia@gmail.com','letz','34123213213','testando1234','testando1234'),(22,'pedrinho','pedro@gmail.com','pedrinhogames','1998424123','pedrin123','pedrin123'),(23,'pedrinho','pedro@gmail.com','pedrinhogames','1998424123','pedrin123','pedrin123'),(24,'testandologin','teste@gmail.com','teste','12321312312','testando','testando'),(25,'rogerin','rogerinteste@gmail.com','rogerinho','19983246763','$2y$10$5rcH.nFngg0BxoWLyN7ZoOIJh8PDY542vSRPTjlbfSiWDAcRApKUa','$2y$10$5rcH.nFngg0BxoWLyN7ZoOIJh8PDY542vSRPTjlbfSiWDAcRApKUa'),(26,'marcelinoteste','marcelinoteste5@gmail.com','marcelinoteste','19983214509','$2y$10$1jjIbFsTleaRjMgOoVqntueebLjfuRsgITYVN0Ig5Q.S7AN2rcfJe','$2y$10$1jjIbFsTleaRjMgOoVqntueebLjfuRsgITYVN0Ig5Q.S7AN2rcfJe'),(27,'testandoteste','testandoteste@gmail.com','testandoteste','19987654312','$2y$10$FMrkL4dT8B0Jb7o5XFMWiOLh5FjuTMKRkXOqNlnGZwlRI2/S71dKa','$2y$10$FMrkL4dT8B0Jb7o5XFMWiOLh5FjuTMKRkXOqNlnGZwlRI2/S71dKa');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-24 14:56:55
