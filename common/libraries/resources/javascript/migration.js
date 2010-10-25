/*
 * The javascript part of the migration tool to make sure the user can't deselect wrong things
 *
 * @author Sven Vanpoucke
 * 
 */
 
function users_clicked()
{
	if(document.page_ssettings.migrate_users.checked == false)
	{
		document.page_ssettings.migrate_personal_agendas.checked = false;
		document.page_ssettings.migrate_settings.checked = false;
		document.page_ssettings.migrate_classes.checked = false;
		document.page_ssettings.migrate_courses.checked = false;
		
		document.page_ssettings.migrate_metadata.checked = false;
		document.page_ssettings.migrate_groups.checked = false;
		document.page_ssettings.migrate_announcements.checked = false;
		document.page_ssettings.migrate_calendar_events.checked = false;
		document.page_ssettings.migrate_documents.checked = false;
		document.page_ssettings.migrate_links.checked = false;
		document.page_ssettings.migrate_dropboxes.checked = false;
		document.page_ssettings.migrate_forums.checked = false
		document.page_ssettings.migrate_learning_paths.checked = false
		document.page_ssettings.migrate_quizzes.checked = false
		document.page_ssettings.migrate_student_publications.checked = false
		document.page_ssettings.migrate_surveys.checked = false
		document.page_ssettings.migrate_scorms.checked = false
		document.page_ssettings.migrate_assignments.checked = false
		document.page_ssettings.migrate_userinfos.checked = false
		document.page_ssettings.migrate_trackers.checked = false
	}
}

function personal_agendas_clicked()
{
	if(document.page_ssettings.migrate_personal_agendas.checked == true)
	{
		document.page_ssettings.migrate_users.checked = true;
	}
}

function settings_clicked()
{
	if(document.page_ssettings.migrate_settings.checked == true)
	{
		document.page_ssettings.migrate_users.checked = true;
	}
}

function classes_clicked()
{
	if(document.page_ssettings.migrate_classes.checked == true)
	{
		document.page_ssettings.migrate_users.checked = true;
	}
}

function courses_clicked()
{
	if(document.page_ssettings.migrate_courses.checked == false)
	{
		document.page_ssettings.migrate_metadata.checked = false;
		document.page_ssettings.migrate_groups.checked = false;
		document.page_ssettings.migrate_announcements.checked = false;
		document.page_ssettings.migrate_calendar_events.checked = false;
		document.page_ssettings.migrate_documents.checked = false;
		document.page_ssettings.migrate_links.checked = false;
		document.page_ssettings.migrate_dropboxes.checked = false;
		document.page_ssettings.migrate_forums.checked = false
		document.page_ssettings.migrate_learning_paths.checked = false
		document.page_ssettings.migrate_quizzes.checked = false
		document.page_ssettings.migrate_student_publications.checked = false
		document.page_ssettings.migrate_surveys.checked = false
		document.page_ssettings.migrate_scorms.checked = false
		document.page_ssettings.migrate_assignments.checked = false
		document.page_ssettings.migrate_userinfos.checked = false
		document.page_ssettings.migrate_trackers.checked = false
	}
	else
	{
		document.page_ssettings.migrate_users.checked = true;
	}
}

function groups_clicked()
{
	if(document.page_ssettings.migrate_groups.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function metadata_clicked()
{
	if(document.page_ssettings.migrate_metadata.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function announcements_clicked()
{
	if(document.page_ssettings.migrate_announcements.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function calendar_events_clicked()
{
	if(document.page_ssettings.migrate_calendar_events.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function documents_clicked()
{
	if(document.page_ssettings.migrate_documents.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function links_clicked()
{
	if(document.page_ssettings.migrate_links.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function dropboxes_clicked()
{
	if(document.page_ssettings.migrate_dropboxes.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true
	}
}

function forums_clicked()
{
	if(document.page_ssettings.migrate_forums.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function learning_paths_clicked()
{
	if(document.page_ssettings.migrate_learning_paths.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function quizzes_clicked()
{
	if(document.page_ssettings.migrate_quizzes.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function student_publications_clicked()
{
	if(document.page_ssettings.migrate_student_publications.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function surveys_clicked()
{
	if(document.page_ssettings.migrate_surveys.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function scorms_clicked()
{
	if(document.page_ssettings.migrate_scorms.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function assignments_clicked()
{
	if(document.page_ssettings.migrate_assignments.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function userinfos_clicked()
{
	if(document.page_ssettings.migrate_userinfos.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function trackers_clicked()
{
	if(document.page_ssettings.migrate_trackers.checked == true)
	{
		document.page_ssettings.migrate_courses.checked = true;
		document.page_ssettings.migrate_users.checked = true;
	}
}

function deleted_files_clicked(message)
{
	if(document.page_ssettings.migrate_deleted_files.checked == true)
	{
		var really_delete = confirm(message);
		
		if(!really_delete)
		{
			document.page_ssettings.migrate_deleted_files.checked = false;
		}
	}
}

function move_files_clicked(message)
{
	if(document.page_ssettings.move_files.checked == true)
	{
		var really_move = confirm(message);
		
		if(!really_move)
		{
			document.page_ssettings.move_files.checked = false;
		}
	}
}