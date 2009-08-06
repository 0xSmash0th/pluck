<?php
/*
 * This file is part of pluck, the easy content management system
 * Copyright (c) somp (www.somp.nl)
 * http://www.pluck-cms.org

 * Pluck is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * See docs/COPYING for the complete license.
*/

//Make sure the file isn't accessed directly.
if (!strpos($_SERVER['SCRIPT_FILENAME'], 'index.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'admin.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'install.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'login.php')) {
	//Give out an "Access denied!" error.
	echo 'Access denied!';
	//Block all other code.
	exit;
}

function blog_page_site_list() {
	if (isset($_GET['post']) && file_exists('data/settings/modules/blog/posts/'.$_GET['post'])) {
		include ('data/settings/modules/blog/posts/'.$_GET['post']);
		$module_page_admin[] = array(
			'func'  => 'viewpost',
			'title' => $post_title
		);
	}
	return $module_page_admin;
}

//---------------
// Theme: main
//---------------
function blog_theme_main() {
	global $lang_blog14, $lang_blog23;

	//Display existing posts, but only if post-index file exists.
	if (file_exists('data/settings/modules/blog/post_index.dat')) {
		$handle = fopen('data/settings/modules/blog/post_index.dat', 'r');
		while (!feof($handle)) {
			$file = fgets($handle, 4096);
			//Filter out line breaks.
			$file = str_replace ("\n",'', $file);
			//Check if post exists.
			if (file_exists('data/settings/modules/blog/posts/'.$file) && is_file('data/settings/modules/blog/posts/'.$file)) {
				//Include post information.
				include_once ('data/settings/modules/blog/posts/'.$file);
				?>
					<div class="blogpost">
						<span class="posttitle">
							<a href="?module=blog&amp;page=viewpost&amp;post=<?php echo $file; ?>&amp;pageback=<?php echo CURRENT_PAGE_FILENAME; ?>" title="post <?php echo $post_title; ?>"><?php echo $post_title; ?></a>
						</span>
						<br />
						<span class="postinfo">
							<?php echo $lang_blog14; ?> <span style="font-weight: bold;"><?php echo $post_category; ?></span> - <?php echo $post_month; ?>-<?php echo $post_day; ?>-<?php echo $post_year; ?>, <?php echo $post_time; ?>
						</span>
						<br /><br />
						<?php echo $post_content; ?>
						<p>
							<a href="?module=blog&amp;page=viewpost&amp;post=<?php echo $file; ?>&amp;pageback=<?php echo CURRENT_PAGE_FILENAME; ?>" title="<?php echo $lang_blog23; ?>">&raquo; <?php echo $lang_blog23; ?></a>
						</p>
					</div>
				<?php
			}
		}
		//Close module-dir.
		fclose($handle);
	}
}

//---------------
// Page: viewpost
//---------------
function blog_page_site_viewpost() {
	//Global language variables
	global $lang, $lang_blog14, $lang_blog16, $lang_blog17, $lang_blog18, $lang_contact3, $lang_contact5, $lang_contact6, $lang_contact10;

	//Load blogpost
	if (isset($_GET['post']) && file_exists('data/settings/modules/blog/posts/'.$_GET['post']))
		include ('data/settings/modules/blog/posts/'.$_GET['post']);
	?>
		<div class="blogpost">
			<span class="postinfo">
				<?php echo $lang_blog14; ?> <span style="font-weight: bold;"><?php echo $post_category; ?></span> - <?php echo $post_month; ?>-<?php echo $post_day; ?>-<?php echo $post_year; ?>, <?php echo $post_time; ?>
			</span><br /><br />
			<?php echo $post_content; ?>
		</div>
		<div style="margin-top: 10px;">
			<span style="font-size: 19px"><?php echo $lang_blog16; ?></span>
			<?php
				//Then show the reactions.
				//Check if there are reactions.
				if (isset($post_reaction_title)) {
					foreach ($post_reaction_title as $key => $value) {
					?>
						<div class="blogpost_reaction">
							<span class="posttitle">
								<?php echo $post_reaction_title[$key]; ?>
							</span>
							<br />
							<span class="postinfo">
								<?php echo $lang_blog18; ?> <span style="font-weight: bold;"><?php echo $post_reaction_name[$key]; ?></span> -  <?php echo $post_reaction_month[$key]; ?>-<?php echo $post_reaction_day[$key]; ?>-<?php echo $post_reaction_year[$key]; ?>, <?php echo $post_reaction_time[$key]; ?>
							</span>
							<br />
							<?php
								//Change linebreaks in html-breaks
								$post_reaction_content_br = str_replace("\n", '<br />', $post_reaction_content[$key]);
								//Display post
								echo $post_reaction_content_br;
							?>
						</div>
					<?php
				}
				unset($key);
			}
		//Show a form to post new reactions
	?>
		<form method="post" action="" style="margin-top: 5px; margin-bottom: 15px;">
			<div>
				<label><?php echo $lang_blog17; ?></label>
				<br />
				<input name="title" type="text" />
				<br />
				<label><?php echo $lang_contact3; ?></label>
				<br />
				<input name="name" type="text" />
				<br />
				<label><?php echo $lang_contact5; ?></label>
				<br />
				<textarea name="message" rows="7" cols="45"></textarea>
				<br />
				<input type="submit" name="Submit" value="<?php echo $lang_contact10; ?>" />
			</div>
		</form>
	</div>

<?php
	//If form is posted...
	if (isset($_POST['Submit'])) {
		require_once('data/modules/blog/functions.php');

		//Check if everything has been filled in.
		if (empty($_POST['title']) || empty($_POST['name']) || empty($_POST['message']))
			echo '<span style="color: red;">'.$lang_contact6.'</span>';

		//Add reaction.
		else {
			blog_save_reaction($_GET['post'], $_POST['title'], $_POST['name'], $_POST['message']);

			//Redirect user.
			redirect('?module=blog&page=viewpost&post='.$_GET['post'].'&pageback='.$_GET['pageback'], 0);
		}
	}

	//Show back link
	if (isset($_GET['pageback'])) { ?>
	<p>
		<a href="?file=<?php echo $_GET['pageback']; ?>" title="<?php echo $lang['general']['back']; ?>">&lt;&lt;&lt; <?php echo $lang['general']['back']; ?></a>
	</p>
<?php
	}
}
?>