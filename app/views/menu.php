<?php
//Create a list of the menu links
$links = array(
        'Prima pagină' => ' ',
        'Creare cont' => 'creare',
        'Contact' => 'contact'
);

foreach($links as $name => $link) {
    //If this this link is the current one in the URI
    if(stripos($route, $link) !== FALSE) {
        print "<li class=\"active\"><a href=\"/?$link\">$name</a></li>";
    } else {
        print "<li><a href=\"/?$link\">$name</a></li>";
    }
}
?>