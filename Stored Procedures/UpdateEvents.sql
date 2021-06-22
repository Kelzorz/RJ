CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateEvents`(IN arg1 INT)
BEGIN
  SELECT *
    INTO @t_id, @t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_end, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_recursion_type, @t_recursion_interval, @t_monday, @t_tuesday, @t_wednesday, @t_thursday, @t_friday, @t_saturday, @t_sunday
  FROM e_recuring_events WHERE id = arg1 ORDER BY ID DESC LIMIT 1;
    
    UPDATE e_events
    SET title = @t_title, `description` = @t_description, cal_id = @t_cal_id, project_id = @t_project_id, starttime = @t_starttime, endtime = @t_endtime, event_owner = @t_event_owner, pgm_id = @t_pgm_id, event_user_id = @t_event_user_id, signups = @t_signups, display_signups = @t_display_signups, block_signups = @t_block_signups, event_location = @t_event_location, event_address = @t_event_address, event_url = @t_event_url, event_contact = @t_event_contact, event_file = @t_event_file, event_file_size = @t_event_file_size, `status` = @t_status, created = @t_created, updated = @t_updated
    WHERE template_id = arg1;
END