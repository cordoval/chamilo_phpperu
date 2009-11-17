<?php
/**
 * $Id: testcvs.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.test
 */
/* 
This file is a temporary test file , used by the students of HoGent to test csv imports for the entire Learning Objects
The file is based on the fill_tables file and has been modded by Maarten Dauwe
*/

require_once dirname(__FILE__) . '/../../common/global.inc.php';
Translation :: set_application('repository');
Display :: header();
set_time_limit(0);
// create new form 
$form = new FormValidator('testbestand');
$form->addElement('submit', 'doen', 'doen');
// if form isnt submitted , the form will be displayed so the user can choose the csv file to upload
//@todo : implement csv import stuff 


if (! $form->isSubmitted())
{
    Display :: normal_message('Hiermee vullen we de databank als test...');
    
    $form->display();
}
else
{
    
    // The user has submitted the form , now we have to check if value's are correct and 
    

    $dataManager = RepositoryDataManager :: get_instance();
    // clear the entire learning object database 
    // WARNING , the entire learning object database will be wiped
    $dataManager->delete_all_content_objects();
    title('Er wordt een nieuwe Category gemaakt (vroegere zijn gewist)');
    
    create_category(Session :: get_user_id());
    
    title('5 willekeurige Announcements worden aangemaakt : ');
    for($i = 0; $i < 5; $i ++)
    {
        $user = Session :: get_user_id();
        //$parent= $dataManager->retrieve_root_category($user);
        //echo $parent;
        $test = parent_split($parent);
        //echo $test;
        $announcement = new Announcement();
        $announcement->set_owner_id($user);
        $announcement->set_title('titel' . $i);
        $announcement->set_description('beschrijving' . $i);
        $announcement->set_parent_id($test);
        $announcement->create();
        progress();
    }
    title('Kalender Evenementen worden aangemaakt ');
    for($i = 0; $i < 5; $i ++)
    {
        $user = Session :: get_user_id();
        $event = new CalendarEvent();
        $event->set_owner_id($user);
        $event->set_title('Titel' . $i);
        $event->set_description('omschrijving' . $i);
        $parent = $dataManager->retrieve_root_category($user);
        //echo $parent;
        $test = parent_split($parent);
        //echo $test;
        $event->set_parent_id($test);
        $start_date = rand(strtotime('-1 Month', time()), strtotime('+1 Month', time()));
        $end_date = rand($start_date + 1, strtotime('+1 Month', $start_date));
        $event->set_start_date($start_date);
        $event->set_end_date($end_date);
        $event->create();
        
        progress();
    } /*
	title('Documents');
	for ($i = 0; $i < $documents; $i ++)
	{
		$user = random_user();
		$document = new Document();
		$document->set_owner_id($user);
		$document->set_title(random_string(2));
		$document->set_description(random_string(8));
		$filename = random_filename();
		$path = $user.'/'.$filename;
		$filesize = create_random_file_content($path);
		$document->set_path($path);
		$document->set_filename($filename);
		$document->set_filesize($filesize);
		$document->set_parent_id(random_category($user));
		$document->create();
		for ($j = 0; $j < rand(2, 5); $j ++)
		{
			$docobjectnumber = $document->get_object_number();
			$document->set_object_number($docobjectnumber);
			$document->set_title(random_string(2));
			$document->set_description(random_string(8));
			$filename = random_filename();
			$path = $user.'/'.$filename;
			$filesize = create_random_file_content($path);
			$document->set_path($path);
			$document->set_filename($filename);
			$document->set_filesize($filesize);
			;
			$document->version();
		}
		progress();
	}
	title('Links');
	for ($i = 0; $i < $links; $i ++)
	{
		$user = random_user();
		$link = new Link();
		$link->set_owner_id($user);
		$link->set_title(random_string(2));
		$link->set_description(random_string(8));
		$link->set_url(random_url());
		$link->set_parent_id(random_category($user));
		$link->create();
		for ($j = 0; $j < rand(2, 5); $j ++)
		{
			$linkobjectnumber = $link->get_object_number();
			$link->set_object_number($linkobjectnumber);
			$link->set_title(random_string(2));
			$link->set_description(random_string(8));
			$link->set_url(random_url());
			$link->version();
		}
		progress();
	}
	title('Forums');
	$created_forums = array ();
	for ($i = 0; $i < $forums; $i ++)
	{
		$user = random_user();
		$forum = new Forum();
		$forum->set_owner_id($user);
		$forum->set_title(random_string(2));
		$forum->set_description(random_string(8));
		$forum->set_parent_id(random_category($user));
		$forum->create();
		$created_forums[] = $forum;
		progress();
	}
	title('Forum Topics');
	$created_forum_topics = array ();
	$topic_to_forum = array ();
	for ($i = 0; $i < $forum_topics; $i ++)
	{
		$forum = random_forum();
		$user = random_user();
		$topic = new ForumTopic();
		$topic->set_owner_id($user);
		$topic->set_title(random_string(2));
		$topic->set_description(random_string(8));
		$topic->set_parent_id($forum->get_id());
		$topic->create();
		$created_forum_topics[] = $topic;
		// Map topic to its forum object, for convenience.
		$topic_to_forum[$topic->get_id()] = $forum;
		// Every topic needs at least one post.
		$post = new ForumPost();
		$post->set_owner_id($user);
		$post->set_title(random_string(2));
		$post->set_description(random_string(8));
		$post->set_parent_id($topic->get_id());
		$post->create();
		progress();
	}
	title('Forum Posts');
	for ($i = 0; $i < $forum_posts - $forum_topics; $i ++)
	{
		$user = random_user();
		$topic = random_forum_topic();
		$post = new ForumPost();
		$post->set_owner_id($user);
		$post->set_title(random_string(2));
		$post->set_description(random_string(8));
		$post->set_parent_id($topic->get_id());
		$post->create();
		progress();
	}

	foreach ($created_forum_topics as $topic)
	{
		$topic->update();
	}

	foreach ($created_forums as $forum)
	{
		$forum->update();
	}
	title('Questions: Fill in blanks');
	for ($i = 0; $i < $questions_fill_in_blanks; $i ++)
	{
		$user = random_user();
		$question = new FillInBlanksQuestion();
		$question->set_owner_id($user);
		$question->set_title(random_string(2));
		$question->set_description(random_string(8));
		$question->set_parent_id(random_category($user));
		$question->set_answer(random_string(2).'['.random_word.']'.random_string(3).'['.random_word().']');
		$question->create();
		progress();
	}
	title('Questions: Multiple Choice');
	for ($i = 0; $i < $questions_multiple_choice; $i ++)
	{
		$user = random_user();
		$question = new MultipleChoiceQuestion();
		$question->set_owner_id($user);
		$question->set_title(random_string(2));
		$question->set_description(random_string(8));
		$question->set_parent_id(random_category($user));
		$question->set_answer_type('checkbox');
		$options = array ();
		for ($j = 0; $j < 3; $j ++)
		{
			$options[] = new MultipleChoiceQuestionOption(random_word(), rand(0, 1), rand(0, 5));
		}
		$question->set_options($options);
		$question->create();

		progress();
	}*/
}
Display :: footer();

