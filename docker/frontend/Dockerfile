FROM node:16-alpine3.14
LABEL org.opencontainers.image.source='https://github.com/everypony/tabun'
WORKDIR /build
COPY package.json .
COPY yarn.lock .
RUN yarn install
# Move this to frontend/ directory
COPY webpack.config.babel.js .
COPY .babelrc .
