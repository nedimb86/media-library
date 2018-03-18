<?php

  $pageTitle = 'Personal Media Library';
  $section = null;

  include("inc/header.php");
  include('inc/functions.php');

?>

		<div class="section catalog random">

			<div class="wrapper">

				<h2>May we suggest something?</h2>

								<ul class="items">
                    <?php
                    $random = random_catalog();
                    foreach ($random as $item) {
                        echo get_item_html($item);
                    }
                    ?>
		            </ul>

			</div>

		</div>

<?php include("inc/footer.php"); ?>