function random_url()
{
    return 'http://www.example.com/~' . totally_random_word(8) . '/' . str_replace(' ', '%20', random_string(2)) . '.' . totally_random_word(3);
}

function random_user()
{
    global $user_ids;
    return $user_ids[rand(1, count($user_ids))];
}

function random_string($length)
{
    $words = array();
    for($i = 0; $i < $length; $i ++)
    {
        $words[] = random_word();
    }
    return implode(' ', $words);
}

function create_random_file_content($path)
{
    $string = random_string(50);
    $path = Path :: get(SYS_REPO_PATH) . $path;
    $handle = fopen($path, 'w+');
    fwrite($handle, $string);
    fclose($handle);
    return filesize($path);
}

function totally_random_word($length)
{
    $str = '';
    for($i = 0; $i < $length; $i ++)
    {
        $str .= chr(rand(97, 122));
    }
    return $str;
}

function random_word()
{
    global $words;
    return random_array_element($words);
}

function random_filename()
{
    return substr(md5(uniqid()), 0, rand(5, 10)) . '.txt';
}

function random_category($owner)
{
    global $created_categories;
    return random_array_element($created_categories[$owner]);
}

function random_forum()
{
    global $created_forums;
    return random_array_element($created_forums);
}

function random_forum_topic()
{
    global $created_forum_topics;
    return random_array_element($created_forum_topics);
}

function random_array_element($array)
{
    return $array[rand(0, count($array) - 1)];
}

function create_category($owner, $parent = 0, $level = 0)
{
    global $max_categories, $created_categories;
    $cat = new Category();
    $cat->set_owner_id($owner);
    $cat->set_parent_id($parent);
    $cat->set_title($parent == 0 ? 'My Repository' : random_string(2));
    $cat->set_description(random_string(8));
    $cat->create();
    $id = $cat->get_id();
    if (! $max_categories[$level])
    {
        return;
    }
    $count = rand(1, $max_categories[$level]);
    for($i = 0; $i < $count; $i ++)
    {
        create_category($owner, $id, $level + 1);
        progress();
    }
    $created_categories[$owner][] = $id;
}

function title($title)
{
    echo '<br /><strong>' . $title . '</strong>';
}

function progress()
{
    echo ' =';
    flush();
}

function parent_split($parent)
{
    echo $parent;
    $aparent = explode('#', $parent);
    $bparent = explode(' ', $aparent[1]);
    //echo '<br /><strong>'.$aparent[1].'</strong>';
    //echo '<br /><strong>'.$bparent[0].'</strong>';
    return $bparent[0];

}

?>
