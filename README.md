# DeepDocClass on Moodle

This repository provides a Dockerfile that is used to execute deep_doc_class

Preparation (collect files on Moodle)
```bash
$ cd MOODLEDIR
$ php admin/cli/export_deep_doc_class.php
$ cd MOODLEDATA/filedir
$ mkdir tmp
$ cat tmp/course_*_files.txt | xargs -I % cp % tmp
$ find . -type f -exec mv '{}' '{}'.pdf \;
$ cp MOODLEDIR/course_*_files.txt tmp
$ cp MOODLEDIR/course_*.csv tmp
$ mv tmp /PATH/TO/DATADIR
```

Execution of DeepDocClass
```bash
$ docker build -t dagefoerde/odrec_deep_doc_class .
$ docker run -it  -v /PATH/TO/DATADIR:/tmp/data dagefoerde/odrec_deep_doc_class:latest /bin/bash 
$ cd src
$ python3 classify_pdf.py -m /tmp/data/course_2.csv -d /tmp/data 
```

Many, many thanks to the people at virtUOS for developing a document classifier!
More information about DeepDocClass: 
https://www.virtuos.uni-osnabrueck.de/forschung/projekte/deepdocclass_erkennung_publizierter_texte.html
