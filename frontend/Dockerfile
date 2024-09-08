ARG NODEJS_VERSION=22

FROM node:${NODEJS_VERSION}

WORKDIR /src

COPY package*.json .

RUN npm ci

COPY . .

RUN npm run build

CMD ["npm", "run", "preview"]
