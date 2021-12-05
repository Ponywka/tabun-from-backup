FROM node:17-alpine3.14
LABEL org.opencontainers.image.source='https://github.com/everypony/tabun'
RUN yarn global add gettext.js
RUN apk add --no-cache gettext
RUN wget -O - https://github.com/cortesi/modd/releases/download/v0.8/modd-0.8-linux64.tgz | tar --strip-components=1 -xzf - -C /usr/bin/
ADD ./modd.conf .
VOLUME /source
VOLUME /destination
CMD ["modd"]
