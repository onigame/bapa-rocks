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
                         -- 2 = AWAITING MACHINE, 3 = IN PROGRESS, 4 = COMPLETED
                         -- 5 = DISQUALIFIED (e.g., broken machine)
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
    value INT, -- NULL = unscored
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
    matchpoints INT NOT NULL, -- add all games in match (with possible bonus)
    matchrank INT, -- rank within match (1st place, 2nd, etc.) Ties are indeterminate.
    game_count INT NOT NULL,
    opponent_count INT NOT NULL,
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
    created_at int(11),
    updated_at int(11)
);

CREATE TABLE seasonuser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notes VARCHAR(255),
    matchpoints INT NOT NULL, -- add all games in match (possible bonus)
    game_count INT NOT NULL,
    opponent_count INT NOT NULL,
    match_count INT NOT NULL,
    dues INT NOT NULL, -- 0 = not paid, 1 = paid
    playoff_division VARCHAR(20), -- NULL = unassgned; 'A', 'B', 'DQ'
    playoff_rank INT, -- NULL = unassigned, otherwise within playoff
    mpg DOUBLE AS (IF(game_count=0,NULL,matchpoints / game_count)) STORED,
    user_id INT NOT NULL,
    FOREIGN KEY user_key (user_id) REFERENCES user(id),
    season_id INT NOT NULL,
    FOREIGN KEY season_key (season_id) REFERENCES season(id),
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
INSERT INTO `location` VALUES (1,'The Office','1536 Moffett St., Salinas, CA 93905','<no contact supplied>','No notes.',1500549133,1500549133);
UNLOCK TABLES;

LOCK TABLES `machine` WRITE;
INSERT INTO `machine` VALUES (1,'2001','2001',2697,1,1500550472,1500550472),(2,'Attack From Mars','AFM',3781,1,1500550485,1500550485),(3,'Batman (Stern, Pro)','BDK',5583,1,1500550485,1500550485),(4,'Big Buck Hunter Pro','BBH',5513,1,1500550485,1500550485),(5,'Congo','CONGO',3780,1,1500550485,1500550485),(6,'Creature from the Black Lagoon','CFTBL',588,1,1500550485,1500550485),(7,'Cue Ball Wizard','CBW',610,1,1500550485,1500550485),(8,'Dirty Harry','DH',684,1,1500550485,1500550485),(9,'Eight Ball Deluxe','EBD',762,1,1500550485,1500550485),(10,'Fireball II','FB2',854,1,1500550485,1500550485),(11,'Harley-Davidson, (2nd Edition)','HD',4455,1,1500550485,1500550485),(12,'Indiana Jones','IJ08',5306,1,1500550485,1500550485),(13,'NBA Fastbreak','NBAF',4023,1,1500550485,1500550485),(14,'Pirates of the Caribbean','POTC',5163,1,1500550485,1500550485),(15,'Playboy','PB',1823,1,1500550485,1500550485),(16,'Road Kings','RK',1970,1,1500550485,1500550485),(17,'Spider-Man.','SM',5237,1,1500550485,1500550485),(18,'Star Trek (Starfleet Pro)','ST13',6044,1,1500550485,1500550485),(19,'The Amazing Spider-Man','AS',2285,1,1500550485,1500550485),(20,'The Getaway: High Speed II','HS2',1000,1,1500550485,1500550485);
UNLOCK TABLES;

