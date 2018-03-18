<?php
include('inc/functions.php');
$pageTitle = 'Full Catalog';
$section = null;
$items_per_page = 8;
$search = null;

if(isset($_GET["cat"])) {
    if ($_GET["cat"] == "books") {
        $pageTitle = "Books";
        $section='books';
    } else if ($_GET["cat"] == "movies") {
        $pageTitle = "Movies";
        $section='movies';
    } else if ($_GET["cat"] == "music") {
        $pageTitle = "Music";
        $section = 'music';
    }
}
if(isset($_GET['s'])) {
  $search = filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING);
}
if(isset($_GET['pg'])) {
  $current_page = filter_input(INPUT_GET, 'pg', FILTER_SANITIZE_NUMBER_INT);
}
if(empty($current_page)) {
  $current_page = 1;
}

$total_items = get_catalog_count($section, $search);
$offset = 0;
$total_pages = 1;
if ($total_items > 0) {
    $total_pages = ceil($total_items / $items_per_page);
    $limit_results = '';
    if (!empty($search)) {
        $limit_results = "s=".  urlencode(htmlspecialchars($search)) ."&";
    } else if (isset($section)) {
        $limit_results = 'cat=' . $section . '&';
    }

    if ($current_page > $total_pages) {
        header('location:catalog.php?' . $limit_results . 'pg=' . $total_pages);
    }
    if ($current_page < 1) {
        header('location:catalog.php?' . $limit_results . 'pg=1');
    }

    $offset = ($current_page - 1) * $items_per_page;

    if (!empty($search)) {
        $catalog = search_catalog($search, $items_per_page, $offset);
    } else if (empty($section)) {
        $catalog = get_full_catalog($items_per_page, $offset);
    } else {
        $catalog = get_section_catalog($section, $items_per_page, $offset);
    }

    $pagination = "<div class=\"pagination\">Pages:";
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $pagination .= " <span>$i</span>";
        } else {
            $pagination .= " <a href='catalog.php?";
            if (!empty($search)) {
                $pagination .= "s=".  urlencode(htmlspecialchars($search)) ."&";
            } else if (!empty($section)) {
                $pagination .= "cat=$section&";
            }
            $pagination .= "pg=$i'><span>$i</span></a>";
        }
    }
    $pagination .= '</div>';
}
include("inc/header.php");
?>
    <div class="section catalog page">
      <div class="wrapper">

            <h1>
                <?php
                if($search != null) {
                 echo 'Search results for: "' . htmlspecialchars($search) . '"';
                } else {
                    if ($section != null) {
                        echo '<a href="catalog.php">Full Catalog</a> &gt; ';
                    }
                    echo $pageTitle;
                }?>
            </h1>


          <?php
          if($total_items < 1) {?>
            <p>No items were found matching that search term.</p>
            <p>Search again or <a href="catalog.php">Browse the Full Catalog</a>.</p>
          <?php } else {
              echo $pagination ?>
            <ul class="items">
                <?php
                foreach ($catalog as $item) {
                    echo get_item_html($item);
                }
                ?>
            </ul>
              <?php echo $pagination;
          }?>
        </div>
    </div>

<?php include("inc/footer.php"); ?>