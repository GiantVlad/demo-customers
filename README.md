DEMO contacts API.
=====

- copy api/.env to api/.env.local
- copy api/phpunit.xml.dist to api/phpunit.xml
- run docker-compose up -d to start docker containers

To populate the DB with fake data run:

```bash
docker-compose exec php bin/console hautelook:fixtures:load -n
```
Open the App: https://localhost/api/doc
<img src="https://i.ibb.co/8xdZqY0/Screenshot-from-2022-08-16-07-07-58.png" alt="Stations">

You'll need to add a security exception in your browser to accept the self-signed TLS certificate that has been
generated for this container when installing the framework.

Code quality:
```bash
docker-compose exec php vendor/bin/phpstan analyse --no-progress -c phpstan.neon ./
```

Create DB for tests once:
```bash
docker-compose exec php bin/console doctrine:database:create --env=test

docker-compose exec php bin/console doctrine:migrations:migrate -n --env=test
```

Run tests
```bash
docker-compose exec php bin/phpunit
```
