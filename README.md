# DeepDocClass on Moodle


## Prerequisites

* Install Docker on your machine
* Clone repository
* Copy `export_deep_doc_class.php` into `MOODLEDIR/admin/cli/`

## Preparation (collect files on Moodle)
```bash
$ cd MOODLEDIR
$ php admin/cli/export_deep_doc_class.php
$ cd MOODLEDATA/filedir
$ mkdir -p tmp/files
$ cp MOODLEDIR/course_*_files.txt tmp
$ cp MOODLEDIR/course_*.csv tmp
$ cat tmp/course_*_files.txt | xargs -I % cp % tmp/files
$ find tmp/files -type f -exec mv '{}' '{}'.pdf \;
$ mv tmp/course_*_files.txt tmp/files
$ mv tmp/course_*.csv tmp/files
$ mv tmp/files /PATH/TO/DATADIR
```

## Execution of DeepDocClass

Follow directions on https://github.com/luniki/docker_deep_doc_class
