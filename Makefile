DOCKER_COMPOSE_BIN = docker-compose
DOCKER_COMPOSE_DIR = docker
PROJECT_NAME = convelio-test
DC_RUN = $(DOCKER_COMPOSE_BIN) run --rm $(PROJECT_NAME)

##
## Project actions
## -----------------------
##

example: ## run the example provided by Convelio
example:
	@$(MAKE) --no-print-directory app "php example/example.php"

.PHONY: example

##
## Docker-specific actions
## -----------------------
##

dc: ## docker-compose shortcut
dc:
	@(cd ${DOCKER_COMPOSE_DIR} && $(DOCKER_COMPOSE_BIN) $(filter-out $@,$(MAKECMDGOALS)))

app: ## docker-compose exec on the application container
app:
	@(cd ${DOCKER_COMPOSE_DIR} && $(DC_RUN) $(filter-out $@,$(MAKECMDGOALS)))

##
## Makefile base
## -------------
##

.DEFAULT_GOAL := help

help: ## Print this help
help:
	@grep -hE '(^[a-zA-Z._-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
	| grep -v "###>" \
	| grep -v "###<" \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

# @see https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line/6273809#6273809
%: # hack to make arguments with targets - use with $(filter-out $@,$(MAKECMDGOALS))
	@:

.PHONY: help %
