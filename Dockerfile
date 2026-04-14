FROM dunglas/frankenphp:latest

RUN docker-php-ext-install mysqli

WORKDIR /app
COPY . .

EXPOSE 80
