# DeepDocClass on Moodle

This repository provides a Dockerfile that is used to execute DeepDocClass, together with some ideas on how to use it with Moodle. Many, many thanks to the people at virtUOS for developing a document classifier!
More information about DeepDocClass: 
https://www.virtuos.uni-osnabrueck.de/forschung/projekte/deepdocclass_erkennung_publizierter_texte.html


## Prerequisites

* Install Docker on your machine
* Clone repository
* Execute `git submodule update --init` inside repository
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
```bash
$ docker build -t dagefoerde/odrec_deep_doc_class .
$ docker run -it  -v /PATH/TO/DATADIR:/tmp/data dagefoerde/odrec_deep_doc_class:latest /bin/bash 
$ cd src
$ python3 classify_pdf.py -m /tmp/data/course_2.csv -d /tmp/data -c 2
$ cp -R ../results/ /tmp/data/results
```

Now analyse the results outside the docker container at `/PATH/TO/DATADIR/results`, e.g. have a look at it  using `joinresults.r` in GNU R. Note: DeepDocClass will classify *all* files present in `/PATH/TO/DATADIR`, although we create per-course metadata CSVs. *TODO*.


