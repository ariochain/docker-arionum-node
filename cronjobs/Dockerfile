FROM ubuntu:bionic

RUN export DEBIAN_FRONTEND=noninteractive && apt-get update && apt-get -y install cron curl && rm -rf /var/lib/apt/lists/*

ADD crontab /etc/cron.d/crontab
COPY sanity /etc/periodic/sanity
RUN chmod 0644 /etc/cron.d/crontab && mkdir -p /etc/periodic/ && chmod 0755 /etc/periodic/sanity

CMD ["cron","-f"]
