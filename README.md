# Introduction

My native language is not English, but Spanish, so I apologize. Well, I am developing my own content management system, but I am an amateur, not a professional developer. My first CMS was enterely written in PHP and MySQL. Several months ago, I discovered xajax, that was such an improvement over plain PHP!

I was looking for an **autosave script** in xajax. I found a very good script in [Jetlogs](http://jetlogs.org/2007/11/11/auto-saving-with-jquery/), but I wanted it in **xajax**. So I kept searching, until I found [this](http://community.xajax-project.org/post/29962/#p29962). It was a VERY GOOD script, but, can I improve it? Why it has to write a file in the server? Why not in a database?

I show you two methods:
- one for a simple form: textarea method
- another for a FCKEditor version 2

So, my script will *autosave* the record into the database.

## First of all

First of all, we have to create de database. This is the code:

		CREATE TABLE `test_db`.`draft` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`title` TEXT NULL ,
		`content` LONGTEXT NULL ,
		) ENGINE = MYISAM 

As you can see, *test_db* is the database, *draft* is the table, and *id*, *title* and *content* are the fields of the table. Of course, I asume you have privileges for creating the database and the table.

## Ok, let's begin. 

The first segment of the script is this:

		session_start();
		unset($_SESSION['article_id']); 
		require_once('../xajax/xajax_core/xajax.inc.php');
		$xajax = new xajax();

I create a session, but I destroy any previous session from previous instances. So, the first time the page loads, `$_SESSION['article_id']` will be empty.

Be careful with the paths: in my server, the xajax library is in `../xajax/xajax_core/xajax.inc.php`.

## The function in PHP

The function's name is **savetodb** (which stands for Save To DataBase; easy, isn't it?). Well, here is the function:

		function savetodb($form) {
			$title = $form["title"];
			$editor = $form["editor1"];
			$host = 'localhost'; 
			$username = 'my_user';  
			$password = 'my_pass'; 
			$database = 'test_db'; 
			$connect = mysql_connect($host, $username, $password);
			mysql_select_db($database, $connect);
			if (!isset($_SESSION['article_id'])) {
				$sql = "INSERT INTO draft (`title`, `content`) VALUES ('$title', '$editor')";
				$result = mysql_query($sql, $connect);
				$_SESSION['article_id'] = mysql_insert_id($connect);
			} else {
				$article_id = $_SESSION['article_id'];
				$sql = "UPDATE draft SET `title`='$title',`content`='$editor' WHERE `id`='$article_id'";
				$result = mysql_query($sql, $connect);
			}
			// Instantiate the object xajaxResponse
			$objResponse = new xajaxResponse();
			$objResponse->assign("autosavemsg","innerHTML", "<br />Record saved to database successfully!");
			return $objResponse;
		    
		}
		$xajax->registerFunction('savetodb');
		$xajax->processRequest();
		?>

The first 3 lines will retrieve data from the xajax.getFormValues() function (We will see it later).

The next lines are only for the database; in this example it is easier and more comprehensive, but you can `include()` a config file, if you want.

The next part is about the logics behind the SQL sentence:

- If there is no `$_SESSION['article_id']` defined, the user has just loaded the page. There is no record. As you cannot write in a non existing row, this `INSERT` command will create the row, and will retrieve the id of this row through the `mysql_insert_id()` function. This id will be *stored* in a **SESSION VARIABLE**, that's why I created the session at the beginning of the script. This session variable will be stored unless we *unset* it (if we reload the page, for instance).
- If there is a defined `$_SESSION['article_id']`, it will mean that there is a row, and this row has an identifier: Do you remember the *id* field  (primary_key and auto_increment) we created in the first step? This *id* is created automatically by the server, and through the `mysql_insert_id()` function we have it. So, the SQL sentence will only `UPDATE` the existing record.

The next lines will end the xajax function.

## The HTML code

This is the HTML code. I tried to keep it as simple as I could:

		</head>
		<body>
		<form name="form" id="form" enctype="multipart/form-data">
		<?php
		echo '<label>Title</label><input type="text" name="title" id="title"><br />';
		echo '<textarea name="editor1"></textarea>' ;
		?>
		<input type="button" name="save" onclick="xajax_savetodb(xajax.getFormValues('form'));" value="Save to Database">
		</form>

		<div id="autosavemsg"></div>

		<script language="Javascript">
			//Interval
			var AutoSaveTime=20000;
			//time object
			var AutoSaveTimer;
			SetAutoSave();
			function SetAutoSave() {
				AutoSaveTimer=setInterval("xajax_savetodb(xajax.getFormValues('form'));",AutoSaveTime);
			}
		</script>
		</body>
		</html>

OK, only the important things:

- We call for the creation of all JS scripts: `<?php $xajax->printJavascript('../xajax'); ?>`
- After we created the form, we call for the xajax function: 
		<input type="button" name="save" onclick="xajax_savetodb(xajax.getFormValues('form'));" value="Save to Database">
- And the most important thing: this Javascript code is a loop that will **run** every 20 sec. the callback to xajax function. Do you notice that it is the same function?:
		xajax_savetodb(xajax.getFormValues('form'));
So, by using the `getFormValues()` function, we can SEND the data from the form to the xajax function `savetodb()` and process the content (in this example, *title* and *content*), but you can define whatever you want.

## The complete code

You can download the complete code from `main.php`. This README was only an explanation

Do you want to use the FCKEditor version? Check `main_editor.php`.

## Important notes

- First, for the newbies, be carefull with the paths: for me, I have to write:

		require_once('../xajax/xajax_core/xajax.inc.php');
		echo $xajax->getJavascript('../xajax');

- Second: the folder **cache** must have read and write access permissions.

If the script does not work, I recommend the use of the *debug* feature:

		$xajax = new xajax();
		// this is the flag:
		$xajax->setFlag('debug', true);
		$xajax->registerFunction('autosave');

Thank you all.
