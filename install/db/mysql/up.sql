SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `minter_pay_transactions`;
CREATE TABLE `minter_pay_transactions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ENTITY_ID` int(11) NOT NULL COMMENT 'Привязка к товару/услуге',
  `ENTITY_NAME` varchar(255) DEFAULT NULL COMMENT 'Название товара',
  `WALLET` varchar(255) NOT NULL COMMENT 'Кошелек покупателя',
  `USER_ID` int(11) DEFAULT NULL COMMENT 'ID пользователя',
  `PRICE` int(11) NOT NULL COMMENT 'Сумма покупки',
  `DATE_CREATE` date NOT NULL COMMENT 'Дата',
  `STATUS` varchar(255) DEFAULT NULL COMMENT 'Статус транзакции',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `minter_pay_transactions`;