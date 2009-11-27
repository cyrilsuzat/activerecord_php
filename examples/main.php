<?php
require_once 'Post.php';
require_once 'Comment.php';


// 1. Create ---------------------------------------
echo "<h1>Create</h1>";

// 1.1. new Post
$post = new Post();
$post->title = "First Post";
$post->body = "This is the body of my post";
$insert_id = $post->save(); # saves this post to the table

	echo "create post #$insert_id => $post->title <br />";


// 1.2. when a form submits an array
$_POST = array('title' => 'Second Post', 'body' => 'This is the body of the second post');
$post = new Post($_POST);
$insert_id = $post->save(); # save yet another post to the db

	echo "create post #$insert_id => $post->title <br />";


// 1.3. new Comment for Post #1
$comment = new Comment();
$comment->post_id = 1;
$comment->author = "Cyril";
$comment->content = "Comment for the first post";
$insert_id = $comment->save(); # saves this comment to the table

	echo "create comment #$insert_id => $comment->content <br />";


// 2. Retrieve -------------------------------------
echo "<h1>Retrieve</h1>";

// 2.1. finds the post with an id = 1
$post = Post::find(1);

	echo "retrieve post #$post->id => $post->title <br />";
	

# 2.2. returns the 10 most recent posts in an array
$posts = Post::find('all', array('order' => 'created_at DESC', 'limit' => 10));

	echo '<table border="1">';
	foreach ($posts as $post)
		echo "<tr><td>$post->id</td><td>$post->title</td><td>$post->body</td></tr>";
	echo '</table>';


// 3. Update ---------------------------------------
echo "<h1>Update</h1>";

// 3.1. find and update
$post = Post::find(1);
$post->title = "Some new title";
$post_id = $post->save(); # saves the change to the post

	echo "update post #$post_id => $post->title <br />";


// 3.2. update from a submit form
$_POST = array('title' => 'New Title', 'body' => 'New body here!');
$post = Post::find(1);
$post_id = $post->update_attributes($_POST); # saves the object with these attributes updated

	echo "update post #$post_id => $post->title <br />";


// 4. Destroy --------------------------------------
echo "<h1>Destroy</h1>";

// 4.1. delete the post #1
$post = Post::find(2);
if($post) {
	$post->destroy();
	echo "destroy post #$post->id => $post->title <br />";
}
else {
	echo "post #2 already destroyed! <br />";
}


// 5. Relationships --------------------------------
echo "<h1>Relationships</h1>";

// 5.1. get comments for post #1
$post = Post::find(1);
$comments = $post->comments;

	echo "comments for post #$post->id ($post->title): <br />";
	echo '<table border="1">';
	foreach ($comments as $comment)
		echo "<tr><td>$comment->id</td><td>$comment->author</td><td>$comment->content</td></tr>";
	echo '</table>';
	
// 5.2. get post for comment #1
$comment = Comment::find(1);
$post = $comment->post;

	echo "<p>comments #$comment->id ($comment->content) belongs to post #$post->id ($post->title)</p>";


// 6. XML ------------------------------------------
echo "<h1>XML</h1>";

// 6.1. return an object into XML
$post = Post::find(1);
$xml = $post->to_xml();

	echo "<pre>".htmlspecialchars($xml)."</pre>";
	
// 6.2. return an array of objects into XML
$post = Post::find(1);
$comments = $post->comments;
$xml = "<comments>\n";
foreach ($comments as $comment) {
	$xml .= $comment->to_xml();
}
$xml .= "<comments>\n";

	echo "<pre>".htmlspecialchars($xml, ENT_QUOTES)."</pre>";
	
	