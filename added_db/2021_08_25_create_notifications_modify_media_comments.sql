ALTER TABLE `dotz_dev`.`media_comments`   
	ADD COLUMN `parent_id` INT(11) NOT NULL AFTER `comment`;

CREATE TABLE `dotz_dev`.`notifications` (  
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `app_user_id` INT(11) NOT NULL,
  `content` VARCHAR(300) NOT NULL,
  `read` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) 
) ENGINE=INNODB CHARSET=utf8 COLLATE=utf8_general_ci;
