select 
  value, 
  game_id, 
  p.name,
  m.name 
from score s
inner join profile p on (s.user_id = p.user_id)
inner join game g on (s.game_id = g.id)
inner join machine m on (g.machine_id = m.id)
into outfile '/var/lib/mysql-files/parsedscores.csv'
fields terminated by ','
enclosed by '"'
lines terminated by '\n'
;
