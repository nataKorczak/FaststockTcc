-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: formulario_produto
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
-- Table structure for table `historico_movimentacoes`
--

DROP TABLE IF EXISTS `historico_movimentacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historico_movimentacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) DEFAULT NULL,
  `nome_produto` varchar(255) NOT NULL,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `unidade` varchar(20) DEFAULT NULL,
  `quantidade_anterior` int(11) DEFAULT NULL,
  `quantidade_nova` int(11) DEFAULT NULL,
  `tipo_movimentacao` enum('ENTRADA','SAIDA','AJUSTE','REMOCAO') NOT NULL,
  `fornecedor` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nome` varchar(255) DEFAULT NULL,
  `data_movimentacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacao` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_historico_data` (`data_movimentacao`),
  KEY `idx_historico_tipo` (`tipo_movimentacao`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico_movimentacoes`
--

LOCK TABLES `historico_movimentacoes` WRITE;
/*!40000 ALTER TABLE `historico_movimentacoes` DISABLE KEYS */;
INSERT INTO `historico_movimentacoes` VALUES (1,0,'coca cola','asgsadfsadsafasdasd','Bebidas','Un',0,17,'ENTRADA','Coca-Cola',27,'testandoteste','2025-10-24 16:03:31','Novo produto adicionado ao estoque'),(2,0,'coca cola','asgsadfsadsafasdasd','Bebidas','Un',17,10,'SAIDA','Coca-Cola',27,'testandoteste','2025-10-24 16:04:04','Produto atualizado no estoque'),(3,0,'coca cola','asgsadfsadsafasdasd','0','Un',10,0,'REMOCAO','Coca-Cola',27,'testandoteste','2025-10-24 16:04:18','Produto removido do estoque');
/*!40000 ALTER TABLE `historico_movimentacoes` ENABLE KEYS */;
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
