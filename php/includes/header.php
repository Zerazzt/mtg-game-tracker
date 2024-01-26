<header>
	<nav>
		<ul>
			<li><a href="/">Home</a></li>
			<li class="right">
				<ul>
					<?php
					if (isset($_SESSION["id"])) {
						$page_list = $logged_in_nav;
					}
					else {
						$page_list = $logged_out_nav;
					}
					foreach($page_list as $file):
					?>
					<li><a href="<?= $page_routes[$file] ?>"><?= $page_titles[$file] ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
		</ul>
	</nav>
</header>