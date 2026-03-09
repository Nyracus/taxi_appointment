TYPE=VIEW
query=select `rpg`.`users`.`username` AS `username`,`rpg`.`characters`.`level` AS `level`,`rpg`.`characters`.`xp` AS `xp`,`rpg`.`characters`.`power_level` AS `power_level` from (`rpg`.`characters` join `rpg`.`users` on(`rpg`.`characters`.`user_id` = `rpg`.`users`.`user_id`)) order by `rpg`.`characters`.`power_level` desc limit 10
md5=c38e7f4db260f60dfe60ef41a7c21c4f
updatable=0
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001744778692061905
create-version=2
source=SELECT username, level, xp, power_level\nFROM Characters\nJOIN Users ON Characters.user_id = Users.user_id\nORDER BY power_level DESC\nLIMIT 10
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_general_ci
view_body_utf8=select `rpg`.`users`.`username` AS `username`,`rpg`.`characters`.`level` AS `level`,`rpg`.`characters`.`xp` AS `xp`,`rpg`.`characters`.`power_level` AS `power_level` from (`rpg`.`characters` join `rpg`.`users` on(`rpg`.`characters`.`user_id` = `rpg`.`users`.`user_id`)) order by `rpg`.`characters`.`power_level` desc limit 10
mariadb-version=100432
