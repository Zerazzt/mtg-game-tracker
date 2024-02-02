<header>
	<nav>
		<ul>
			<li><a href="/">Home</a></li>
			<li class="right">
				<ul>
					<?php
					$navSet = isset($_SESSION['id']) ? 'logged_in_nav' : 'logged_out_nav';
					foreach($pages as $page):
						if ($page[$navSet]):
					?>
					<li><a href="<?= $page['route'] ?>"><?= $page['title'] ?></a></li>
					<?php endif; endforeach; ?>
				</ul>
			</li>
		</ul>
	</nav>
</header>