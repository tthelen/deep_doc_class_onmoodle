<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

error_reporting(E_ALL); // TODO remove later

define("OUTPUT_PATH", "/tmp");
define("OUTPUT_CSV_FILENAME", "course_%s.csv");
define("OUTPUT_LIST_FILENAME", "course_%s_files.txt");
define("OUTPUT_CSV_HEADER", "document_id, filename, folder_name, folder_description, description\n");

/* aim: 
A metadata csv (comma separated) file containing the following columns (please make sure that the values on the csv have no quotes)
document_id = unique id of the file which should be the name of the pdf without the '.pdf' extension
filename = the name of the file (in case the name of the file on the system is different than the id)
folder_name = the name of the folder on the system where the file is located
folder_description = a description of the folder where the file is located
description = description of the file
csv expected: document_id, filename, folder_name, folder_description, description
moodle mapping: <f.contenthash>, <f.filename>, "[$cm->name] <filepathinmod>", <f.component>, ??? (<f.filename>? filetype by extension? <cm.visible>? <cmid>?)

..one CSV per course.
another "metadata" file containing names + preferred e-mail of teachers
(can we combine all e-mails per teacher?)
*/

// obtain courses (maybe starting at "last successful course", see below)
$startingfrom = 1554069600; // Timestamp of April 1, 2019  0:00 CEST ( == Summer Term 2019)
$courses = $DB->get_records_sql('select id from {course} where startdate >= :startingfrom', ['startingfrom' => $startingfrom]);

foreach ($courses as $course) {
	$cid = $course['id'];
	$currentcourse = get_course($cid);

	// TODO: remove irrelevant columns
	$files = $DB->get_records_sql('select * from (
	--course-level files (legacy course files)
	select 
	f.id as fid, c.id as courseid, c.fullname, f.contenthash, f.filename, 1 as "visible", \'legacy\' as "component", 0 as "cmid", \'/\' as "filepathinmod"
	from {files} f
	join {context} ctx on ctx.id = f.contextid
	join {course} c on ctx.instanceid = c.id
	where ctx.contextlevel = 50 -- courses
	  and f.filename <> \'.\'
	  and c.id = ?
	--;c
	UNION
	--module-level files (e.g. new files)
	select 
	f.id as fid, c.id as courseid, c.fullname, f.contenthash, f.filename, cm.visible, f.component, cm.id as "cmid", f.filepath as "filepathinmod"
	from {files} f
	join {context} ctx on ctx.id = f.contextid
	join {course_modules} cm on ctx.instanceid = cm.id
	join {course} c on c.id = cm.course
	where ctx.contextlevel = 70 -- module instances
	  and f.filename <> \'.\'
	  and c.id = ?
	  and f.component not in (\'mod_forum\', \'mod_data\', \'question\', \'assignsubmission_file\', \'assignfeedback_file\', \'mod_label\', \'mod_assign\', \'mod_wiki\')
	  ) files
	  order by files.courseid asc, cmid asc', [$cid, $cid]);

	$csvfilename = join_path(OUTPUT_PATH, sprintf(OUTPUT_CSV_FILENAME, $cid));
	$fpcsv = fopen($csvfilename, 'w');
	if (!$fpcsv) {
		printf("Could not write to %s.", $csvfilename);
		continue;
	}

	$listfilename = join_path(OUTPUT_PATH, sprintf(OUTPUT_LIST_FILENAME, $cid));
	$fplist = fopen($listfilename, 'w');
	if (!$fplist) {
		printf("Could not write to %s.", $listfilename);
		fclose($fpcsv); // clean up the other.
		continue;
	}

	fwrite($fpcsv, OUTPUT_CSV_HEADER);

	foreach ($files as $file) {
		// skip invisible files (TODO: this is debatable!)
		if (!$file->visible) {
			continue;
		}

		if ($file->cmid == 0) {
			$name = "Legacy";
		} else {
			list($course, $cm) = get_course_and_cm_from_cmid($file->cmid, '', $currentcourse, -1);  // "userid -1 avoids user-dependent calculation - we are only interested in names, so whatevs.
			$name = $cm->name;
		}

		fwrite($fpcsv, 
			sprintf("%s, %s, %s, [%s] %s, %s\n",
				$file->contenthash, // document_id,
				csvsanitize($file->filename), // filename,
				csvsanitize($name), // folder_name pt 1,
				csvsanitize($file->filepathinmod), // folder_name pt 2, 
				csvsanitize($file->component), // folder_description, 
				csvsanitize("{$file->cmid} [".pathinfo($file->filename, PATHINFO_EXTENSION)."]")  //description // TODO: ??? (<f.filename>? filetype by extension? <cm.visible>? <cmid>?) 

				 )
			);
		$h = $file->contenthash;
		fwrite($fplist,
			sprintf("%s/%s/%s\n",
				$h[0].$h[1],
				$h[2].$h[3],
				$h)
			);
	}

	fclose($fpcsv);
	fclose($fplist);
	
	// (TODO: update $cid as last successful course)
}
function join_path($path, $file) {
	return join('/', [
		rtrim($path, '/'),
		ltrim($file, '/')
		]);
}

function csvsanitize($str) {
	return str_replace(',', '_', $str);
}