CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateEvents`()
BEGIN
    SELECT *
    INTO @t_id, @t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_end, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_recursion_type, @t_recursion_interval, @t_monday, @t_tuesday, @t_wednesday, @t_thursday, @t_friday, @t_saturday, @t_sunday
  FROM e_recuring_events ORDER BY ID DESC LIMIT 1;

    -- Add first date
    INSERT INTO e_events (`title`, `description`, `cal_id`, `project_id`, `start`, `end`, `starttime`, `endtime`, `event_owner`, `pgm_id`, `event_user_id`, `email_user_id`, `signups`, `display_signups`, `block_signups`, `event_location`, `event_address`, `event_url`, `event_contact`, `event_file`, `event_file_size`, `status`, `created`, `updated`, `template_id`) VALUES
      (@t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_start, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_id);

    SET @counter = 0;
    
    -- Daily
    IF @t_recursion_type = 0 THEN
    REPEAT
      SET @t_start = date_add(@t_start, INTERVAL 1 DAY);
      IF @counter < 365 AND dayofweek(@t_start) = 1 AND @t_sunday = 1 OR dayofweek(@t_start) = 2 AND @t_monday = 1 OR dayofweek(@t_start) = 3 AND @t_tuesday = 1 OR dayofweek(@t_start) = 4 AND @t_wednesday = 1 OR dayofweek(@t_start) = 5 AND @t_thursday = 1 OR dayofweek(@t_start) = 6 AND @t_friday = 1 OR dayofweek(@t_start) = 7 AND @t_saturday = 1 THEN
        INSERT INTO e_events (`title`, `description`, `cal_id`, `project_id`, `start`, `end`, `starttime`, `endtime`, `event_owner`, `pgm_id`, `event_user_id`, `email_user_id`, `signups`, `display_signups`, `block_signups`, `event_location`, `event_address`, `event_url`, `event_contact`, `event_file`, `event_file_size`, `status`, `created`, `updated`, `template_id`) VALUES
        (@t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_start, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_id);
          SET @counter = @counter + 1;
      END IF;
    UNTIL @t_start >= @t_end
    END REPEAT;
    END IF;
    -- Weekly
    IF @t_recursion_type = 1 THEN
    REPEAT
      SET @t_start = date_add(@t_start, INTERVAL (7 * @t_recursion_interval) DAY);
      IF @counter < 365 AND @t_start <= @t_end THEN -- Don't craete events after the end date
        INSERT INTO e_events (`title`, `description`, `cal_id`, `project_id`, `start`, `end`, `starttime`, `endtime`, `event_owner`, `pgm_id`, `event_user_id`, `email_user_id`, `signups`, `display_signups`, `block_signups`, `event_location`, `event_address`, `event_url`, `event_contact`, `event_file`, `event_file_size`, `status`, `created`, `updated`, `template_id`) VALUES
        (@t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_start, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_id);
        SET @counter = @counter + 1;
      END IF;
        UNTIL @t_start >= @t_end
        END REPEAT;
    END IF;
    -- Monthly
  IF @t_recursion_type = 2 THEN
    -- Same date every month
    IF @t_recursion_interval = 0 THEN
      REPEAT
        SET @t_start = date_add(@t_start, INTERVAL 1 MONTH);
        IF @counter < 365 AND @t_start <= @t_end THEN -- Don't craete events after the end date
          INSERT INTO e_events (`title`, `description`, `cal_id`, `project_id`, `start`, `end`, `starttime`, `endtime`, `event_owner`, `pgm_id`, `event_user_id`, `email_user_id`, `signups`, `display_signups`, `block_signups`, `event_location`, `event_address`, `event_url`, `event_contact`, `event_file`, `event_file_size`, `status`, `created`, `updated`, `template_id`) VALUES
          (@t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_start, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_id);
          SET @counter = @counter + 1;
        END IF;
      UNTIL @t_start >= @t_end
      END REPEAT;
    -- Same day of week every month
    ELSE
      -- Some setup for this part
      SET @t_start = date_sub(@t_start, INTERVAL 1 MONTH);
      SET @t_start = last_day(@t_start);
      SET @holding_date = @t_start;
      SET @found_days = 0;
      REPEAT -- Iterate through every day until we reach the end date
        SET @t_start = date_add(@t_start, INTERVAL 1 DAY);
        IF month(@holding_date) != month(@t_start) THEN
          SET @holding_date = @t_start;
          SET @found_days = 0; -- Reset our counter every month
        END IF;
        IF dayofweek(@t_start) = 1 AND @t_sunday = 1 OR dayofweek(@t_start) = 2 AND @t_monday = 1 OR dayofweek(@t_start) = 3 AND @t_tuesday = 1 OR dayofweek(@t_start) = 4 AND @t_wednesday = 1 OR dayofweek(@t_start) = 5 AND @t_thursday = 1 OR dayofweek(@t_start) = 6 AND @t_friday = 1 OR dayofweek(@t_start) = 7 AND @t_saturday = 1 THEN
          SET @found_days = @found_days + 1; -- Increment the counter for each day we find
        END IF;
        IF @counter < 365 AND @t_recursion_interval = @found_days AND @t_start >= now() THEN
          INSERT INTO e_events (`title`, `description`, `cal_id`, `project_id`, `start`, `end`, `starttime`, `endtime`, `event_owner`, `pgm_id`, `event_user_id`, `email_user_id`, `signups`, `display_signups`, `block_signups`, `event_location`, `event_address`, `event_url`, `event_contact`, `event_file`, `event_file_size`, `status`, `created`, `updated`, `template_id`) VALUES
          (@t_title, @t_description, @t_cal_id, @t_project_id, @t_start, @t_start, @t_starttime, @t_endtime, @t_event_owner, @t_pgm_id, @t_event_user_id, @t_email_user_id, @t_signups, @t_display_signups, @t_block_signups, @t_event_location, @t_event_address, @t_event_url, @t_event_contact, @t_event_file, @t_event_file_size, @t_status, @t_created, @t_updated, @t_id);
          SET @counter = @counter + 1;
          SET @found_days = 99; -- Only once per month allowed anyway
        END IF;
      UNTIL @t_start >= @t_end
      END REPEAT;
        END IF;
    END IF;
END