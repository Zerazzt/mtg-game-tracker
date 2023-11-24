<?php
require_once "../includes/variables.php";
?>
<h2>Add a new game</h2>
<form method="post" class="container">
	<?php
	for ($i = 1; $i <= MAX_PLAYER_COUNT; ++$i):
	?>
	<div>
		<label for="player<?= $i ?>">Player <?= $i ?>:</label>
		<select class="playerSelect" name="player<?= $i ?>" id="player<?= $i ?>">
		
		</select>
	</div>
	<div>
		<label for="owner<?= $i ?>">Owner <?= $i ?>:</label>
		<select class="ownerSelect" name="owner<?= $i ?>" id="owner<?= $i ?>">

		</select>
	</div>
	<div>
		<label for="deck<?= $i ?>">Deck <?= $i ?>:</label>
		<select class="deckSelect" name="deck<?= $i ?>" id="deck<?= $i ?>">
		
		</select>
	</div>
	<div>
		<input type="radio" name="winner" id="p<?= $i ?>w" value=<?= $i ?>>
		<label for="p<?= $i ?>w">Player <?= $i ?> wins</label>
	</div>
	<?php
	endfor;
	?>
	<button type="submit" name="addGame">Submit</button>
</form>