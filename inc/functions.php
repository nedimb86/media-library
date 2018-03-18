<?php

function get_catalog_count($category = null, $search = null) {
    include('connection.php');
    $category = strtolower($category);
    try {
        $sql = "SELECT COUNT(media_id) FROM Media";
        if(!empty($search)){
            $results = $db->prepare($sql . " WHERE title LIKE ?");
            $results->bindValue(1, '%'. $search . '%', PDO::PARAM_STR);
        } else if(!empty($category)){
            $results = $db->prepare($sql . " WHERE LOWER(category) = ?");
            $results->bindParam(1, $category, PDO::PARAM_STR);
        } else {
            $results = $db->prepare($sql);
        }
        $results->execute();
    } catch (Exception $e) {
        echo 'Could not retrieve number of elements';
        exit;
    }
    var_dump($results);
    $count = $results->fetchColumn(0);
    return $count;
}

function get_full_catalog($limit = null, $offset = 0) {
    include('connection.php');

    try {
        $sql = 'SELECT media_id, title, category, img 
              FROM Media
              ORDER BY REPLACE(
                  REPLACE(
                    REPLACE(title, \'The \', \'\'), \'A \', \'\'
                    ), \'An \', \'\'
                )';
        if(is_integer($limit)){
            $results = $db->prepare($sql . " LIMIT ? OFFSET ?");
            $results->bindParam(1,$limit,PDO::PARAM_INT);
            $results->bindParam(2,$offset,PDO::PARAM_INT);
        } else {
            $results = $db->prepare($sql);
        }
        $results->execute();

    } catch (Exception $e) {
        echo 'Cannot retrieve full catalog';
        exit;
    }

    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}

function get_section_catalog($section, $limit = null, $offset = 0) {
    include('connection.php');
    $section = strtolower($section);
    try {

        $sql = "SELECT media_id, title, category, img 
              FROM Media
              WHERE LOWER(category) = ?
              ORDER BY REPLACE(
                  REPLACE(
                    REPLACE(title, 'The ', ''), 'A ', ''
                    ), 'An ', ''
                )";
        if(is_integer($limit)){
            $results = $db->prepare($sql . " LIMIT ? OFFSET ?");
            $results->bindParam(1, $section, PDO::PARAM_STR);
            $results->bindParam(2,$limit,PDO::PARAM_INT);
            $results->bindParam(3,$offset,PDO::PARAM_INT);
        } else {
            $results = $db->prepare($sql);
            $results->bindParam(1, $section, PDO::PARAM_STR);
        }
        $results->execute();
    } catch (Exception $e) {
        echo 'Cannot retrieve full catalog';
        exit;
    }

    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}
function search_catalog($search, $limit = null, $offset = 0) {
    include('connection.php');
    $search = strtolower($search);
    try {
        $sql = "SELECT media_id, title, category, img 
              FROM Media
              WHERE LOWER(title) LIKE ?
              ORDER BY REPLACE(
                  REPLACE(
                    REPLACE(title, 'The ', ''), 'A ', ''
                    ), 'An ', ''
                )";
        if(is_integer($limit)){
            $results = $db->prepare($sql . " LIMIT ? OFFSET ?");
            $results->bindValue(1, "%" . $search ."%", PDO::PARAM_STR);
            $results->bindParam(2,$limit,PDO::PARAM_INT);
            $results->bindParam(3,$offset,PDO::PARAM_INT);
        } else {
            $results = $db->prepare($sql);
            $results->bindValue(1,  "%" . $search ."%", PDO::PARAM_STR);
        }
        $results->execute();
    } catch (Exception $e) {
        echo 'Cannot retrieve full catalog';
        exit;
    }

    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}

function random_catalog() {
    include('connection.php');

    try {
        $results = $db->query(
            'SELECT media_id, title, category, img 
              FROM Media
              ORDER BY RANDOM()
              LIMIT 4
              ');
    } catch (Exception $e) {
        echo 'Cannot retrieve full catalog';
        exit;
    }

    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}

function get_single_item($id) {
    include('connection.php');

    try {
        $results = $db->prepare(
            "SELECT Media.media_id, title, category, img, format,year, genre, publisher, isbn 
              FROM Media
              JOIN Genres ON Media.genre_id = Genres.genre_id
              LEFT OUTER JOIN Books ON Media.media_id = Books.media_id
              WHERE Media.media_id = ?"
        );
        $results->bindParam(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'Cannot retrieve data';
        exit;
    }

    $item = $results->fetch();
    if (empty($item)) return $item;

    try {
        $results = $db->prepare(
            "SELECT fullname, role
              FROM Media_People
              JOIN People ON Media_People.people_id = People.people_id
              WHERE Media_People.media_id = ?"
        );
        $results->bindParam(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'Cannot retrieve people';
        exit;
    }

    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $item[$row['role']][] = $row['fullname'];
    }

    return $item;
}

function get_item_html($item) {
    $output = '<li><a href="details.php?id='
        . $item['media_id'] . '" ><img src="'
        . $item['img'] . '" alt="'
        . $item['title'] .'"><p>View More</p></a></li>';
    return $output;
}

function array_category($catalog, $category) {
    $output = [];
    foreach ($catalog as $id => $item) {
        if( $category == null OR strtolower($category) == strtolower($item['category'])) {
            $sort = $item['title'];
            $sort = ltrim($sort, "The ");
            $sort = ltrim($sort, "A ");
            $sort = ltrim($sort, "An ");
            $output[$id] = $sort;
        }
    }
    asort($output);
    return array_keys($output);
}

function get_genres_array($category = null){

}