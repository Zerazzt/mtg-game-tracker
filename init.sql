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