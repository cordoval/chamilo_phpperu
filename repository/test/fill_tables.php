<?php
/**
 * $Id: fill_tables.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.test
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

Translation :: set_application('repository');

Display :: header();
set_time_limit(0);

$form = new FormValidator('create_random');
$form->addElement('submit', 'go', 'go');

if (! $form->isSubmitted())
{
    Display :: warning_message('By running this script, all existing users and learning objects will be deleted and replaced by randomly created users and learning objects. Only use this for testing purpuses.');
    echo 'If you\'re sure to continue, click the button below';
    $form->display();
}
else
{
    /**
     * This is a simple test script that removes all learning objects from the LCMS
     * data source and fills it with garbage data. For testing purposes only.
     *
     * @author Tim De Pauw
     * @package repository
     */
    
    $users = 200;
    
    $max_categories = array(2, 3);
    
    $announcements = rand(200, 1000);
    $calendar_events = rand(200, 1000);
    $documents = rand(200, 1000);
    $links = rand(500, 1000);
    $forums = rand(100, 500);
    $forum_topics = rand(10, 5000);
    $forum_posts = rand(60, 1200);
    $questions_fill_in_blanks = rand(200, 100);
    $questions_multiple_choice = rand(200, 100);
    
    // TODO
    //$learning_paths = rand(100,500);
    

    $randomTexts[] = 'Lorem ipsum dolor sit amet consectetuer adipiscing elit Cras vel erat Phasellus est Curabitur nunc leo laoreet eu varius sit amet faucibus faucibus magna Quisque venenatis ante quis dictum sodales orci velit molestie';
    $words = array();
    foreach ($randomTexts as $index => $randomText)
    {
        $words = array_merge($words, explode(' ', $randomText));
    }
    
    // Create some random users
    $usermanager = UserDataManager :: get_instance();
    $usermanager->delete_all_users();
    
    title('Create users');
    for($user_nr = 1; $user_nr <= $users; $user_nr ++)
    {
        $user = new User();
        $user->set_firstname(random_word());
        $user->set_lastname(random_word());
        $user->set_username('user' . $user_nr);
        $user->set_password(md5('user' . $user_nr));
        $user->set_email('user' . $user_nr . '@example.com');
        $user->set_official_code('USER' . $user_nr);
        $user->set_language('english');
        $user->set_status(5);
        $user->set_creator_id(Session :: get_user_id());
        $user->create();
        $user_ids[$user_nr] = $user->get_id();
        progress();
    }
    //exit;
    // Create some random learning objects
    $dataManager = RepositoryDataManager :: get_instance();
    
    $dataManager->delete_all_content_objects();
    title('Categories');
    for($u = 1; $u <= $users; $u ++)
    {
        create_category($user_ids[$u]);
    }
    title('Announcements');
    for($i = 0; $i < $announcements; $i ++)
    {
        $user = random_user();
        $announcement = new Announcement();
        $announcement->set_owner_id($user);
        $announcement->set_title(random_string(2));
        $announcement->set_description(random_string(8));
        $announcement->set_parent_id(random_category($user));
        $announcement->create();
        for($j = 0; $j < rand(1, 5); $j ++)
        {
            $announcementobjectnumber = $announcement->get_object_number();
            $announcement->set_object_number($announcementobjectnumber);
            $announcement->set_title(random_string(2));
            $announcement->set_description(random_string(8));
            $announcement->version();
        }
        progress();
    }
    title('Calendar Events');
    for($i = 0; $i < $calendar_events; $i ++)
    {
        $user = random_user();
        $event = new CalendarEvent();
        $event->set_owner_id($user);
        $event->set_title(random_string(2));
        $event->set_description(random_string(8));
        $event->set_parent_id(random_category($user));
        $start_date = rand(strtotime('-1 Month', time()), strtotime('+1 Month', time()));
        $end_date = rand($start_date + 1, strtotime('+1 Month', $start_date));
        $event->set_start_date($start_date);
        $event->set_end_date($end_date);
        $event->create();
        for($j = 0; $j < rand(2, 5); $j ++)
        {
            $eventobjectnumber = $event->get_object_number();
            $event->set_object_number($eventobjectnumber);
            $event->set_title(random_string(2));
            $event->set_description(random_string(8));
            $event->version();
        }
        progress();
    }
    title('Documents');
    for($i = 0; $i < $documents; $i ++)
    {
        $user = random_user();
        $document = new Document();
        $document->set_owner_id($user);
        $document->set_title(random_string(2));
        $document->set_description(random_string(8));
        $filename = random_filename();
        $path = $user . '/' . $filename;
        $filesize = create_random_file_content($path);
        $document->set_path($path);
        $document->set_filename($filename);
        $document->set_filesize($filesize);
        $document->set_parent_id(random_category($user));
        $document->create();
        for($j = 0; $j < rand(2, 5); $j ++)
        {
            $docobjectnumber = $document->get_object_number();
            $document->set_object_number($docobjectnumber);
            $document->set_title(random_string(2));
            $document->set_description(random_string(8));
            $filename = random_filename();
            $path = $user . '/' . $filename;
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
    for($i = 0; $i < $links; $i ++)
    {
        $user = random_user();
        $link = new Link();
        $link->set_owner_id($user);
        $link->set_title(random_string(2));
        $link->set_description(random_string(8));
        $link->set_url(random_url());
        $link->set_parent_id(random_category($user));
        $link->create();
        for($j = 0; $j < rand(2, 5); $j ++)
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
    $created_forums = array();
    for($i = 0; $i < $forums; $i ++)
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
    $created_forum_topics = array();
    $topic_to_forum = array();
    for($i = 0; $i < $forum_topics; $i ++)
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
    for($i = 0; $i < $forum_posts - $forum_topics; $i ++)
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
    for($i = 0; $i < $questions_fill_in_blanks; $i ++)
    {
        $user = random_user();
        $question = new FillInBlanksQuestion();
        $question->set_owner_id($user);
        $question->set_title(random_string(2));
        $question->set_description(random_string(8));
        $question->set_parent_id(random_category($user));
        $question->set_answer(random_string(2) . '[' . random_word . ']' . random_string(3) . '[' . random_word() . ']');
        $question->create();
        progress();
    }
    title('Questions: Multiple Choice');
    for($i = 0; $i < $questions_multiple_choice; $i ++)
    {
        $user = random_user();
        $question = new MultipleChoiceQuestion();
        $question->set_owner_id($user);
        $question->set_title(random_string(2));
        $question->set_description(random_string(8));
        $question->set_parent_id(random_category($user));
        $question->set_answer_type('checkbox');
        $options = array();
        for($j = 0; $j < 3; $j ++)
        {
            $options[] = new MultipleChoiceQuestionOption(random_word(), rand(0, 1), rand(0, 5));
        }
        $question->set_options($options);
        $question->create();
        
        progress();
    }
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
?>