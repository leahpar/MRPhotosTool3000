<style>
    * {
        margin: 0;
        padding: 0;
    }
    body {
        background-color: black;
    }
    div {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
        grid-gap: 1em;
        padding: 1em;
    }
    div img {
        width: 100%;
        height: auto;
    }
</style>
<div>
<?php

// find ${FOLDER} -iname '*.jpg' -exec convert \{} -verbose -resize $WIDTHx$HEIGHT\> \{} \;
// find . -iname '*.jpg' -exec convert \{} -verbose -resize 500x500 \> \{} \;

/*
if ($handle = opendir('.')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && substr($entry, -3) == "jpg") {
            echo "<img src=$entry>";
        }
    }
    closedir($handle);
}
*/

// get everything except hidden files
// sorted by name by default
$files = preg_grep('/^([^.])/', scandir("."));
foreach ($files as $file) {
    if (substr($file, -3) == "jpg") {
        echo "<img src=$file>";
    }
}
?>
</div>
