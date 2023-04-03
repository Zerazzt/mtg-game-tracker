CREATE TABLE `players` (
	`id` int NOT NULL AUTO_INCREMENT,
	`username` varchar(64) NOT NULL,
	`name` varchar(64) NOT NULL,
	`wins` int DEFAULT 0,
	`losses` int DEFAULT 0,
	PRIMARY KEY (id)
);

CREATE TABLE `decks` (
	`id` int NOT NULL AUTO_INCREMENT,
	`owner` int NOT NULL,
	`commander` varchar(64) NOT NULL,
	`partner` varchar(64) DEFAULT NULL,
	`companion` varchar(64) DEFAULT NULL,
	`wins` int DEFAULT 0,
	`losses` int DEFAULT 0,
	PRIMARY KEY (id),
	FOREIGN KEY (owner) REFERENCES players(id)
);

CREATE TABLE `games` (
	`id` int NOT NULL AUTO_INCREMENT,
	`date` datetime NOT NULL,
	`winning_deck` int NOT NULL,
	`winning_player` int NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (winning_deck) REFERENCES decks(id),
	FOREIGN KEY (winning_player) REFERENCES players(id)
);

CREATE TABLE `game_participation` (
	`game_id` int NOT NULL,
	`deck_id` int NOT NULL,
	`player_id` int NOT NULL,
	FOREIGN KEY (game_id) REFERENCES games(id),
	FOREIGN KEY (deck_id) REFERENCES decks(id),
	FOREIGN KEY (player_id) REFERENCES players(id)
);

CREATE TABLE `game_tags` (
	`id` int NOT NULL,
	`tag` varchar(64) NOT NULL,
	FOREIGN KEY (id) REFERENCES games(id)
);

CREATE TABLE `admin` (
	`id` int NOT NULL AUTO_INCREMENT,
	`email` varchar(64) NOT NULL,
	`password` text NOT NULL,
	`token` text DEFAULT NULL,
	`token_expiry` bigint(20) DEFAULT NULL,
	PRIMARY KEY (id)
);

DELIMITER $$
CREATE TRIGGER `game_added` BEFORE INSERT ON `game_participation`
FOR EACH ROW
BEGIN
	UPDATE `players`
	SET
		`wins`=`wins`+IF(
			(
				SELECT `games`.`winning_player`
				FROM `games`
				WHERE `games`.`id`=`new`.`game_id`
			)=`new`.`player_id`,
			1,
			0
			),
		`losses`=`losses`+IF(
			(
				SELECT `games`.`winning_player`
				FROM `games`
				WHERE `games`.`id`=`new`.`game_id`
			)=`new`.`player_id`,
			0,
			1
			)
	WHERE `id`=`new`.`player_id`;

	UPDATE `decks`
	SET
		`wins`=`wins`+IF(
			(
				SELECT `games`.`winning_deck`
				FROM `games`
				WHERE `games`.`id`=`new`.`game_id`
			)=`new`.`deck_id`,
			1,
			0
			),
		`losses`=`losses`+IF(
			(
				SELECT `games`.`winning_deck`
				FROM `games`
				WHERE `games`.`id`=`new`.`game_id`
			)=`new`.`deck_id`,
			0,
			1
			)
	WHERE `id`=`new`.`deck_id`;
END;
$$
DELIMITER ;

CREATE PROCEDURE `RestrictedDeckWinRate`(IN `player_count` INT)
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
SELECT
	rgp.deck_id,
	rgp.commander,
	COALESCE(deck_wins.wins, 0) AS `wins`,
	rgp.count - COALESCE(deck_wins.wins, 0) AS `losses`,
	100 * COALESCE(deck_wins.wins, 0) / rgp.count AS `win rate`
FROM (
	SELECT decks.id AS `id`,
		decks.commander,
		COUNT(rgames.winning_deck) AS `wins`
	FROM decks
	RIGHT JOIN (
		SELECT
			games.winning_deck
		FROM games
		RIGHT JOIN game_participation
		ON games.id = game_participation.game_id
		GROUP BY games.id
		HAVING COUNT(games.id) = player_count
	) AS `rgames`
	ON decks.id = rgames.winning_deck
	GROUP BY decks.id
) AS `deck_wins`
RIGHT JOIN (
	SELECT
		game_participation.deck_id,
		decks.commander,
		COUNT(game_participation.deck_id) AS `count`
	FROM game_participation
	RIGHT JOIN (
		SELECT
			games.id AS `id`
		FROM games
		RIGHT JOIN game_participation
		ON game_participation.game_id = games.id
		GROUP BY games.id
		HAVING COUNT(games.id) = player_count
	) AS `allrgames`
	ON game_participation.game_id = allrgames.id
	LEFT JOIN decks
	ON game_participation.deck_id = decks.id
	GROUP BY deck_id
) AS `rgp`
ON deck_wins.id = rgp.deck_id
ORDER BY `win rate` DESC;

