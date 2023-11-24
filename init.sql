CREATE TABLE `players` (
	`id` int NOT NULL AUTO_INCREMENT,
	`username` varchar(64) NOT NULL,
	`name` varchar(64) NOT NULL,
	`active` TINYINT(1),
	PRIMARY KEY (id)
);

CREATE TABLE `decks` (
	`id` int NOT NULL AUTO_INCREMENT,
	`owner` int NOT NULL,
	`commander` varchar(64) NOT NULL,
	`partner` varchar(64) DEFAULT NULL,
	`companion` varchar(64) DEFAULT NULL,
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
	`turn_order` int NOT NULL,
	FOREIGN KEY (game_id) REFERENCES games(id),
	FOREIGN KEY (deck_id) REFERENCES decks(id),
	FOREIGN KEY (player_id) REFERENCES players(id)
);

CREATE TABLE `tags` (
	`id` int NOT NULL,
	`tag` varchar(64) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE `game_tags` (
	`game_id` int NOT NULL,
	`tag_id` int NOT NULL,
	FOREIGN KEY (game_id) REFERENCES games(id),
	FOREIGN KEY (tag_id) REFERENCES tags(id)
);

CREATE VIEW `player_win_rates` AS
SELECT
	`gp`.`id`,
	`gp`.`name`,
	COALESCE(`player_wins`.`wins`, 0) AS `wins`,
	`gp`.`count` - COALESCE(`player_wins`.`wins`, 0) AS `losses`,
	100 * COALESCE(`player_wins`.`wins`, 0) / `gp`.`count` AS `win rate`
FROM (
	SELECT
		`players`.`id` AS `id`,
		`players`.`name`,
		COUNT(`games`.`winning_player`) AS `wins`
	FROM `players`
	RIGHT JOIN `games`
	ON `players`.`id` = `games`.`winning_player`
	GROUP BY `games`.`winning_player`
) AS `player_wins`
RIGHT JOIN (
	SELECT
		`players`.`id` AS `id`,
		`players`.`name` AS `name`,
		COUNT(`game_participation`.`player_id`) AS `count`
	FROM `players`
	RIGHT JOIN `game_participation`
	ON `players`.`id` = `game_participation`.`player_id`
	GROUP BY `game_participation`.`player_id`
) AS `gp`
ON `player_wins`.`id` = `gp`.`id`
ORDER BY `win rate` DESC;

CREATE VIEW `deck_win_rates` AS
SELECT
	`players`.`name`,
	`data`.`id`,
	`data`.`commander`,
	`data`.`partner`,
	`data`.`wins`,
	`data`.`losses`,
	`data`.`win rate`
FROM (
	SELECT
		`gp`.`id`,
		`gp`.`owner`,
		`gp`.`commander`,
		`gp`.`partner`,
		COALESCE(`deck_wins`.`wins`, 0) AS `wins`,
		`gp`.`count` - COALESCE(`deck_wins`.`wins`, 0) AS `losses`,
		100 * COALESCE(`deck_wins`.`wins`, 0) / `gp`.`count` AS `win rate`
	FROM (
		SELECT
			`decks`.`id` AS `id`,
			COUNT(`games`.`winning_deck`) AS `wins`
		FROM `decks`
		RIGHT JOIN `games`
		ON `decks`.`id` = `games`.`winning_deck`
		GROUP BY `games`.`winning_deck`
	) AS `deck_wins`
	RIGHT JOIN (
		SELECT
			`decks`.`id` AS `id`,
			`decks`.`owner` AS `owner`,
			`decks`.`commander` AS `commander`,
			`decks`.`partner` AS `partner`,
			COUNT(`game_participation`.`deck_id`) AS `count`
		FROM `decks`
		RIGHT JOIN `game_participation`
		ON `decks`.`id` = `game_participation`.`deck_id`
		GROUP BY `game_participation`.`deck_id`
	) AS `gp`
	ON `deck_wins`.`id` = `gp`.`id`
) AS `data`
LEFT JOIN `players`
ON `data`.`owner` = `players`.`id`
ORDER BY `win rate` DESC;

CREATE PROCEDURE `RestrictedDeckWinRate`(IN `player_count` INT)
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
SELECT
	`restricted_game_participation`.`deck_id`,
	`restricted_game_participation`.`commander`,
	COALESCE(`deck_wins`.`wins`, 0) AS `wins`,
	`restricted_game_participation`.`count` - COALESCE(`deck_wins`.`wins`, 0) AS `losses`,
	100 * COALESCE(`deck_wins`.`wins`, 0) / `restricted_game_participation`.`count` AS `win rate`
FROM (
	SELECT
		`decks`.`id` AS `id`
		`decks`.`commander` AS `commander`,
		COUNT(`restricted_games`.`winning_deck`) AS `wins`
	FROM decks
	RIGHT JOIN (
		SELECT
			`games`.`winning_deck` AS `winning_deck`
		FROM `games`
		RIGHT JOIN `game_participation`
		ON `games`.`id` = `game_participation`.`game_id`
		GROUP BY `games`.`id`
		HAVING COUNT(`games`.`id`) = player_count
	) AS `restricted_games`
	ON `decks`.`id` = `restricted_games`.`winning_deck`
	GROUP BY `decks`.`id`
) AS `deck_wins`
RIGHT JOIN (
	SELECT
		`game_participation`.`deck_id`,
		`decks`.`commander`,
		COUNT(`game_participation`.`deck_id`) AS `count`
	FROM `game_participation`
	RIGHT JOIN (
		SELECT
			`games`.`id` AS `id`
		FROM `games`
		RIGHT JOIN `game_participation`
		ON `game_participation`.`game_id` = `games`.`id`
		GROUP BY `games`.`id`
		HAVING COUNT(`games`.`id`) = player_count
	) AS `all_restricted_games`
	ON `game_participation`.`game_id` = `all_restricted_games`.`id`
	LEFT JOIN `decks`
	ON `game_participation`.`deck_id` = `decks`.`id`
	GROUP BY `decks`.`id`
) AS `restricted_game_participation`
ON `deck_wins`.`id` = `restricted_game_participation`.`deck_id`
ORDER BY `win rate` DESC;

CREATE PROCEDURE `RestrictedPlayerWinRate`(IN `player_count` INT)
NOT DETERMINISTIC
CONTAINS SQL
SQL SECURITY DEFINER
SELECT
	`restricted_game_participation`.`player_id`,
	`restricted_game_participation`.`name`,
	COALESCE(`player_wins`.`wins`, 0) AS `wins`,
	`restricted_game_participation`.`count` - COALESCE(`player_wins`.`wins`, 0) AS `losses`,
	100 * COALESCE(`player_wins`.`wins`, 0) / `restricted_game_participation`.`count` AS `win rate`
FROM (
	SELECT
		`players`.`id` AS `id`,
		`players`.`name` AS `name`,
		COUNT(`restricted_games`.`winning_player`) AS `wins`
	FROM `players`
	RIGHT JOIN (
		SELECT
			`games`.`winning_player`
		FROM `games`
		RIGHT JOIN `game_participation`
		ON `games`.`id` = `game_participation`.`game_id`
		GROUP BY `games`.`id`
		HAVING COUNT(`games`.`id`) = player_count
	) AS `restricted_games`
	ON `players`.`id` = `restricted_games`.`winning_player`
	GROUP BY `players`.`id`
) AS `player_wins`
RIGHT JOIN (
	SELECT
		`players`.`player_id`
		`players`.`name`,
		COUNT(`game_participation`.`player_id`) AS `count`
	FROM `game_participation`
	RIGHT JOIN (
		SELECT
			`games`.`id` AS `id`
		FROM `games`
		RIGHT JOIN `game_participation`
		ON `game_participation`.`game_id` = `games`.`id`
		GROUP BY `games`.`id`
		HAVING COUNT(`games`.`id`) = player_count
	) AS `all_restricted_games`
	ON `game_participation`.`game_id` = `all_restrited_games`.`id`
	LEFT JOIN `players`
	ON `game_participation`.`player_id` = `players`.`id`
	GROUP BY `game_participation`.`player_id`
) AS `restricted_game_participation`
ON `player_wins`.`id` = `restricted_game_participation`.`player_id`
ORDER BY `win rate` DESC;