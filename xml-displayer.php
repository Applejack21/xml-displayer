<?php
/**
 * Plugin Name: XML-displayer
 * Description: When activated, this plugin will automatically make a separate post about books with information on each book within the posts. It will also display information about each book under every post that is created. If there is a post with the same "post_title" as the title of a book in the XML file, a post will not be created.
 * Version: 1.1
 * Author: George Goodall
 */

//Adds information about books below each post created
function endNote ($content) 
{      
    //dont have to type in '<br>' anymore
    $break = '<br>';
    
    //get xml file from w3schools
    $xml = simplexml_load_file("https://www.w3schools.com/php/books.xml") or die("Error: Couldn\'t load XML File.");
    
    $content .= '==== "endNote" function below ====';
    $content .= '<p>Displays information about each book in the XML file from <a href=https://www.w3schools.com/php/books.xml>W3Schools</a>.</p>';
    
    //loop through each book and display them
        foreach ($xml->book as $books)
        {
            $content .= '<b> Book Title: </b>' . $books->title . $break;
            $content .= '<b> Written By: </b>' . $books->author . $break;
            $content .= '<b> Release Year: </b>' . $books->year . $break;
            $content .= '<b> Price:  </b> £ ' . $books->price . $break;
            $content .= $break;
        }
        return $content;
}

//run the function above after each post
add_action('the_content', 'endNote');


// Create a post based on each entry in the XML file
function createNewPost()
{
    //dont have to type in '<br>' anymore
    $break = '<br>'; 
    
    //get xml file from w3schools
    $xml = simplexml_load_file("https://www.w3schools.com/php/books.xml") or die("Error: Couldn\'t load XML File.");
    
    //get id of who is making the post
    $userId = get_current_user_id(); 
    
    //loop through each book and make a post about each of them
    foreach ($xml->book as $books)
    {
        $title = $books->title;
        $content = '<p>Below is the information about ' .$title. ' from the XML file loaded.</p>';
        $content .= '<b> Written By: </b>' . $books->author . $break;
        $content .= '<b> Release Year: </b>' . $books->year . $break;
        $content .= '<b> Price: </b> £ ' . $books->price . $break;
    
        //check to see if the post already exists, uses the post's title to confirm if it exists or not
        global $wpdb;
        $newPost = $wpdb->get_var("SELECT count(post_title) FROM $wpdb->posts WHERE post_title = '$title'");
        if ($newPost < 1)
        {
            //if post doesnt exist, make it
            $newPost = array
                (
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_author' => $userId,
                    'post_title' => $title,
                    'post_status' => 'publish',
                    'post_type' => 'post',
                    'post_content' => $content,
                );
            wp_insert_post($newPost);
        }
    }
    //else do nothing, can be amended to bring up error message stating that the post already exists?
}

//run the above code after refresh
add_filter ('after_setup_theme', 'createNewPost');
?>  

