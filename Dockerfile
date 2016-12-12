FROM ubuntu:xenial
RUN apt-get update && \
	apt-get install -qy python3 python3-pip
RUN apt-get install -qy tesseract-ocr ghostscript poppler-utils
RUN apt-get install -qy libhdf5-dev python3-dev
RUN apt-get install -qy python3-numpy python3-scipy
RUN pip3 install --upgrade pip
RUN locale-gen de_DE.UTF-8 && update-locale LANG=de_DE.UTF-8
ENV LANG de_DE.UTF-8  
ENV LANGUAGE de_DE:de  
ENV LC_ALL de_DE.UTF-8  
ADD ./deep_doc_class /deep_doc_class
WORKDIR /deep_doc_class
RUN pip3 install -r requirements.txt
