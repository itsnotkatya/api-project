PHP := php
CONSOLE := $(PHP) bin/console
THIS_FILE := $(lastword $(MAKEFILE_LIST))

-include .env

show-commands:
	$(CONSOLE)

about:
	$(CONSOLE) about

install: c-inst
	cp .env .env.local
	$(info Configure the parameters in the .env.local file)
	$(MAKE) -f $(THIS_FILE) dev

c-inst:
	composer install

admin:
	$(CONSOLE) fos:user:create --super-admin

default-admin:
	$(CONSOLE) fos:user:create admin admin@admin.com admin --super-admin

test:
	bin/phpunit -c phpunit.xml.dist src --process-isolation

test-failing:
	bin/phpunit -c app src --group failing --process-isolation

test-prod: clear-cache-prod

dev: set-permissions cache-dev cache-dev
	$(MAKE) -f $(THIS_FILE) set-permissions

prod: inc-assets-version set-permissions cache-prod dump
	$(MAKE) -f $(THIS_FILE) set-permissions

set-permissions:
	sudo $(MAKE) -f $(THIS_FILE) set-permissions-wo-sudo

rm-cache:
	sudo rm -rf var/cache/de* && sudo rm -rf var/cache/prod

clear-cache-dev: set-permissions cache-dev
	$(MAKE) -f $(THIS_FILE) set-permissions

clear-cache-prod: set-permissions cache-prod
	$(MAKE) -f $(THIS_FILE) set-permissions

cache-dev:
	$(CONSOLE) cache:clear --env=dev

cache-prod:
	$(CONSOLE) cache:clear --env=prod

update-db:
	$(CONSOLE) doctrine:schema:update --force --dump-sql

reload-db: check-reload-db
	$(CONSOLE) doctrine:database:drop --force
	$(MAKE) -f $(THIS_FILE) create-db

reload-db-test: check-reload-db
	$(CONSOLE) doctrine:database:drop --force --env=test
	$(CONSOLE) doctrine:database:create --env=test
	$(CONSOLE) doctrine:schema:create --env=test

create-db:
	$(CONSOLE) doctrine:database:create

create-schema:
	$(CONSOLE) doctrine:schema:create

check-reload-db:
	@echo "\033[92mAre you sure that you want to reload the database?\033[0m [y/N] " && read ans && [ $${ans:-N} = y ]

dump:
	composer dump-autoload --optimize
	composer dump-env prod

install-web-assets:
	$(CONSOLE) assets:install public --symlink

migrate:
	$(CONSOLE) doctrine:migrations:migrate latest

migrate-next:
	$(CONSOLE) doctrine:migrations:migrate next

migrate-prev:
	$(CONSOLE) doctrine:migrations:migrate prev

migrations-diff:
	$(CONSOLE) doctrine:migrations:diff

migrations-status:
	$(CONSOLE) doctrine:migrations:list

inc-assets-version:
	ASSETS_NUM=$$(cat .env.local | grep ASSETS_VERSION= | grep -Eo '[0-9]{1,4}'); \
	sed -i "s/ASSETS_VERSION=$$ASSETS_NUM/ASSETS_VERSION=$$((ASSETS_NUM+1))/" .env.local

dev-wo-permissions: cache-dev

set-permissions-wo-sudo:
	chmod -R ug+rw .
	chmod -R a+rws var/cache var/log

router:
	$(CONSOLE) debug:router

configs:
	$(CONSOLE) debug:config
