# DeepDocClass on Moodle

This repository provides a Dockerfile that is used to execute deep_doc_class

Preparation (collect files on Moodle)
```bash
$ cd MOODLEDIR
$ php admin/cli/export_deep_doc_class.php
$ cd MOODLEDATA/filedir
$ mkdir tmp
$ cp MOODLEDIR/course_*_files.txt tmp
$ cat tmp/course_*_files.txt | xargs -I % cp % tmp
$ mv tmp /PATH/TO/DATADIR
```

Execution of DeepDocClass
```bash
$ docker build -t dagefoerde/odrec_deep_doc_class .
$ docker run -it  -v /PATH/TO/DATADIR:/tmp/data dagefoerde/odrec_deep_doc_class:latest /bin/bash 
```

Many, many thanks to the people at virtUOS for developing a document classifier!
More information about DeepDocClass: 
https://www.virtuos.uni-osnabrueck.de/forschung/projekte/deepdocclass_erkennung_publizierter_texte.html
