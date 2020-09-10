<?php
class db{
	public $config;
	public $db;
	function __construct($db){
		$this->config = $db;
	}
	function connect(){
		$this->db = new mysqli($this->config["server"], $this->config["user"], $this->config["pass"], $this->config["database"]);
	}
	function addPost($post){
		return $this->db->query("INSERT INTO posts 
		(id, name, date, reply, board, post, info, style, fortune, roll, admin) 
		VALUES (" . $post["id"] . ", '" . $post["name"] . "', '" . $post["date"]
		 . "', " . $post["reply"] . ", '" . $post["board"] . "', '" . $post["post"]
		  . " ', '" . $post["info"] . "', '" . $post["style"] . "', '" . $post["fortune"]
		  . "', '" . $post["roll"] . "', '" . $post["admin"] . "');") or die($this->db->error); 
	}
	// get a single post
	function getPost($id){
		$query = $this->db->query("SELECT * from posts WHERE id = " . $id) or die($this->db->error);
		return $query->fetch_assoc();
	}
		// get a single post
	function getPosts(){
		$query = $this->db->query("SELECT * from posts") or die($this->db->error);
		return $query;
	}
	function getThreads(){
		$query = $this->db->query("SELECT * from posts WHERE reply = 0 ORDER BY id DESC") or die($this->db->error);
		return $query;
	}
	function getReplies($id){
		$query = $this->db->query("SELECT * from posts WHERE reply = " . $id) or die($this->db->error);
		return $query;
	}
	function getReplyCount($id){
		$query = $this->db->query("SELECT COUNT(*) FROM posts WHERE reply = " . $id) or die($this->db->error);
		return $query;
	}
	function getLatestReplies($id){
		$query = $this->db->query("SELECT * from posts WHERE reply = " . $id . " ORDER BY id DESC LIMIT 5") or die($this->db->error);
		return $query;
	}
}

class render{
	// Render a thread for catalog.
	function catalog($db, $post){
		return '<a href="threads/' . $post["id"] . '.html"><div class="catalogThread">
		<span class="catalogReplies"><span class="catalogRepliesCount">' . strval($db->getReplies($post["id"])->num_rows) . '</span><span class="catalogRepliesText">replies</span></span>
		<span class="catalogContent"><span class="catalogThreadTitle">' . $post["name"] . '</span>
		<span class="catalogThreadDate">' . $post["date"] . '</span></span>
		</div></a>';
	}
	
	// Render an OP post.
	function op($post){
			return '' .
			'<span class="postHeadline">' .  $post["name"] . '</span>' .
			'<span class="postDate"> ' . $post["date"] . '</span>' .
			'<span class="postName"> No. ' . $post["id"] . '</span>' .
			'<p>' . str_replace("\r\n", "<br>" , $post["post"]) . '</p></div>';

	}
	// Render a Reply post.
	function reply($post){
		return '<div class="reply">' .
		'<span class="postName">' . $post["name"] . '</span>' .
		'<span class="postDate"> ' . $post["date"] . '</span>' .
		'<span class="postNum">' . "<a href='#" . $post["id"] . "'> No. " . $post["id"] . '</a></span>' .
		'<p>' . str_replace("\r\n", "<br>" , $post["post"]) . '</p>' .
		'</div>';

	}
	// render a new thread form
	function newThreadForm(){
		return '<div id="thread"><form action="createthread.php" method="post" id="newthread"><table><tr><h1>New Thread</h1><td id="postTable">Headline:</td><td> <input type="text" name="name" value=""><input type="submit" value="Post"></td><input type="text" name="reply" value="0" style="display:none;"><input type="text" name="board" value="lounge" style="display:none;"></tr><tr><td id="postTable">Post:</td><td><textarea name="post"></textarea></td></tr></table></form></div>';
	}
		// render a new reply form
	function newReplyForm($id){
		return '<div id="thread"><form action="createthread.php" method="post" id="newthread"><table><tr><h1>Reply</h1><td id="postTable">Name:</td><td> <input type="text" name="name" value=""><input type="submit" value="Post"></td><input type="text" name="reply" value="' . $id . '" style="display:none;"><input type="text" name="board" value="lounge" style="display:none;"></tr><tr><td id="postTable">Post:</td><td><textarea name="post"></textarea></td></tr></table></form></div>';
	}
	
}
class buildHTML{
	public $db;
	public $posts;
	function __construct($db){
		$this->db = $db; //give it the class, not the config
	}
	function insertHTML($file, $content){
		return str_replace("<!--[[CONTENT]]-->", $content,file_get_contents($file));
	}
	function threads(){
		$result = "";
		for($i=0; $i < count($this->threads);$i++){
			$result = $result . render::op($this->threads, $i);
			$repliesArray = $this->db->getLatestReplies($this->posts[$i]["id"]);
			for($x=0; $x < count($repliesArray);$x++){
				$result = $result . render::reply($repliesArray, $x);
			}
		}
		return buildHTML::insertHTML("html/threads.html", $result);
	}
	function thread($id){
		$result = "";
		$result = $result . render::op($this->db->getPost($id));
		$replies = $this->db->getReplies($id);
		while($reply = $replies->fetch_assoc()){
			$result = $result . render::reply($reply);
		}
		$result = $result . render::newReplyForm($id);
		return buildHTML::insertHTML("html/threads.html", $result);
	}
	function frontpage(){
		$result = "";
		$threads = $this->db->getThreads();
		while($thread = $threads->fetch_assoc()){
			$result = $result . render::op($thread);
			$replies = $this->db->getLatestReplies($thread["id"]);
			$result = $result . "<div id='more'><a href='thread.php?q=" . $thread["id"] . "'>See " . strval($this->db->getReplies($thread["id"])->num_rows) . " replies.</a></div>";
			while($reply = $replies->fetch_assoc()){
				$result = $result . render::reply($reply);
			}
			$result = $result . render::newReplyForm($thread["id"]);
		}
		return buildHTML::insertHTML("html/threads.html", $result);
	}
	function catalog(){
		$threads = $this->db->getThreads();
		$result = "";
		while($thread = $threads->fetch_assoc()){
			$result = $result . render::catalog($this->db, $thread);
		}
		return buildHTML::insertHTML("html/threads.html", $result);
	}
}
?>
