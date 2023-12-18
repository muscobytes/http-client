#!make

TAG=muscobytes/php-8.1-cli
DOCKER=docker run -ti \
	--volume "$(shell pwd):/var/www/html" \
	$(TAG)

.PHONY: help
help:      ## Shows this help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: build
build:
	curl --silent "https://gist.githubusercontent.com/postfriday/e405590799994018c8bef7705436eb4f/raw/bdddc4b26a920fb6663dd80648ac0b7ac60d020b/Dockerfile%2520(php:8.1-cli)" | docker build --progress plain --tag $(TAG) --file - .

.PHONY: shell
shell:
	${DOCKER} sh

.PHONY: test
test:
	${DOCKER} ./vendor/bin/phpunit tests

.PHONY: install
install:
	${DOCKER} composer install

.PHONY: update
update:
	${DOCKER} composer update
