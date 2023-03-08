FROM node:18-alpine

WORKDIR /app

COPY package.json pnpm-lock.yaml ./

RUN npm install -g pnpm && pnpm install

COPY . .

RUN node ace build --production --ignore-ts-errors

COPY .env.production build/.env

WORKDIR /build

RUN pnpm install

CMD ["node", "build/server.js"]
