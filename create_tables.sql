# username: bapaserver
# password: wizzzzzard
use baparocks;

-- DROP TABLE machinepool;
DROP TABLE sessionuser;
DROP TABLE seasonuser;
DROP TABLE matchuser;
DROP TABLE score;
DROP TABLE queuegame;
DROP TABLE machinestatus;
DROP TABLE game;
DROP TABLE `match`;
DROP TABLE session;
DROP TABLE season;
DROP TABLE machine;
DROP TABLE location;

CREATE TABLE location (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    address VARCHAR(255) NOT NULL UNIQUE,
    contact VARCHAR(255) NOT NULL UNIQUE,
    notes VARCHAR(255) NOT NULL UNIQUE,
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE machine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    abbreviation VARCHAR(15) NOT NULL,
    ipdb_id INT COMMENT 'ID number on IPDB',
    location_id INT NOT NULL,
    FOREIGN KEY location_key (location_id) REFERENCES location(id),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE season (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status INT NOT NULL, -- 0 = NOT_STARTED, 
             -- 1 = STARTED, 2 = REGULAR WEEKS COMPLETED
             -- 3 = PLAYOFFS COMPLETED
    name VARCHAR(255) NOT NULL,
    previous_season_id INT,
    FOREIGN KEY previous_season_key (previous_season_id) REFERENCES season(id),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE session (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type INT NOT NULL, -- 1 = regular season, 2 = playoff
    status INT NOT NULL, -- 0 = NOT_STARTED, 1 = STARTED, 2 = COMPLETED
    playoff_division VARCHAR(20),
    season_id INT NOT NULL,
    FOREIGN KEY season_key (season_id) REFERENCES season(id),
    location_id INT NOT NULL,
    FOREIGN KEY location_key (location_id) REFERENCES location(id),
    name VARCHAR(255) NOT NULL,
    date int(11),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE `match` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    FOREIGN KEY session_key (session_id) REFERENCES session(id),
    code VARCHAR(255) NOT NULL, -- group, round, etc.
    format INT NOT NULL, -- 1 = best of 3, 3 = 3-player, 4 = 4-player, 5 = best of 5, 7 = best of 7
    status INT NOT NULL, -- 0 = AWAITING PLAYERS, 2 = IN PROGRESS, 3 = COMPLETED
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE game (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    FOREIGN KEY match_key (match_id) REFERENCES `match`(id),
    machine_id INT, -- NULL = not selected yet
    FOREIGN KEY machine_key (machine_id) REFERENCES machine(id),
    number INT NOT NULL, -- 1-4 for regular season, 1-3 for best of 3, 1-5 for best of 5, etc.
    status INT NOT NULL, -- 0 = AWAITING MASTER SELECTION, 1 = AWAITING MACHINE OR PLAYER ORDER SELECTION,
                         -- 2 = AWAITING KNOWN MACHINE (playoffs) , 3 = IN PROGRESS, 4 = COMPLETED
                         -- 5 = DISQUALIFIED (e.g., broken machine)
                         -- 6 = AWAITING UNKNOWN MACHINE (regular season)
    player_order_selector INT,
    FOREIGN KEY (player_order_selector) REFERENCES user(id),
    machine_selector INT,
    FOREIGN KEY (machine_selector) REFERENCES user(id),
    master_selector INT COMMENT 'Machine/Player order selector', -- one who selects machine or player order
    FOREIGN KEY (master_selector) REFERENCES user(id),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE machinestatus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status INT NOT NULL, -- 1 = AVAILABLE, 2 = IN_PLAY, 3 = BROKEN, 4 = GONE
    machine_id INT NOT NULL,
    FOREIGN KEY machine_key (machine_id) REFERENCES machine(id),
    game_id INT, -- only meaningful if status == 2;
    FOREIGN KEY game_key (game_id) REFERENCES game(id),
    recorder_id INT NOT NULL COMMENT 'User who changed status',
    FOREIGN KEY (recorder_id) REFERENCES user(id),
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
        -- since we need fractions for this one.
);

-- a list of games that are waiting for a specific machine
-- used for playoffs only, as regular weeks don't have that.
-- items should be deleted as they free up.
CREATE TABLE queuegame (
    id INT AUTO_INCREMENT PRIMARY KEY,
    machine_id INT NOT NULL,
    FOREIGN KEY machine_key (machine_id) REFERENCES machine(id),
    game_id INT NOT NULL,
    FOREIGN KEY game_key (game_id) REFERENCES game(id),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE score (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playernumber INT NOT NULL, -- 1-4
    value BIGINT, -- NULL = unscored
    matchpoints INT, -- NULL = unscored
    forfeit INT NOT NULL, -- 0 = not forfeit, 1 = forfeit
    verified INT NOT NULL, -- 0 = unverified, 1 = verified
    game_id INT NOT NULL,
    FOREIGN KEY (game_id) REFERENCES game(id),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id),
    recorder_id INT,
    FOREIGN KEY (recorder_id) REFERENCES user(id),
    verifier_id INT,
    FOREIGN KEY (verifier_id) REFERENCES user(id),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE matchuser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    starting_playernum INT NOT NULL, -- useful for playoff sources
    matchrank INT, -- rank within match (1st place, 2nd, etc.) Ties are indeterminate.
    game_count INT NOT NULL,
    opponent_count INT NOT NULL, -- includes forfeit opponents
    forfeit_opponent_count INT NOT NULL DEFAULT 0, -- will be subtracted later
    match_id INT NOT NULL,
    FOREIGN KEY match_key (match_id) REFERENCES `match`(id),
    user_id INT NOT NULL,
    FOREIGN KEY user_key (user_id) REFERENCES user(id),
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE sessionuser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notes VARCHAR(255),
    status INT NOT NULL, -- 1 = present at start, 2 = coming late
    user_id INT NOT NULL,
    FOREIGN KEY user_key (user_id) REFERENCES user(id),
    session_id INT NOT NULL,
    FOREIGN KEY session_key (session_id) REFERENCES session(id),
    recorder_id INT NOT NULL,
    FOREIGN KEY (recorder_id) REFERENCES user(id),
    previous_performance INT, -- in matchpoints.
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE seasonuser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notes VARCHAR(255),
    matchpoints INT NOT NULL, -- add all games in match (including bonus)
    game_count INT NOT NULL, -- only effective games in season
    opponent_count INT NOT NULL, -- only effective opponents in season, including forfeits
    forfeit_opponent_count INT NOT NULL DEFAULT 0, -- will be subtracted from opponent_count to calculate mpo
    match_count INT NOT NULL, -- only effective matches in season
    dues INT NOT NULL, -- 0 = not paid, 1 = paid
    playoff_division VARCHAR(20), -- NULL = unassgned; 'A', 'B', 'DQ'
    playoff_rank INT, -- NULL = unassigned, otherwise within playoff

    surplus_matchpoints INT NOT NULL DEFAULT 0, -- matchpoints that aren't counted "drop 2 worst weeks"
    surplus_mpo_matchpoints INT NOT NULL DEFAULT 0, -- opponents that aren't counted "drop 2 worst weeks"
    surplus_mpo_opponent_count INT NOT NULL DEFAULT 0, -- opponents that aren't counted "drop 2 worst weeks"

    playoff_matchpoints INT AS (matchpoints - surplus_matchpoints) STORED,
    playoff_mpo_matchpoints INT AS (matchpoints - surplus_mpo_matchpoints - forfeit_opponent_count) STORED,
    playoff_mpo_opponent_count INT AS (opponent_count - surplus_mpo_opponent_count - forfeit_opponent_count) STORED,

    mpg DOUBLE AS (IF(game_count=0,NULL,(matchpoints / game_count))) STORED,
    mpo DOUBLE AS (IF(game_count=0,NULL,(playoff_mpo_matchpoints / playoff_opponent_count))) STORED,

    user_id INT NOT NULL,
    FOREIGN KEY user_key (user_id) REFERENCES user(id),
    season_id INT NOT NULL,
    FOREIGN KEY season_key (season_id) REFERENCES season(id),
    previous_season_rank DOUBLE, -- 1 = 1st place, 2 = 2nd place, etc.
    created_at int(11),
    updated_at int(11)
);

-- maybe this should be a view because we can deduce it from
-- other tables
/*
CREATE TABLE machinepool (
    pick_count INT NOT NULL, -- at least 1
    machine_id INT NOT NULL,
    user_id INT NOT NULL,
    session_id INT NOT NULL,
    PRIMARY KEY (machine_id, user_id, session_id),
    FOREIGN KEY machine_key (machine_id) REFERENCES machine(id),
    FOREIGN KEY user_key (user_id) REFERENCES user(id),
    FOREIGN KEY session_key (session_id) REFERENCES session(id),
    created_at int(11),
    updated_at int(11)
);
*/

CREATE OR REPLACE VIEW machinepool AS (
SELECT
  machine_selector as user_id,
  machine_id,
  session_id,
  COUNT(*) as pick_count
FROM
  game
  JOIN `match` ON game.match_id = match.id
  JOIN session ON match.session_id = session.id
WHERE
  machine_id IS NOT NULL
  AND machine_selector IS NOT NULL
  AND game.status != 5
GROUP BY
  machine_id, machine_selector, session_id
);

CREATE OR REPLACE VIEW playoffresults AS (
SELECT
  su.session_id,
  su.user_id,
  su.id as sessionuser_id,
--  su.status as sessionuser_status,
  mu1.id as matchuser_id,
--  mu1.starting_playernum,
  mu1.match_id,
--  mu1.matchrank,
  m.code as code,
--  m.format as format,
  m.status as match_status,
--  eg.seed_p1,
--  eg.seed_p2,
  if (m.status = 3,
      if (mu1.matchrank = 1, eg.seed_p1 + 1, eg.seed_p2 + 1),
      eg.seed_min) as seed_min,
  if (m.status = 3,
      if (mu1.matchrank = 1, eg.seed_p1 + 1, eg.seed_p2 + 1),
      eg.seed_max) as seed_max,
  if (m.status = 3, 
      if (mu1.matchrank = 1, eg.seed_p1 + 1, eg.seed_p2 + 1),
      if (mu1.starting_playernum = 1, eg.seed_p1 + 1, eg.seed_p2 + 1) )
     as seed
FROM sessionuser su
JOIN matchuser mu1
  ON (su.user_id = mu1.user_id)
LEFT OUTER JOIN matchuser mu2 
  ON (su.user_id = mu2.user_id
      AND mu1.created_at < mu2.created_at)
JOIN `match` m
  ON (m.id = mu1.match_id
      AND m.session_id = su.session_id)
JOIN eliminationgraph eg
  ON (m.code = eg.code)
WHERE mu2.id IS NULL
);

CREATE OR REPLACE VIEW regularresults AS (
SELECT
  p.name as name,
  su.session_id,
  su.user_id,
  su.id as sessionuser_id,
--  su.status as sessionuser_status,
  mu1.id as matchuser_id,
--  mu1.starting_playernum,
  mu1.match_id,
--  mu1.matchrank,
  m.code as code,
--  m.format as format,
  ss.date as date,
  m.status as match_status
FROM sessionuser su
JOIN matchuser mu1
  ON (su.user_id = mu1.user_id)
JOIN `match` m
  ON (m.id = mu1.match_id
      AND m.session_id = su.session_id)
JOIN session ss
  ON (m.session_id = ss.id)
JOIN profile p
  ON (p.user_id = su.user_id)
LEFT OUTER JOIN matchuser mu2
  ON (su.user_id = mu2.user_id
      AND mu1.match_id = mu2.match_id
      AND mu1.created_at < mu2.created_at)
WHERE mu2.id IS NULL AND ss.type = 1
ORDER BY name
);

CREATE OR REPLACE VIEW regularmatchpoints AS (
SELECT
  mu.id,
  p.name as name,
  sess.season_id,
  m.session_id,
  mu.match_id,
  mu.user_id,
  mu.game_count,
  mu.opponent_count,
  mu.forfeit_opponent_count,
  sess.name as session_name,
  m.code,
  mu.bonuspoints + SUM(s.matchpoints) as matchpoints
FROM matchuser mu
JOIN `match` m
  ON (m.id = mu.match_id)
JOIN game g
  ON (g.match_id = mu.match_id)
JOIN score s
  ON (s.game_id = g.id AND s.user_id = mu.user_id)
JOIN profile p
  ON (p.user_id = s.user_id)
JOIN session sess
  ON (sess.id = m.session_id)
WHERE
  s.matchpoints IS NOT NULL
GROUP BY
  mu.id
);

CREATE OR REPLACE VIEW machinescore AS (
SELECT
 m.id,
 s.value
FROM machine m
INNER JOIN game g
 ON (m.id = g.machine_id)
INNER JOIN score s
 ON (g.id = s.game_id)
WHERE
 s.value IS NOT NULL
ORDER BY
 m.id, s.value
);

CREATE OR REPLACE VIEW machinescoreminmax AS (
SELECT
 id,
 MIN(value) as min,
 MAX(value) as max
FROM
 machinescore
GROUP BY
 id
);

CREATE OR REPLACE VIEW machinescoremedian AS (
SELECT
 medians.id,
 (MAX(medians.value)+MIN(medians.value))/2 as median
FROM
(
SELECT cs.id, value FROM
 (SELECT id, value,
  (SELECT COUNT(1) FROM machinescore ms2
   WHERE ms2.value < ms.value
      AND ms2.id = ms.id) as ls,
  (SELECT COUNT(1) FROM machinescore ms2
   WHERE ms2.value <= ms.value
      AND ms2.id = ms.id) as lse
  FROM machinescore ms) cs
JOIN
 (SELECT id, COUNT(1)*.5 as cn
  FROM machinescore GROUP BY id) cc
ON
 cs.id = cc.id
WHERE
 cn BETWEEN ls AND lse
) as medians
GROUP BY id
);

CREATE OR REPLACE VIEW machinerecentstatus AS (
SELECT
  m.id,
  m.name,
  m.abbreviation,
  m.ipdb_id,
  m.location_id,
  ms1.id as machinestatus_id,
  ms1.status,
  ms1.game_id,
  ms1.recorder_id,
  ms1.updated_at,
  mmx.min,
  mmx.max,
  mmn.median
FROM machine m
LEFT OUTER JOIN machinestatus ms1
  ON (m.id = ms1.machine_id)
LEFT OUTER JOIN machinestatus ms2
  ON (m.id = ms2.machine_id
      AND (ms1.created_at < ms2.created_at
           OR ms1.id IS NULL AND ms2.id IS NULL))
LEFT OUTER JOIN machinescoreminmax mmx
  ON (m.id = mmx.id)
LEFT OUTER JOIN machinescoremedian mmn
  ON (m.id = mmn.id)
WHERE ms2.id IS NULL
);



/*
CREATE OR REPLACE VIEW `gamePublicVoter` AS (
SELECT
  vote.game_id as game_id,
  user.id as user_id,
  user.username as username,
  user.votespublic as votespublic
FROM
  vote
  JOIN user on user.id = vote.user_id
WHERE
  votespublic = 1
ORDER BY
  user.username
);

CREATE OR REPLACE VIEW `possibleVote` AS (
  SELECT
    game.id      AS game_id,
    user.id      AS user_id,
    poll.id      AS poll_id,
    vote.game_id AS vote_game_id
  FROM (
    user
    JOIN game ON true
    JOIN poll ON poll.id = poll_id
    LEFT JOIN vote ON (vote.user_id = user.id
              AND vote.game_id = game.id)
  )
  WHERE
    poll.state = 1
  ORDER BY
    user_id, poll_id, game.title
);

CREATE OR REPLACE VIEW `voteCount` AS (
  SELECT
    poll.id as poll_id,
    user.id as user_id,
    COUNT(game_id) as count
  FROM
    poll, game
    INNER JOIN user
    LEFT JOIN vote ON game.id = vote.game_id AND vote.user_id = user.id
  WHERE
    poll.state = 1
    AND poll.id = game.poll_id
  GROUP BY
    poll.id, user.id
);

CREATE OR REPLACE VIEW `gameVoteCount` AS (
  SELECT
    poll_id,
    game.id as game_id,
    COUNT(*)
  FROM
    game
    JOIN vote ON game.id = vote.game_id
  GROUP BY
    game_id
  ORDER BY
    poll_id, game_id
);

INSERT INTO poll (id, title, state, votelimit, created_at, updated_at) 
  VALUES (1, "2015 Preliminary Round", 1, 3, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

*/

LOCK TABLES `location` WRITE;
INSERT INTO `location` VALUES (1,'MFP','1536 Moffett St., Salinas, CA 93905','Cary Carmichael','No notes.',1500549133,1500549133);
UNLOCK TABLES;

LOCK TABLES `machine` WRITE;
INSERT INTO `machine` VALUES (1,'2001','2001',2697,1,1500550472,1500550472),(2,'Attack From Mars','AFM',3781,1,1500550485,1500550485),(3,'Batman (Stern, Pro)','BDK',5583,1,1500550485,1500550485),(4,'Big Buck Hunter Pro','BBH',5513,1,1500550485,1500550485),(5,'Congo','CONGO',3780,1,1500550485,1500550485),(6,'Creature from the Black Lagoon','CFTBL',588,1,1500550485,1500550485),(7,'Cherry Bell','CB',3070,1,1500550485,1503223533),(8,'Dirty Harry','DH',684,1,1500550485,1500550485),(9,'Eight Ball Deluxe','EBD',762,1,1500550485,1500550485),(10,'Fireball II','FB2',854,1,1500550485,1500550485),(11,'Harley-Davidson, (2nd Edition)','HD',4455,1,1500550485,1500550485),(12,'Indiana Jones','IJ08',5306,1,1500550485,1500550485),(13,'NBA Fastbreak','NBAF',4023,1,1500550485,1500550485),(14,'Pirates of the Caribbean','POTC',5163,1,1500550485,1500550485),(15,'Playboy','PB',1823,1,1500550485,1500550485),(16,'Road Kings','RK',1970,1,1500550485,1500550485),(17,'Spider-Man','SM',5237,1,1500550485,1503115095),(18,'Star Trek (Starfleet Pro)','ST13',6044,1,1500550485,1500550485),(19,'Disney TRON Legacy','DTL',5682,1,1500550485,1503223888),(20,'The Getaway: High Speed II','HS2',1000,1,1500550485,1500550485),(21,'Godzilla','GZ',4443,1,1503115181,1503115181),(22,'Game of Thrones (Pro)','GoT',6307,1,1503115219,1503115219),(23,'Aerosmith (Pro)','AERO',6370,1,1503115260,1503115260),(24,'Aquarius','AQ',79,1,1503223350,1503223350),(25,'Dealer\'s Choice','DC',649,1,1503223589,1503223589),(26,'Ghostbusters (Premium)','GB',6333,1,1503223658,1503223688),(27,'Old Chicago','OC',1704,1,1503223744,1503223744),(28,'WHO dunnit','WD?',3685,1,1503224099,1503224099);
UNLOCK TABLES;

INSERT INTO `season` VALUES (1,2,'BAPA 2017 Spring Season',1503120302,1503120302);

INSERT INTO seasonuser (id, notes, matchpoints, game_count, opponent_count, match_count, dues, user_id, season_id)
  VALUES
(1, "", 258, 44, 33, 11, 1, 44, 1),
(2, "", 240, 44, 33, 11, 1, 37, 1),
(3, "", 238, 48, 36, 12, 1, 32, 1),
(4, "", 218, 48, 36, 12, 1, 31, 1),
(5, "", 207, 48, 36, 12, 1, 46, 1),
(6, "", 186, 40, 30, 10, 1, 54, 1),
(7, "", 167, 36, 27, 9, 1, 34, 1),
(8, "", 163, 40, 30, 10, 1, 55, 1),
(9, "", 162, 36, 27, 9, 1, 33, 1),
(10, "", 162, 40, 30, 10, 1, 40, 1),
(11, "", 156, 40, 30, 10, 1, 35, 1),
(12, "", 147, 40, 30, 10, 1, 52, 1),
(13, "", 139, 40, 30, 10, 1, 49, 1),
(14, "", 137, 32, 24, 8, 1, 1, 1),
(15, "", 136, 36, 27, 9, 1, 41, 1),
(16, "", 134, 36, 27, 9, 1, 57, 1),
(17, "", 132, 40, 30, 10, 1, 43, 1),
(18, "", 129, 28, 21, 7, 1, 45, 1),
(19, "", 128, 40, 30, 10, 1, 53, 1),
(20, "", 119, 32, 24, 8, 1, 51, 1),
(21, "", 119, 40, 30, 10, 1, 47, 1),
(22, "", 113, 32, 24, 8, 1, 59, 1),
(23, "", 110, 24, 18, 6, 1, 48, 1),
(24, "", 101, 28, 21, 7, 1, 39, 1),
(25, "", 101, 32, 24, 8, 1, 42, 1),
(26, "", 98, 24, 18, 6, 1, 56, 1),
(27, "", 96, 24, 18, 6, 1, 38, 1),
(28, "", 96, 20, 15, 5, 1, 28, 1),
(29, "", 91, 24, 18, 6, 1, 50, 1),
(30, "", 79, 20, 15, 5, 1, 58, 1),
(31, "", 72, 20, 15, 5, 1, 30, 1);


