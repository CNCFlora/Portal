FROM ubuntu:14.04

RUN apt-get update && apt-get install -y python-dev python python-pip libfontconfig supervisor

ADD supervisord.conf /etc/supervisor/conf.d/portal.conf

EXPOSE 8000
EXPOSE 9001

CMD ["supervisord"]

ADD requirements.txt /root/portal/requirements.txt

RUN pip install -r /root/portal/requirements.txt

ADD ./static/pdf /root/portal/static/pdf

ADD ./static /root/portal/static

ADD . /root/portal

