TYPE=VIEW
query=select `rpg`.`characters`.`char_id` AS `char_id`,`rpg`.`characters`.`name` AS `name`,case when `rpg`.`characters`.`power_level` >= 95 then \'SSS\' when `rpg`.`characters`.`power_level` >= 85 then \'SS\' when `rpg`.`characters`.`power_level` >= 70 then \'S\' when `rpg`.`characters`.`power_level` >= 50 then \'A\' when `rpg`.`characters`.`power_level` >= 35 then \'B\' when `rpg`.`characters`.`power_level` >= 20 then \'C\' when `rpg`.`characters`.`power_level` >= 10 then \'D\' else \'F\' end AS `rank` from `rpg`.`characters`
md5=e11190c6afb26f558141caf97e12b32f
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001744778645481127
create-version=2
source=SELECT char_id, name,\n  CASE\n    WHEN power_level >= 95 THEN \'SSS\'\n    WHEN power_level >= 85 THEN \'SS\'\n    WHEN power_level >= 70 THEN \'S\'\n    WHEN power_level >= 50 THEN \'A\'\n    WHEN power_level >= 35 THEN \'B\'\n    WHEN power_level >= 20 THEN \'C\'\n    WHEN power_level >= 10 THEN \'D\'\n    ELSE \'F\'\n  END AS rank\nFROM Characters
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_general_ci
view_body_utf8=select `rpg`.`characters`.`char_id` AS `char_id`,`rpg`.`characters`.`name` AS `name`,case when `rpg`.`characters`.`power_level` >= 95 then \'SSS\' when `rpg`.`characters`.`power_level` >= 85 then \'SS\' when `rpg`.`characters`.`power_level` >= 70 then \'S\' when `rpg`.`characters`.`power_level` >= 50 then \'A\' when `rpg`.`characters`.`power_level` >= 35 then \'B\' when `rpg`.`characters`.`power_level` >= 20 then \'C\' when `rpg`.`characters`.`power_level` >= 10 then \'D\' else \'F\' end AS `rank` from `rpg`.`characters`
mariadb-version=100432