CREATE PROCEDURE `OverallDeckWinRate`()
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
SELECT
	rgp.deck_id,
	rgp.commander,
	COALESCE(deck_wins.wins, 0) AS `wins`,
	rgp.count - COALESCE(deck_wins.wins, 0) AS `losses`,
	100 * COALESCE(deck_wins.wins, 0) / rgp.count AS `win rate`
FROM (
	SELECT
		decks.id AS `id`,
		decks.commander,
		COUNT(rgames.winning_deck) AS `wins`
	FROM decks
	RIGHT JOIN (
		SELECT
			games.winning_deck
		FROM games
		RIGHT JOIN game_participation
		ON games.id = game_participation.game_id
		GROUP BY games.id
		HAVING COUNT(games.id) > 0
	) AS `rgames`
	ON decks.id = rgames.winning_deck
	GROUP BY decks.id
) AS `deck_wins`
RIGHT JOIN (
	SELECT
		game_participation.deck_id,
		decks.commander,
		COUNT(game_participation.deck_id) AS `count`
	FROM game_participation
	RIGHT JOIN (
		SELECT
			games.id AS `id`
		FROM games
		RIGHT JOIN game_participation
		ON game_participation.game_id = games.id
		GROUP BY games.id
		HAVING COUNT(games.id) > 0 
	) AS `allrgames`
	ON game_participation.game_id = allrgames.id
	LEFT JOIN decks
	ON game_participation.deck_id = decks.id
	GROUP BY deck_id
) AS `rgp`
ON deck_wins.id = rgp.deck_id
ORDER BY `win rate` DESC;

CREATE PROCEDURE `RestrictedPlayerWinRate`(IN `player_count` INT)
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
SELECT
	rgp.player_id,
	rgp.name,
	COALESCE(player_wins.wins, 0) AS `wins`,
	rgp.count - COALESCE(player_wins.wins, 0) AS `losses`,
	100 * COALESCE(player_wins.wins, 0) / rgp.count AS `win rate`
FROM (
	SELECT
		players.id AS `id`,
		players.name,
		COUNT(rgames.winning_player) AS `wins`
	FROM players
	RIGHT JOIN (
		SELECT
			games.winning_player
		FROM games
		RIGHT JOIN game_participation
		ON games.id = game_participation.game_id
		GROUP BY games.id
		HAVING COUNT(games.id) = player_count
	) AS `rgames`
	ON players.id = rgames.winning_player
	GROUP BY players.id
) AS `player_wins`
RIGHT JOIN (
	SELECT
		game_participation.player_id, players.name,
		COUNT(game_participation.player_id) AS `count`
	FROM game_participation
	RIGHT JOIN (
		SELECT
			games.id AS `id`
		FROM games
		RIGHT JOIN game_participation
		ON game_participation.game_id = games.id
		GROUP BY games.id
		HAVING COUNT(games.id) = player_count
	) AS `allrgames`
	ON game_participation.game_id = allrgames.id
	LEFT JOIN players
	ON game_participation.player_id = players.id
	GROUP BY player_id
) AS `rgp`
ON player_wins.id = rgp.player_id
ORDER BY `win rate` DESC;

CREATE PROCEDURE `OverallPlayerWinRate`()
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
SELECT
	rgp.player_id,
	rgp.name,
	COALESCE(player_wins.wins, 0) AS `wins`,
	rgp.count - COALESCE(player_wins.wins, 0) AS `losses`,
	100 * COALESCE(player_wins.wins, 0) / rgp.count AS `win rate`
FROM (
	SELECT
		players.id AS `id`,
		players.name,
		COUNT(rgames.winning_player) AS `wins`
	FROM players
	RIGHT JOIN (
		SELECT
			games.winning_player
		FROM games
		RIGHT JOIN game_participation
		ON games.id = game_participation.game_id
		GROUP BY games.id
		HAVING COUNT(games.id) > 0
	) AS `rgames`
	ON players.id = rgames.winning_player
	GROUP BY players.id
) AS `player_wins`
RIGHT JOIN (
	SELECT
		game_participation.player_id,
		players.name,
	COUNT(game_participation.player_id) AS `count`
	FROM game_participation
	RIGHT JOIN (
		SELECT
			games.id AS `id`
		FROM games
		RIGHT JOIN game_participation
		ON game_participation.game_id = games.id
		GROUP BY games.id
		HAVING COUNT(games.id) > 0
	) AS `allrgames`
	ON game_participation.game_id = allrgames.id
	LEFT JOIN players
	ON game_participation.player_id = players.id
	GROUP BY player_id
) AS `rgp`
ON player_wins.id = rgp.player_id
ORDER BY `win rate` DESC;