FROM debian:wheezy

RUN sed -i -e 's/http.debian.net/ftp.us.debian.org/g' /etc/apt/sources.list && \
    apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y python-dev python python-pip libfontconfig && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 80

RUN mkdir /root/portal

WORKDIR /root/portal
CMD ["gunicorn","-b","0.0.0.0:80","run"]

ADD requirements.txt /root/portal/requirements.txt

RUN pip install -r /root/portal/requirements.txt

ADD ./static/pdf /root/portal/static/pdf
ADD ./static /root/portal/static
ADD ./run.py /root/portal/run.py
ADD ./json /root/portal/json
ADD ./templates /root/portal/templates
ADD ./handlers /root/portal/handlers

