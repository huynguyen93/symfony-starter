.PHONY: up
up:
	docker-compose up --remove-orphans

.PHONY: main-deploy
main-deploy:
	cd deploy && ./vendor/bin/dep deploy

.PHONY: cc
cc:
	./bin/console c:c && ./bin/console c:w

.PHONY: fixtures
fixtures:
	./bin/console doctrine:fixtures:load --no-interaction

.PHONY: reset-db
reset-db:
	./bin/console doctrine:database:drop --if-exists --force
	./bin/console doctrine:database:create
	./bin/console doctrine:schema:create
	make fixtures
	# ./bin/console messenger:setup-transports

.PHONY: testing-db
testing-db:
	./bin/console c:c --env=test && ./bin/console c:w --env=test
	./bin/console doctrine:database:drop --if-exists --force --env=test
	./bin/console doctrine:database:create --env=test
	./bin/console doctrine:schema:create --env=test

.PHONY: testing-cc
testing-cc:
	./bin/console c:c --env=test && ./bin/console c:w --env=test

front-install:
	cd apps/main/frontend && npm install

front-start:
	cd apps/main/frontend && npm start

front-build:
	cd apps/main/frontend && npm build
