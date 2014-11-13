FROM ubuntu:14.04

RUN apt-get update && apt-get install -y python-dev python python-pip libfontconfig supervisor

ADD supervisord.conf /etc/supervisor/conf.d/portal.conf

EXPOSE 8889
EXPOSE 9001

CMD ["supervisord"]

ADD requirements.txt /root/portal/requirements.txt

RUN pip install -r /root/portal/requirements.txt

ADD . /root/portal

