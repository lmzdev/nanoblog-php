# nanoblog-php
PHP Boilerplate for storing Text/Timestamp entries in a SQLite Database.

### Introduction 
I needed a dead-simple solution for storing status updates without any additional setup. Also, my website resides on a shared hosting plan where only PHP is available.

The solutions i found where either too static or too bloated to my liking, so i came up with my own. But beware: As of now i guess this Project only qualifies as a collection of code snippets or a boilerplate at best.

If you are looking for a more refined and ready-to-use microblogging solution, take a look at [oelna/microblog](https://github.com/oelna/microblog). 

### Usage
1. Copy ```nanoblog.php``` to your webspace.
1. Include with ``` <?php include('nanoblog.php') ?>``` or only use via HTTP Requests
#### Add Entry
* Use ```db_insert``` function
* or do a HTTP POST Request and include _add_ and _secret_ , eg. ```add=<newconent>&secret=<mysecret>```

#### Remove Entry
* Remove latest entry with ```db_delete_latest``` function
* or do a HTTP POST Request and include _delete_ and _secret_ , eg. ```delete&secret=<mysecret>```

### Hints
I included ```nanoblog.php``` into my ```index.php``` and did the following to display a list of Posts:
```php
<?php
$posts = db_select_posts();
if ($posts) {
    foreach ($posts as $item) {
        $datetime = strftime('%a., %d.%m.%Y - %H:%M', $item['post_timestamp']);
        $post = $item["post_content"]; ?>
        <article class="post">
            <b class="date"><?= $datetime ?></b><br>
                <?= $post ?>
        </article>
<?php
    }
}
>
```

Insertion and removal is done through a JavaScript-Client which hits the HTTP Endpoints with ```XMLHttpRequest```
