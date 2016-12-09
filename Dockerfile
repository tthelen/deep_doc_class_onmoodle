FROM ubuntu:xenial
RUN apt-get update && \
	apt-get install -qy python3 python3-pip
RUN apt-get install -qy tesseract-ocr ghostscript poppler-utils
RUN apt-get install -qy libhdf5-dev python3-dev
ADD ./deep_doc_class /deep_doc_class
WORKDIR /deep_doc_class
RUN pip3 install -r requirements.txt
RUN pip3 install .
