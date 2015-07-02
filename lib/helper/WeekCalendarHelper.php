<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php
use_helper('Asset', 'Javascript');

function _WeekCalendar_common()
{
    use_javascript('/jquery-weekcalendar/jquery.weekcalendar.js','last');
    use_stylesheet('/jquery-weekcalendar/jquery.weekcalendar.css','first');
}

function WeekCalendar($name, $json_events)
{
 _WeekCalendar_common();
 $first_day = SchoolBehaviourFactory::getInstance()->getFirstCourseSubjectWeekday();
 $days_to_show = SchoolBehaviourFactory::getInstance()->getLastCourseSubjectWeekday()-SchoolBehaviourFactory::getInstance()->getFirstCourseSubjectWeekday()+1;
 $day_names_to_sort = CourseSubjectDay::geti18Names();
 $order = array(7,1,2,3,4,5,6,7);
 foreach($order as $index)
 {
   $day_names[]= $day_names_to_sort[$index];
 }
 $day_names = json_encode($day_names);
 $hours = SchoolBehaviourFactory::getInstance()->getHoursArrayForSubjectWeekday();
 ksort($hours,SORT_NUMERIC);
 $start_hour = array_shift($hours);
 $end_hour = count($hours) > 0 ? array_pop($hours): $start_hour;
 if ( ++$end_hour > 24) $end_hour--;
 return '<div id="'.$name.'"></div>'.javascript_tag("
   jQuery(document).ready(function() {
    jQuery('#$name').weekCalendar({
        readonly: true,
        overlapEventsSeparate: true,
        timeSeparator: ' - ',
        timeslotsPerHour: 4,
        buttons: false,
        firstDayOfWeek: $first_day,
        daysToShow: $days_to_show,
        height: function(\$calendar){
          return jQuery(window).height() - jQuery(\"h1\").outerHeight();
        },
        longDays: $day_names ,
        headerShowDay: false,
        highlightToday: false,
        use24Hour: true,
        businessHours: {start: $start_hour, end: $end_hour, limitDisplay: true},
        data: { events: $json_events },

    });
   });
 ");
}