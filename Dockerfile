FROM debian:wheezy

RUN sed -i -e 's/http.debian.net/ftp.us.debian.org/g' /etc/apt/sources.list && \
    sed -i -e 's/httpredir/ftp.us/g' /etc/apt/sources.list && \
    apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y python-dev python python-pip libfontconfig && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 80

RUN mkdir /opt/portal

WORKDIR /opt/portal
CMD ["gunicorn","-b","0.0.0.0:80","run"]

ADD requirements.txt /opt/portal/requirements.txt

RUN pip install --index-url https://pypi.python.org/simple/ --upgrade pip
RUN pip install -r /opt/portal/requirements.txt

ADD ./static/pdf /opt/portal/static/pdf
ADD ./static /opt/portal/static
ADD ./run.py /opt/portal/run.py
ADD ./json /opt/portal/json
ADD ./templates /opt/portal/templates
ADD ./handlers /opt/portal/handlers

