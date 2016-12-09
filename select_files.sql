-- first: get list of courses and their respective teachers + emails. then, for each course, do the following:

-- files
select * from (
--course-level files ("legacy course files")
select 
c.id as courseid, c.fullname, f.contenthash, f.filename, 1 as "visible", 'legacy' as "component", 0 as "cmid", '/' as "filepathinmod"
from mdl_files f
join mdl_context ctx on ctx.id = f.contextid
join mdl_course c on ctx.instanceid = c.id
where ctx.contextlevel = 50 -- courses
  and f.filename <> '.'
  and c.id = 2
--;
UNION
--module-level files (e.g. "new" files)
select -- f.component, count(*)
c.id as courseid, c.fullname, f.contenthash, f.filename, cm.visible, f.component, cm.id as "cmid", f.filepath as "filepathinmod"
from mdl_files f
join mdl_context ctx on ctx.id = f.contextid
join mdl_course_modules cm on ctx.instanceid = cm.id
join mdl_course c on c.id = cm.course
where ctx.contextlevel = 70 -- module instances
  and f.filename <> '.'
  and c.id = 2
  and f.component not in ('mod_forum', 'mod_data', 'question', 'assignsubmission_file', 'assignfeedback_file', 'mod_label', 'mod_assign', 'mod_wiki')
  ) files
  order by files.courseid asc, cmid asc
;

-- now use list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'forum'); from lib/modinfolib.php to obtain coursemodule's name ($cm->name)
-- use name to augment "filepathinmod"
-- construct a CSV for deep_doc_class 
