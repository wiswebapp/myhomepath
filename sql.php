<?
/**
 * @table Users 
 */
CREATE TABLE `medicineapp`.`med_users` ( `id` INT NOT NULL AUTO_INCREMENT , `name` INT NOT NULL , `phone` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , `accessToken` VARCHAR(255) NOT NULL , `isActive` ENUM('Yes','No') NOT NULL DEFAULT 'No' , `isLogin` ENUM('Yes','No') NOT NULL DEFAULT 'No' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `med_users` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

